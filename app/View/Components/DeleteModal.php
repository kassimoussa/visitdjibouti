<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DeleteModal extends Component
{
    /**
     * Create a new component instance.
     */
    public $delmodal, $message, $delf ; 
    public function __construct($delmodal, $message, $delf )
    {
        $this->delmodal = $delmodal ;
        $this->message = $message ;
        $this->delf = $delf ;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.delete-modal');
    }
}
