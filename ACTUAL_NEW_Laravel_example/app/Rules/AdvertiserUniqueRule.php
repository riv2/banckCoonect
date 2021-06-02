<?php

namespace App\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;

class AdvertiserUniqueRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return User::query()
            ->where('role', '=', 'advertiser')
            ->where('email', '=', $value)
            ->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('register-advert.fields.email.errors.exists');
    }
}
