<?php

namespace App\View\Components;

use App\Models\Form;
use Illuminate\View\Component;

class ViserForm extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $identifier;
    public $identifierValue;
    public $form;
    public $formData;

    public function __construct($identifier,$identifierValue)
    {
        $this->identifier = $identifier;
        $this->identifierValue = $identifierValue;
        $this->form = Form::where($this->identifier,$this->identifierValue)->first();
        $this->formData = @$this->form->form_data ?? [];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        // Try to detect the active template from various sources
        $activeTemplate = null;
        
        // Method 1: Check if activeTemplate is in view data
        $viewData = app('view')->getShared();
        if (isset($viewData['activeTemplate'])) {
            $activeTemplate = str_replace(['templates.', '.'], '', $viewData['activeTemplate']);
        }
        
        // Method 2: Check global view composer data
        if (!$activeTemplate) {
            $activeTemplate = config('app.template', 'basic');
        }
        
        // Check if we're in MayaOfLagos template and if template-specific component exists
        $templateSpecificView = "templates.{$activeTemplate}.components.viser-form";
        
        if ($activeTemplate === 'MayaOfLagos' && view()->exists($templateSpecificView)) {
            return view($templateSpecificView);
        }
        
        return view('components.viser-form');
    }
}
