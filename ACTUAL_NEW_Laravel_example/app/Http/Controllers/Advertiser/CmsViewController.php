<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Lists\CmsList;
use App\Models\Offer;
use App\User;
use Illuminate\Http\Request;

class CmsViewController extends Controller
{
    public function index()
    {
        $cmsSystems = CmsList::getList();

        return view('advertiser.integration.cms.index')->with('cmsSystems', $cmsSystems);
    }

    public function tilda(Request $request)
    {
        $projectid = $request->get('projectid');
        if (!isset($projectid)) {

            return view('advertiser.integration.cms.tilda');
        } else {
            $pixelCode = '        <!-- Start ' . auth()->user()->pp->tech_domain . ' TRACK -->
        <script>
        !function(e,t,p,c,a,n,o){e[c]||((a=e[c]=function(){a.process?a.process.apply(a,arguments):a.queue.push(arguments)}).queue=[],a.t=+new Date,(n=t.createElement(p)).async=1,n.src="https://' . auth()->user()->pp->tech_domain . '/openpixel.min.js?t="+864e5*Math.ceil(new Date/864e5),(o=t.getElementsByTagName(p)[0]).parentNode.insertBefore(n,o))}(window,document,"script","gocpa");
        gocpa("init","https://' . auth()->user()->pp->tech_domain . '/cpapixel.gif");
        gocpa("event","pageload");
        </script>
        <!-- End ' . auth()->user()->pp->tech_domain . ' TRACK  -->';

            if (isset($request->createWebhook)) {
                $lastCreatedOffer = Offer::getOwnOffers(User::query()->where('id', '=', auth()->user()->id)->first())->last();
                $webhookLink = 'https://' . auth()->user()->pp->tech_domain
                    . '/webhook/tilda/?offer_id=' . $lastCreatedOffer->id
                    . '&status=' . $lastCreatedOffer->model
                    . '&sign=' . md5($lastCreatedOffer->id);
            }

            return view('advertiser.integration.cms.tilda')->with([
                'projectid' => $request->get('progectid'),
                'pixelCode' => $pixelCode,
                'webhookLink' => $webhookLink ?? '',
            ]);
        }
    }
}
