<?php

namespace App\Rules\Syllabus;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class MaterialsExist implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    protected $errorValue = '';

    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $links = $value['new']['link'] ?? [];

        foreach ($links as $link)
        {
            if(isset($value['new']['link'])) {
                if (!@fopen($link, "r")) {
                    $this->errorValue = $link;
                    return false;
                }
            }
        }

        $links = $value['update']['link'] ?? [];

        foreach ($links as $link)
        {
            if(isset($value['new']['link'])) {
                if (!@fopen($link, "r")) {
                    $this->errorValue = $link;
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid material resource link ' . $this->errorValue;
    }
}
