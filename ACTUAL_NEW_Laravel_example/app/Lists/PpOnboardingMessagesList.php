<?php

namespace App\Lists;


class PpOnboardingMessagesList
{
    public static function getList()
    {
        return  [
            'registered' => __('lists.onboarding_messages.registered'),
            'first_login' => __('lists.onboarding_messages.first_login'),
            'step1' => __('lists.onboarding_messages.step1'),
            'first_offer_added' => __('lists.onboarding_messages.first_offer_added'),
            'not_integrated' => __('lists.onboarding_messages.not_integrated'),
            'integration_initiated' => __('lists.onboarding_messages.integration_initiated'),
            'integrated_successfully' => __('lists.onboarding_messages.integrated_successfully'),
            'first_partner_added' => __('lists.onboarding_messages.first_partner_added'),
        ];
    }

}
