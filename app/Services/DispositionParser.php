<?php

namespace App\Services;

use App\Models\Disposition;
use App\Models\Keyboard;
use App\Models\DispositionRegister;
use App\Models\RegisterName;
use App\Enums\DispositionLanguage;
use App\Enums\Pitch;
use Illuminate\Support\Facades\DB;

class DispositionParser
{
    
    private $text;

    public function __construct(
        string $text,
        private DispositionLanguage $language,
        private $keyboardNumbering = true
    ) {
        $this->text = str($text)
            ->trim()
            ->replace("\r\n", "\n")
            ->replaceMatches("/[\n]{2,}/", "\n\n")
            ->replaceMatches("/[ ]{2,}/", ' ');
    }
    
    public function parse(): Disposition
    {
        $dispositionModel = new Disposition;
        $this->parseInto($dispositionModel);
        return $dispositionModel;
    }
    
    public function parseInto(Disposition $dispositionModel): void
    {
        $keyboardOrder = 1;
        foreach ($this->parseKeyboards() as $keyboard) {
            $keyboardModel = new Keyboard([
                'name' => $keyboard['name'],
                'pedal' => $keyboard['pedal'],
                'order' => $keyboardOrder++,
            ]);
            $registerOrder = 1;
            foreach ($keyboard['registers'] as $register) {
                $data = [
                    'multiplier' => $register['multiplier'] ?? null,
                    'pitch' => isset($register['pitch']) ? $this->getPitch($register['pitch']) : null,
                    'coupler' => $register['coupler'],
                    'order' => $registerOrder++,
                ];
                
                $registerNameModel = $this->getRegisterNameModel($register['name']);
                if ($registerNameModel) $data['register_name_id'] = $registerNameModel->id;
                else $data['name'] = $register['name'];
                
                $registerModel = new DispositionRegister($data);
                $keyboardModel->dispositionRegisters->push($registerModel);
            }
            $dispositionModel->keyboards->push($keyboardModel);
        }
    }
    
    public function saveInto(Disposition $disposition)
    {
        $this->parseInto($disposition);
        $this->save($disposition);
    }
    
    private function save(Disposition $disposition)
    {
        if (!$disposition->exists) throw new \LogicException();
        
        foreach ($disposition->keyboards as $keyboard) {
            $keyboard->disposition_id = $disposition->id;
            $keyboard->save();
            
            $keyboard->dispositionRegisters()->saveMany($keyboard->dispositionRegisters);
        }
        return $this;
    }
    
    private function getRegisterNameModel($registerName)
    {
        $table = (new RegisterName())->getTable();
        return DB::table($table)
            // accent-sensitive srovnání
            ->whereRaw(
                'LOWER(name) COLLATE utf8mb4_bin = LOWER(CONVERT(? USING utf8mb4)) COLLATE utf8mb4_bin',
                $registerName
            )
            ->first();
    }
    
    private function getPitch($label)
    {
        foreach (Pitch::cases() as $pitch) {
            if ($pitch->getLabel($this->language) === $label) return $pitch;
        }
        echo("Poloha nebyla nalezena: $label\n");
    }
    
    private function parseKeyboards()
    {
        foreach (explode("\n\n", $this->text) as $paragraph) {
            $lines = explode("\n", $paragraph);
            $name = $this->parseKeyboardName(array_shift($lines));
            $pedal = in_array(
                mb_strtolower($name['name']),
                ['pedál', 'pedal', 'pédale', 'P']
            );
            
            $keyboard = [
                'name' => $name['name'],
                'pedal' => $pedal,
                'registers' => array_map($this->parseRegister(...), $lines),
            ];
            yield $keyboard;
        }
    }
    
    private function parseKeyboardName($line)
    {
        if ($this->keyboardNumbering) {
            $matches = [];
            if (preg_match('/^([IV]{1,3}\. )?(.*)$/', $line, $matches)) {
                return ['number' => $matches[1], 'name' => $matches[2]];
            }
        }
        return ['name' => $line];
    }
    
    private function isRegisterNameCoupler($name, $strict = false)
    {
        if (!$strict) {
            foreach (['spojka', 'koppel'] as $substring) {
                if (mb_strpos(mb_strtolower($name), $substring) !== false) {
                    return true;
                }
            }
        }
        
        if (mb_strpos($name, '/') === false) return false;
        
        $parts = explode('/', $name);
        if (count($parts) !== 2) return false;
        
        foreach ($parts as $part) {
            if ($part !== 'P' && !preg_match('/^[IV]{1,3}$/', $part)) {
                return false;
            }
        }
        
        return true;
    }
    
    private function parseRegister($line)
    {
        $register = [];
        $register['coupler'] = true;
        
        // [5.] Píšťala šumivá 4-5x 2 2/3'
        $parts = explode(' ', $line);
        if (preg_match('/^[0-9]\.$/', $parts[0])) {
            $register['number'] = array_shift($parts);
        }
        
        // 5. Píšťala šumivá 4-5x 2 [2/3']
        if (count($parts) >= 2) {
            $last = end($parts);
            if (str_ends_with($last, "'")) {
                $register['pitch'] = array_pop($parts);
                $register['coupler'] = false;
                
                // 5. Píšťala šumivá 4-5x [2] 2/3'
                if (count($parts) >= 2) {
                    $last = end($parts);
                    if (is_numeric($last)) {
                        $register['pitch'] = sprintf(
                            "%s {$register['pitch']}",
                            array_pop($parts)
                        );
                    }
                }
            }
        }
        
        // 5. Píšťala šumivá [4-5x] 2 2/3'
        if (count($parts) >= 2) {
            $last = end($parts);
            if (str_ends_with($last, 'x')) {
                $register['coupler'] = false;
                $multiplier = array_pop($parts);
                $register['multiplier'] = mb_substr($multiplier, 0, -1);
            }
        }
        
        // 5. [Píšťala šumivá] 4-5x 2 2/3'
        $register['name'] = implode(' ', $parts);
        
        if (!$register['coupler'] && $this->isRegisterNameCoupler($register['name'])) {
            $register['coupler'] = true;
        }
        if ($this->isRegisterNameCoupler($register['name'], strict: true)) {
            $register['pitch'] ??= "8'";
        }
        
        return $register;
    }
    
}
