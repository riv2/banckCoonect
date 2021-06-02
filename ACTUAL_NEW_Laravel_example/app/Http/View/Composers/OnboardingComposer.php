<?php

namespace App\Http\View\Composers;

use App\Lists\PpOnboardingMessagesList;
use Illuminate\View\View;

class OnboardingComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('onboarding_messages', $this->getOnboardingMessages());
    }

    /**
     * Получает сообщения для онбординга
     *
     * @return void
     */
    public function getOnboardingMessages()
    {
        $messages = [];
        if (auth()->check() && auth()->user()->role == 'advertiser') {
            if (!empty(PpOnboardingMessagesList::getList()[auth()->user()->pp->onboarding_status])) {
                $messages[] = PpOnboardingMessagesList::getList()[auth()->user()->pp->onboarding_status];
            }
        }

        return $messages;
    }
}
