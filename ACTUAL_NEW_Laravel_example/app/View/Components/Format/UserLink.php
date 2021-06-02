<?php

namespace App\View\Components\Format;

use App\User;
use Illuminate\View\Component;

class UserLink extends Component
{
    public $value;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(User $value)
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
        return view('components.format.user-link');
    }
}
