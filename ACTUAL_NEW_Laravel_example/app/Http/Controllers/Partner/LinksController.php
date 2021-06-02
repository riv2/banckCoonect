<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Jobs\XMLFeedCreate;
use App\Lists\LinksStatusesList;
use App\Models\Link;
use App\Models\OfferMaterial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LinksController extends Controller
{
    public function index(Request $request)
    {
        $links = Link::query()
            ->allowed()
            ->when($request->get('status', null), function (Builder $query, $status) {
                return $query->where('status', '=', $status);
            })
            ->with('offer')
            ->orderBy('links.id', 'desc')
            ->get();

        foreach ($links as &$link) {
            $link->copy_link = '<button class="btn btn-primary btn-sm js-click-tr js-clipboard-write" data-clipboard="' . $link->link . '">Скопировать&nbsp;в&nbsp;буфер</button>';
            $link->offer_name = $link->offer->offer_name;

            if ($link->has_macros) {
                $link->link = str_replace('{WEB_ID}', '<kbd>{WEB_ID}</kbd>', $link->link);
                $link->link = str_replace('{CLICK_ID}', '<kbd>{CLICK_ID}</kbd>', $link->link);
                $link->link = '<div><code>' . $link->link . '</code></div>';
                $link->link .= '<div class="mt-2">Внимание!!! Замените <kbd>{WEB_ID}</kbd> на ID субпартнера, а <kbd>{CLICK_ID}</kbd> на значение CLICK_ID</div>';
            } else {
                $link->link .= '<div class="mt-2">Если вы хотите передавать в ссылке CLICK_ID, настройте интеграцию по <a href="'.route('partner.postbacks').'">постбэкам</a></div>';
            }
        }

        return view('partner.links', [
            'statuses' => LinksStatusesList::getList(),
            'links' => $links,
        ]);
    }

    public function show($id)
    {
        return redirect(route('partner.link.show', $id));
    }

    public function create(Request $request)
    {
        $offer_material_id = $request->get('offer_material_id');

        /** @var OfferMaterial $OfferMaterial */
        $OfferMaterial = OfferMaterial::query()
            ->where('offer_material_id', '=', $offer_material_id)
            ->firstOrFail();

        //iss 2966 - не более одного фида на партнера
        if ($OfferMaterial->material_type == 'xmlfeed') {
            if (Link::query()->where('partner_id', '=', auth()->id())
                    ->where('offer_materials_id', '=', $OfferMaterial->offer_material_id)->count() > 0) {
                return redirect('/partner/offers/' . $OfferMaterial->offer_id)
                    ->withErrors(__('Фид уже существует'));
            }
        }

        $link = new Link();
        $link->offer_id = $OfferMaterial->offer_id;
        $link->partner_id = auth()->id();
        $link->status = 'ACTIVE';
        $link->offer_materials_id = $OfferMaterial->offer_material_id;
        $link->link_name = $OfferMaterial->name;
        $link->link = '';
        $link->pp_id = auth()->user()->pp->id;
        $link->save();

        if ($OfferMaterial->material_type == 'xmlfeed') {
            XMLFeedCreate::dispatch($link);
        }

        return redirect(route('partner.links.index'))
            ->with(['success' => 'Ссылка успешно добавлена']);
    }

    public function destroy(Request $request)
    {
        $id = $request->get('id', null);
        $link = Link::query()
            ->where('id', '=', $id)
            ->where('partner_id', '=', auth()->id())
            ->firstOrFail();
        $link->status = 'DELETED';
        $link->save();
        return 'Ссылка удалена успешно';
    }
}
