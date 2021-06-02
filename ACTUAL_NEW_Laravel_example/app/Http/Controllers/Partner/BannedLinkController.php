<?php

namespace App\Http\Controllers\Partner;

use App\Filters\BannedLinksFilter;
use App\Http\Controllers\Controller;
use App\Models\BannedLink;

class BannedLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(BannedLinksFilter $filters)
    {
        $bannedLinks = BannedLink::query()
            ->filter($filters)
            ->with('link')
            ->whereHas('link', function($query){
                $query->where('partner_id', '=', auth()->user()->id);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate();

        return view('partner.banned_links.index', [
            'fields' => $filters->fields,
            'bannedLinks' => $bannedLinks,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BannedLink  $bannedLink
     * @return \Illuminate\Http\Response
     */
    public function show(BannedLink $bannedLink)
    {
        $link = $bannedLink->link()
            ->with('partner')
            ->where('partner_id', '=', auth()->user()->id)
            ->firstOrFail();
        return view('partner.banned_links.show', [
            'bannedLink' => $bannedLink,
            'link' => $link,
        ]);
    }
}
