<?php

namespace App\View\Components\Format;

use App\Models\Offer;
use Illuminate\View\Component;

class OfferLink extends Component
{
    public $value;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Offer $value)
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
        return view('components.format.offer-link');
    }
}
