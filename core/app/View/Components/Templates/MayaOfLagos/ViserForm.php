<?php

namespace App\View\Components\Templates\MayaOfLagos;

use App\Models\Form;
use Illuminate\View\Component;

class ViserForm extends Component
{
    public $identifier;
    public $identifierValue;
    public $form;
    public $formData;

    public function __construct($identifier, $identifierValue)
    {
        $this->identifier = $identifier;
        $this->identifierValue = $identifierValue;
        $this->form = Form::where($this->identifier, $this->identifierValue)->first();
        $this->formData = @$this->form->form_data ?? [];
    }

    public function render()
    {
        return view('templates.MayaOfLagos.components.viser-form');
    }
}