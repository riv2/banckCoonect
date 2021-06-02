<?php

namespace App\Http\Requests;

use App\Lists\OrderStateList;
use App\Models\Offer;
use App\Models\Order;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrdersOrder extends FormRequest
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
        $orderIDs = [];
        foreach (Order::all() as $order) {
            array_push($orderIDs, $order->order_id);
        }
        $offerIDs = [];
        foreach (Offer::getOwnOffers(auth()->user()) as $offer) {
            array_push($offerIDs, $offer->id);
        }

        return [
            'order_id' => ['required', 'string', Rule::notIn($orderIDs)],
            'offer_id' => ['required', Rule::in($offerIDs)],
            'partner_id' => ['integer', Rule::in($partners)],
            /**
             * @todo Дополнительные правила валидации для поля link_id?
             */
            'link_id' => 'integer',
            'gross_amount' => ['required', 'numeric'],
            'datetime' => 'required|date',

//            'status' => ['required', Rule::in(array_keys(OrderStateList::getList()))],
//            'click_id' => 'string|nullable',
//            'web_id' => 'string|nullable',
//            'client_id' => 'string|nullable',
//            'wholesale' => 'boolean',
        ];
    }
}
