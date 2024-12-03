<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class RegistrationSetForm extends Form
{
    #[Validate('required', message: 'Název musí být vyplněn.')]
    public $name;
    
    public $registrations = [];
    
}
