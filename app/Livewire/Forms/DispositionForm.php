<?php

namespace App\Livewire\Forms;

use Illuminate\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\Attributes\Locked;
use App\Helpers;
use App\Models\Disposition;
use App\Models\DispositionRegister;
use App\Models\Keyboard;
use App\Enums\DispositionLanguage;

class DispositionForm extends Form
{
    
    public string $name;
    public ?int $organId;
    public ?string $appendix;
    public ?string $description;
    public bool $numbering = true;
    public bool $keyboardNumbering = true;
    public string $language = DispositionLanguage::Czech->value;     // TODO
    
    public array $keyboards = [];
    public array $registers = [];
    
    public Disposition $disposition;
    
    private $public;

    // TODO: zřejmě se volá dvakrát (automaticky s DI, pak ručně z organ-edit)
    public function boot($public = false)
    {
        $this->public = $public;
    }
    
    public function setDisposition(Disposition $disposition)
    {
        $this->disposition = $disposition;
        
        $data = Helpers::arrayKeysCamel($this->disposition->toArray());
        if (isset($data['language'])) {
            $data['language'] = $data['language']->value;
        }
        $data['keyboards'] = $this->getKeyboards($this->disposition);
        $data['registers'] = $this->getDispositionRegisters($this->disposition);
        
        $this->fill($data);
    }
    
    public function isDispositionPublic()
    {
        if (!$this->disposition->exists) return $this->public;
        else return $this->disposition->isPublic();
    }
    
    public function getLanguage()
    {
        return DispositionLanguage::from($this->language);
    }
    
    private function getKeyboards(Disposition $disposition)
    {
        return $disposition->keyboards->map(function (Keyboard $keyboard) {
            $data = $keyboard->only(['id', 'pedal', 'name']);
            return Helpers::arrayKeysCamel($data);
        })->all();
    }
    
    private function getDispositionRegisters(Disposition $disposition)
    {
        $registers = [];
        foreach ($disposition->keyboards as $keyboard) {
            $keyboardRegisters = [];
            foreach ($keyboard->dispositionRegisters as $register) {
                $data = $register->only(['id', 'register_name_id', 'name', 'coupler', 'multiplier', 'pitch_id']);
                $data['register_name_id'] ??= $data['name'];
                unset($data['name']);
                $keyboardRegisters[] = Helpers::arrayKeysCamel($data);
            }
            $registers[] = $keyboardRegisters;
        }
        return $registers;
    }
    
    private function getEntityData($entity)
    {
        $data = collect($entity)->except(['id'])->toArray();
        return Helpers::arrayKeysSnake($data);
    }
    
    private function getDispositionRegisterData($register)
    {
        if ($register['custom']) {
            $register['name'] = $register['registerNameId'];
            unset($register["registerNameId"]);
        }
        unset($register['custom']);
        if ($register['multiplier'] === '') $register['multiplier'] = null;
        if ($register['pitchId'] === '') $register['pitchId'] = null;
        return $this->getEntityData($register);
    }
    
    private function fillEntitiesWithOrder(array $entities)
    {
        $order = 1;
        return collect($entities)->map(function ($entity) use (&$order) {
            return $entity + ['order' => $order++];
        })->toArray();
    }
    
    public function validateRegister($keyboardIndex, $registerIndex)
    {
        $register = $this->registers[$keyboardIndex][$registerIndex] ?? throw new \LogicException;
        $keyboard = $this->keyboards[$keyboardIndex] ?? throw new \LogicException;

        $isSameRegister = function ($register1, $register2) {
            unset($register1['id'], $register2['id']);
            return $register1 == $register2;
        };
        
        if ($register['registerNameId'] == '') {
            $registerText = $register['coupler'] ? 'spojky' : 'rejstříku';
            throw ValidationException::withMessages([
                'register' => __("Název $registerText musí být uveden."),
            ]);
        }
        
        // TODO: nekontroluje se, jestli není název nějakého custom rejstříku stejný jako název rejstříku vybraného ze seznamu
        foreach ($this->registers[$keyboardIndex] as $registerIndex1 => $register1) {
            if ($registerIndex1 !== $registerIndex && $isSameRegister($register1, $register)) {
                $inManualPedal = $keyboard['pedal'] ? 'v pedálu' : 'v tomto manuálu';
                throw ValidationException::withMessages([
                    'register' => __("Rejstřík se stejnými parametry je již $inManualPedal vložen.")
                ]);
            }
        }
        $this->resetValidation();
    }
    
    public function save()
    {
        //$this->validate();
        $data = Helpers::arrayKeysSnake($this->except(['keyboards', 'registers', 'disposition']));
        $data['language'] = DispositionLanguage::from($data['language']);
        
        DB::transaction(function () use ($data) {
            //$exists = $this->disposition->exists;
            $this->disposition->fill($data);
            if (!$this->isDispositionPublic()) $this->disposition->user_id = Auth::id();
            $this->disposition->save();

            $this->saveKeyboards();
            $this->disposition->refresh();      // ať je kolekce keyboards aktuální
            foreach ($this->disposition->keyboards as $keyboard) {
                $this->saveRegisters($keyboard);
            }
        });
    }
    
    private function saveKeyboards()
    {
        $keyboards = $this->fillEntitiesWithOrder($this->keyboards);
        
        // nutné načíst ještě před uložením $newKeyboardModels!
        $keyboardModels = $this->disposition->keyboards;
        
        $newKeyboardModels = collect($keyboards)
            ->filter(
                fn($keyboard) => !isset($keyboard['id'])
            )
            ->map(
                fn($keyboard) => new Keyboard(
                    $this->getEntityData($keyboard)
                )
            );
        $this->disposition->keyboards()->saveMany($newKeyboardModels);

        foreach ($keyboardModels as $keyboardModel) {
            $keyboard = collect($keyboards)->firstWhere('id', $keyboardModel->id);
            if ($keyboard) {
                $keyboardModel->fill(
                    $this->getEntityData($keyboard)
                );
            }
            else $keyboardModel->delete();
        }
        $this->disposition->push();   // uloží změny v existujících klaviaturách
    }
    
    private function saveRegisters(Keyboard $keyboard)
    {
        $keyboardIndex = $keyboard->order - 1;
        $registers = $this->registers[$keyboardIndex] ?? throw new \LogicException;
        $registers = $this->fillEntitiesWithOrder($registers);
        
        // nutné načíst ještě před uložením $newRegisterModels!
        $registerModels = $keyboard->dispositionRegisters;
        
        $newRegisterModels = collect($registers)
            ->filter(
                fn($register) => !isset($register['id'])
            )
            ->map(
                fn($register) => new DispositionRegister(
                    $this->getDispositionRegisterData($register)
                )
            );
        $keyboard->dispositionRegisters()->saveMany($newRegisterModels);

        foreach ($registerModels as $registerModel) {
            $register = collect($registers)->firstWhere('id', $registerModel->id);
            if ($register) {
                $registerModel->fill(
                    $this->getDispositionRegisterData($register)
                );
            }
            else $registerModel->delete();
        }
        $keyboard->push();   // uloží změny v existujících rejstřících
    }
    
    public function delete()
    {
        if (!$this->disposition->exists) throw new \RuntimeException;
        $this->disposition->delete();
    }
    
}
