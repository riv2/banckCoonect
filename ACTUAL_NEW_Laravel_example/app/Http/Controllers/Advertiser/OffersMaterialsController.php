<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OffersMaterialsController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(Request $request)
    {
        $offer = Offer::find($request->get('offer'));
        if (!$offer) {
            return redirect()
                ->route('advertiser.offers.index')
                ->withErrors( __('advertiser.offersmaterials.material-landing.oops') );
        }
        return view('advertiser.offersmaterials.new', [
            'offer' => $offer,
            'type' => $request->get('type')
            ]);
    }

    protected function storeMaterial($request, $material, $offer_id)
    {
        switch ($request->get("type")) {
            case "link":
            {
                $validator = [
                    "link" => ["url", "min:1"],
                ];

                $data = $request->validate($validator);
                $data["name"] = $data["link"];
                OfferMaterial::storeLink($offer_id, $data, $material);
                break;
            }
            case "banner":
            {
                $validator = [
                    "name" => ["string", "min:1"],
                    "link" => ["url", "min:1"],
                ];

                $data = $request->validate($validator);
                $subid = 1;
                $dt = date("YmdHis");
                $banners = [];
                $allowed_mimes = [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/tif',
                    'image/tiff',
                    'image/bmp',
                    'image/jpg',
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'application/msword', // doc
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // docx
                    'text/rtf',
                    'application/rtf',
                    'application/pdf',
                    'video/avi',
                    'video/mp4',
                    'video/mpeg',
                    'video/mov',
                    'image/webp',
                ];
                foreach ($request->file("banner") as $banner) {
                    if (!in_array($banner->getMimeType(), $allowed_mimes)) {
                        continue;
                    }

                    $file = $offer_id . '-' . $dt . $subid . '.' . $banner->extension();
                    $fname = "public/materials/banners/" . $file;
                    Storage::put($fname, $banner->get());
                    $subid++;
                    $banners[] = "storage/materials/banners/" . $file;
                }
                OfferMaterial::storeBanners($offer_id, $data["name"], $banners, $material);

                break;
            }
            case "landing":
            {
                $validator = [
                    "offer" => ["integer"],
                    "name" => ["string", "min:1"],
                    "link" => ["url", "min:1"],
                ];

                $data = $request->validate($validator);
                OfferMaterial::storeLanding($offer_id, $data);
                break;
            }
            case "xmlfeed":
            {
                $validator = [
                    "offer" => ["integer"],
                    "name" => ["string", "min:1"],
                    "link" => ["url", "min:1"],
                ];

                $data = $request->validate($validator);
                OfferMaterial::storeFeed($offer_id, $data, $material);
                break;
            }
            case "pwa":
            {
                $validator = [
                    "offer" => ["integer"],
                    "name" => ["string", "min:1"],
                    "api_url" => ["string", "min:1"],
                    "script" => ["string", "min:1"],
                ];

                $data = $request->validate($validator);
                OfferMaterial::storePWA($offer_id, $data, $material);
                break;
            }
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        $offer_id = $request->get("offer", null);

        $offer = Offer::query()
            ->where("id", "=", $offer_id)
            ->where("user_id", "=", Auth::id())->firstOrFail();

        $this->storeMaterial($request, new OfferMaterial(), $offer_id);

        return redirect(route('advertiser.offers.show', ['offer' => $offer]))->with('success', ["Рекламный материал успешно добавлен"]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @todo https://rt.gocpa.ru/task/2394
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        $item = OfferMaterial::query()->where("offer_material_id", "=", $request->get("id"))
            ->join("offers", "offers.id", "=", "offer_materials.offer_id")
            ->where("offers.user_id", "=", Auth()->id())->firstOrFail();

        return view("advertiser.offersmaterials.edit", [
            "item" => $item,
            "type" => $item->material_type
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @todo https://rt.gocpa.ru/task/2394
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $material = OfferMaterial::query()->where("offer_material_id", "=", $request->get("offer_material_id"))
            ->join("offers", "offers.id", "=", "offer_materials.offer_id")
            ->where("offers.user_id", "=", Auth()->id())->firstOrFail();
        $this->storeMaterial($request, $material, $material->offer_id);
        return redirect("/advertiser/offers/show?offer=" . $material->offer_id)
            ->with("success", ["Рекламный материал сохранен успешно"]);
    }

    /**
     * Remove the specified resource from storage.
     * @param $offer_material_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete($offer_material_id)
    {
        $material = OfferMaterial::query()->where('offer_material_id', '=', $offer_material_id)->first();
        $material->deleted_at = now();
        $material->save();
        return redirect(route('advertiser.offers.show', ['offer' => $material->offer_id]))->with('success', ['Рекламный материал успешно удален']);
    }
}
