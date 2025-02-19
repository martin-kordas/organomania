<?php

namespace App\Livewire\Forms;

use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Livewire\Attributes\Session;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Helpers;
use App\Models\LiturgicalDay;

class WorshipSongForm extends Form
{
    #[Validate('date', message: 'Hodnota musí být platné datum.')]
    #[Validate('required', message: 'Datum musí být vyplněno.')]
    public $date;
    
    #[Session]
    #[Validate('nullable')]
    #[Validate('date_format:H:i', message: 'Hodnota musí obsahovat platný čas.')]
    public $time = null;
    
    #[Validate([
        'songIds' => 'array|min:1',
    ], message: [
        'songIds.min' => 'Uveďte alespoň 1 píseň.',
    ])]
    public $songIds = [];
    
    public function boot()
    {
        $this->withValidator(function (Validator $validator) {
            $validator->after(function (Validator $validator) {
                $date = new Carbon($this->date);
                $minDate = LiturgicalDay::getMinDate();
                if ($date < $minDate) {
                    $minDateFormat = Helpers::formatDate($minDate);
                    $validator->errors()->add('date', __('Datum nesmí být dřívější než :minDate.', ['minDate' => $minDateFormat]));
                }
                else {
                    $maxDate = LiturgicalDay::getMaxDate();
                    if ($date > $maxDate) {
                        $maxDateFormat = Helpers::formatDate($maxDate);
                        $validator->errors()->add('date', __('Datum nesmí být pozdější než :maxDate.', ['maxDate' => $maxDateFormat]));
                    }
                }
            });
            
            if ($validator->fails()) throw new ValidationException($validator);
        });
    }
    
}
