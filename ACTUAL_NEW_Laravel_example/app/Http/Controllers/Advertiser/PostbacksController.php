<?php

namespace App\Http\Controllers\Advertiser;

use App\Filters\PostbacksFilter;
use App\Http\Controllers\Controller;
use App\Models\ApiLog;
use Illuminate\Http\Request;

class PostbacksController extends Controller
{
    /**
     * @todo https://rt.gocpa.ru/task/2394
     * @param PostbacksFilter $filters
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(PostbacksFilter $filters)
    {
        return view("advertiser.integration.postbacks", [
            "fields"=>$filters->fields,
            "postbacks"=>ApiLog::filter($filters)
                ->join("offers", "offers.id", "=", "api_log.offer_id")
                ->where("offers.user_id", "=", Auth()->id())
                ->orderBy("api_log.created_at", "DESC")
                ->paginate(20)
        ]);
    }
}
