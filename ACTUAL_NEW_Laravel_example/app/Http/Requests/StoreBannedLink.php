<?php

namespace App\Http\Requests;

use App\Models\Link;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBannedLink extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $linkIDs = [];
        foreach (Link::query()
                     ->where('pp_id', '=', auth()->user()->pp_id)
                     ->get() as $link) {
            array_push($linkIDs, $link->id);
        }
        $webIDs = [];
        foreach (Order::all() as $order) {
            array_push($webIDs, $order->web_id);
        }
        return [
            'link_id' => ['required','string', Rule::in($linkIDs)],
            'web_id' => ['nullable','string', Rule::in($webIDs)],
            'date_start' => ['required','date'],
            'date_end' => ['nullable', 'date', 'after:date_start'],
            'comment' => ['nullable','string'],
            'evidence' => ['nullable','string'],
        ];
    }
}
