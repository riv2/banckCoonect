<?php

namespace App\Http\Requests;

use App\Models\Offer;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBannedFraud extends FormRequest
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
        $orderIDs = [];
        foreach (Order::all() as $order) {
            array_push($orderIDs, $order->order_id);
        }
        $offerIDs = [];
        foreach (Offer::all() as $offer) {
            array_push($offerIDs, $offer->id);
        }
        return [
            'order_id' => ['required','string', Rule::in($orderIDs)],
            'offer_id' => ['required','string', Rule::in($offerIDs)],
            'comment' => ['required','string'],
            'evidence' => ['nullable','string'],
        ];
    }
}
