<?php

namespace App\Http\Requests;

use App\Models\Offer;
use App\Models\Order;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrdersPenalty extends FormRequest
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
        $partners = [];
        foreach (User::getPartners(auth()->user()) as $item) {
            array_push($partners, $item->id);
        }
        $offerIDs = [];
        foreach (Offer::getOwnOffers(auth()->user()) as $offer) {
            array_push($offerIDs, $offer->id);
        }

        return [
            'order_id' => ['required', 'string'],
            'offer_id' => ['required', Rule::in($offerIDs)],
            'datetime' => ['required', 'date'],
            'partner_id' => ['required', 'integer', Rule::in($partners)],
            'gross_amount' => ['required', 'numeric'],
            'comment' => ['nullable', 'string'],
            'type' => ['required', 'string'],
        ];
    }
}
