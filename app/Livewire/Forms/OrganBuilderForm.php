<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class OrganBuilderForm extends Form
{
    public $isWorkshop;
    
    #[Validate('nullable|regex:/^[[:upper:]]/', message: 'Název dílny musí začínat velkým písmenem.')]
    public $workshopName;
    
    #[Validate('nullable|regex:/^[[:upper:]]/', message: 'Jméno varhanáře musí začínat velkým písmenem.')]
    public $firstName;
    #[Validate('nullable|regex:/^[[:upper:]]/', message: 'Příjmení varhanáře musí začínat velkým písmenem.')]
    public $lastName;
    
    public $placeOfBirth;
    public $placeOfDeath;
    public $categories = [];
    public $activePeriod;
    public $activeFromYear;
    public $municipality;
    public $latitude;
    public $longitude;
    public $regionId;
    public $importance;
    public $description;
    
}
