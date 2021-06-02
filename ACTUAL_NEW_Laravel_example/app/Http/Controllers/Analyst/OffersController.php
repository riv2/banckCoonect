<?php

namespace App\Http\Controllers\Analyst;

use App\Helpers\PartnerProgramStorage;
use App\Http\Controllers\Controller;
use App\Lists\OffersFeeTypeList;
use App\Lists\OffersMaterialsTypesList;
use App\Lists\OffersMetumList;
use App\Lists\OrderStateList;
use App\Models\Offer;
use App\Models\OfferMaterial;
use App\Models\RateRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OffersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $collection = Offer::query()
            ->paginate();

        return view('analyst.offers.index', [
            'collection' => $collection,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('analyst.offers.create');
    }

    public function edit(Request $request)
    {
        $offer = Offer::query()
            ->where('id', '=', $request->get('offer'))
            ->where('user_id', '=', auth()->id())
            ->firstOrFail();

        return view('analyst.offers.edit', ['offer' => $offer]);
    }

    public function show(Request $request, Offer $offer)
    {
        $tmp = OfferMaterial::query()->where('offer_id', '=', $offer->id)
            ->orderBy('material_type')
            ->get();

        $materials = array_fill_keys(array_keys(OffersMaterialsTypesList::getList()), []);
        foreach ($tmp as $t) {
            $materials[$t->material_type][] = $t;
        }

        return view('analyst.offers.show', [
            'offer' => $offer,
            'materials' => $materials,
            'materials_types' => OffersMaterialsTypesList::getList(),
            'rateRules' => RateRule::query()->where('offer_id', '=', $offer->id)
            ->orderBy('id')
            ->get(),
        ]);
    }

    protected function doFillOffer(Offer $offer, Request $request)
    {
        $fields = [
            'offer_name' => 'required|string|min:5',
            'description' => 'required|string|min:15',
            'model' => ['string', Rule::in(array_keys(OrderStateList::getList()))],
            'fee_type' => ['string', Rule::in(array_keys(OffersFeeTypeList::getFeeTypeList()))],
            'image' => 'image|max:2048',
        ];
        $request->validate($fields);

        $offer->offer_name = $request->get('offer_name');
        $offer->fee_type = $request->get('fee_type');
        $offer->model = $request->get('model');
        $offer->description = $request->get('description');
        $offer->user_id = auth()->id();

        $offer->save();

        $file = $request->file('image');
        if ($file && $file->isValid()) {
            $file_name = $offer->id . '.' . $file->extension();
            $file->storePubliclyAs('offers', $file_name, 'public');
            $offer->image = '/storage/offers/' . $file_name;
            $offer->save();
        }

        foreach (OffersMetumList::getList() as $meta => $data) {
            $offer->setMeta($meta, $request->get($meta, null), $data);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Offer $offer)
    {
        $this->doFillOffer($offer, $request);
        return redirect(route('analyst.offers.show', ['offer' => $offer]))->with('success', ['Оффер сохранен']);
    }

    /** @return \Illuminate\Http\RedirectResponse */
    public function store(Request $request)
    {
        //Создадим оффер
        $offer = new Offer();
        $this->doFillOffer($offer, $request);

        //Запишем дефолтную ставку в rate_rules
        $rate = new RateRule();
        $rate->offer_id = $offer->id;
        $rate->fee = $request->fee;
        $rate->date_start = date("Y-m-d");
        $rate->save();

        //Создадим первый лендинг
        $landing = new OfferMaterial();
        $landing->storeLanding($offer->id, ['link'=>$request->url, 'name'=>'Main page']);

        //Сменим статус онбординга если нужно
        $pp = PartnerProgramStorage::getPP();
        if ($pp->onboarding_status == 'step1') {
            $pp->onboarding_status = 'first_offer_added';
            $pp->save();
        }

        return redirect(route('analyst.offers.show', $offer))->with('success', [__('Оффер сохранен')]);
    }
}
