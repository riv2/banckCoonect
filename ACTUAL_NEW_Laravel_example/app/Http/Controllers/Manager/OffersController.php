<?php

namespace App\Http\Controllers\Manager;

use App\Filters\ManagerOffersFilter;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;

class OffersController extends Controller
{
    public function index(ManagerOffersFilter $filters)
    {
        $collection = Offer::query()
            ->filter($filters)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('manager.offers.index', [
            'fields' => $filters->fields,
            'offers'=> $collection,
        ]);
    }

    public function save(Request $request)
    {
        $offer = Offer::query()
            ->where('id', '=', $request->get('offer_id'))
            ->firstOrFail()
            ->update($request->toArray());

        return redirect(route('manager.offers.edit')
            . '?id=' . $request->get('offer_id'))->with('success', ['Оффер обновлен']);
    }
}
