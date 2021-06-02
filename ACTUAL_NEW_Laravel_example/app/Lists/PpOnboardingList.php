<?php

namespace App\Lists;


class PpOnboardingList
{
    public static function getList()
    {
        return  [
            'registered' => __('lists.onboarding.registered'),
            'first_login' => __('lists.onboarding.first_login'),
            'step1' => __('lists.onboarding.step1'),
            'first_offer_added' => __('lists.onboarding.first_offer_added'),
            'not_integrated' => __('lists.onboarding.not_integrated'),
            'integration_initiated' => __('lists.onboarding.integration_initiated'),
            'integrated_successfully' => __('lists.onboarding.integrated_successfully'),
            'first_partner_added' => __('lists.onboarding.first_partner_added'),
        ];
    }

}
