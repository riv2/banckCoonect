<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Lists\OffersMaterialsTypesList;
use App\Lists\OffersMetumList;
use App\Models\MultiStorage;
use App\Models\Offer;
use App\Models\OfferMaterial;
use Composer\Package\Archiver\ZipArchiver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

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

        return view('partner.offers.index', [
            'collection' => $collection,
        ]);
    }

    public function download(Request $request)
    {
        $id = $request->get('id');
        $materials = OfferMaterial::query()->where('offer_material_id', '=', $id)->firstOrFail();

        $zipname = 'materials/banners/' . $id . '.zip';
        if (!MultiStorage::exists($zipname)) {
            $zip = new \ZipArchive();
            $tmpfile = MultiStorage::getRealPath('tmp_' . md5(time() . auth()->id() . '\t' . $id));
            $res = $zip->open($tmpfile, \ZipArchive::CREATE);
            if ($res !== true) {
                return redirect('/partner/offers/' . $materials->offer_id)->withErrors(['Не удается создать архив']);
            }
            foreach ($materials->material_files as $file) {
                $res = $zip->addFile(MultiStorage::getRealPath($file), basename($file));
            }
            $zip->close();

            if (!rename($tmpfile, MultiStorage::getRealPath($zipname))) {
                return redirect('/partner/offers/' . $materials->offer_id)->withErrors(['Не удается сохранить архив']);
            }
        }
        return redirect('/' . $zipname);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Offer $offer
     * @return \Illuminate\View\View
     */
    public function show(Offer $offer)
    {
        // Если нужна премодерация офферов
        if ($offer->flag_approvable) {
            // Если заявка не была подана
            if (!$offer->partnersApproves(auth()->id())->exists()) {
                return redirect()->route('partner.offers.approve.approve', $offer);
            }

            // Если заявка не была подана
            if ($offer->partnersApproves(auth()->id(), 'pending')->exists()) {
                return redirect()->route('partner.offers.index')->withSuccess('Ваша заявка на премодерацию для Вашего участия в данном оффере находится в статусе рассмотрения.');
            }

            // Если заявка не была подана
            if ($offer->partnersApproves(auth()->id(), 'declined')->exists()) {
                return redirect()->route('partner.offers.index')->withErrors('Ваша заявка на премодерацию для Вашего участия в данном оффере отклонена.');
            }
        }

        $offer->load([
            'links' => function (Relation $query) {
                return $query->withCount([
                    'clicks',
                    'orders',
                    'orders_sales',
                ]);
            }
        ]);
        $tmp = OfferMaterial::query()->where('offer_id', '=', $offer->id)
            ->orderBy('material_type')
            ->get();
        $materials = array_fill_keys(array_keys(OffersMaterialsTypesList::getList()), []);
        foreach ($tmp as $t) {
            $materials[$t->material_type][] = $t;
        }

        $rawOfOfferMaterial = OfferMaterial::query()->where('offer_id', '=', $offer->id)
            ->where('material_type', '=', 'landing')->first();

        return view('partner/offers/show', [
            'offer' => $offer,
            'materials' => $materials,
            'metum' => OffersMetumList::getList(),
            'materials_types' => OffersMaterialsTypesList::getList(),
            'item' => $rawOfOfferMaterial,
        ]);
    }

    public function requestApprove(Request $request, Offer $offer)
    {
        abort_if($offer->flag_approvable && $offer->partnersApproves(auth()->id(), 'pending')->exists(), 403, 'Вы уже отправили заявку на подтверждение');

        return view('partner/offers/request_approve', [
            'offer' => $offer,
        ]);
    }

    public function requestApproveStore(Request $request, Offer $offer)
    {
        abort_if($offer->flag_approvable && $offer->partnersApproves(auth()->id())->exists(), 403, 'Вы уже отправили заявку на подтверждение');

        DB::table('offers_partners_approves')->insert([
            'offer_id' => $offer->id,
            'partner_id' => auth()->id(),
            'created_at' => now(),
            'status' => 'approved', // Пока без премодерации (EgorV)
        ]);

        return redirect()->route('partner.offers.index')->withSuccess('Получите ссылку для начала работы с оффером');
    }
}
