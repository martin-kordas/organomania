<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Rules\Municipality;

class OrganBuilderForm extends Form
{
    #[Validate('required')]
    public $isWorkshop;
    
    #[Validate('nullable')]
    #[Validate('regex:/^[[:upper:]]/u', message: 'Název dílny musí začínat velkým písmenem.')]
    public $workshopName;
    
    #[Validate('nullable')]
    #[Validate('regex:/^[[:upper:]]/u', message: 'Jméno varhanáře musí začínat velkým písmenem.')]
    public $firstName;
    #[Validate('nullable')]
    #[Validate('regex:/^[[:upper:]]/u', message: 'Příjmení varhanáře musí začínat velkým písmenem.')]
    public $lastName;
    
    #[Validate(['nullable', new Municipality], as: 'Místo narození')]
    public $placeOfBirth;
    #[Validate(['nullable', new Municipality], as: 'Místo úmrtí')]
    public $placeOfDeath;
    public $categories = [];
    public $activePeriod;
    #[Validate('required', message: 'Rok začátku působení musí být vyplněn.')]
    public $activeFromYear;
    #[Validate('required', message: 'Obec musí být vyplněna.')]
    public $municipality;
    #[Validate('required', message: 'Zeměpisná šířka musí být vyplněna.')]
    public $latitude;
    #[Validate('required', message: 'Zeměpisná délka být vyplněna.')]
    public $longitude;
    public $regionId;
    #[Validate('nullable')]
    #[Validate('url', message: 'Nebyla zadána platná URL adresa.')]
    public $web;
    public $workshopMembers;
    #[Validate('required', message: 'Význam musí být vyplněn.')]
    public $importance;
    public $perex;
    public $description;
    public $literature;
    
}
