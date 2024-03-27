<?php

namespace App\View\Components;

use Illuminate\View\Component;

class clientinforstatus extends Component
{
    public $organization_details = [];
    public $readonly = "";

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.clientinforstatus');
    }
}
