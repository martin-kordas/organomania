<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class DispositionOcrForm extends Form
{
    
    #[Validate([
        'photos' => 'required|array|min:1|max:5',
        'photos.*' => 'image|max:4096',
    ], message: [
        'required' => 'Musí být zvolen alespoň 1 soubor.',
        'photos.max' => 'Maximálně lze zvolit 5 souborů.',
        'image' => 'Nahraný soubor musí být obrázek.',
        'photos.*.max' => 'Nahraný soubor nesmí být větší než 4 MB.',
    ])]
    public $photos = [];
    
    
}
