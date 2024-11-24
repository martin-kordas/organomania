<?php

// https://github.com/livewire/livewire/issues/823#issuecomment-821074805

namespace App\Traits;

trait ConvertEmptyStringsToNull
{
    /**
     * @var string[]
     */
    protected $convertEmptyStringsExcept = [
        //
    ];

    /**
     * @param string $name
     * @param mixed $value
     */
    public function updatedConvertEmptyStringsToNull(string $name, $value): void
    {
        if (!is_string($value) || in_array($name, $this->convertEmptyStringsExcept)) {
            return;
        }

        if (trim($value) === '') $value = '';
        $value = $value === '' ? null : $value;

        data_set($this, $name, $value);
    }
}
