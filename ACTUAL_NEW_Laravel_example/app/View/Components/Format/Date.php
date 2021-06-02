<?php

namespace App\View\Components\Format;

use Illuminate\View\Component;
use Jenssegers\Date\Date as DateDate;

class Date extends Component
{
    public $value;
    public $format;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(DateDate $value, $format = 'j F Y H:i')
    {
        $this->value = $value;
        $this->format = $format;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.format.datetime');
    }
}
