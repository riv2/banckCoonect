<?php

namespace App\View\Components\Format;

use App\Models\Pp;
use Illuminate\View\Component;

class PpLink extends Component
{
    public $value;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Pp $value)
    {
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.format.pp-link');
    }
}
