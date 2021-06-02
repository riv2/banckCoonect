<?php

namespace App\Rules;

use App\Models\Pp;
use Illuminate\Contracts\Validation\Rule;

class PpDomainUniqueRule implements Rule
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
        return Pp::query()
            ->where('tech_domain', '=', $value . '.' . config('app.domain'))
            ->orWhere('prod_domain', '=', $value)
            ->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('register-advert.fields.domain.errors.exists');
    }
}
