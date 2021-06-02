<?php

namespace App\Http\Controllers\Manager;

use App\Filters\ManagerOffersFilter;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\TrafficSource;
use Illuminate\Http\Request;

class TrafficSourcesController extends Controller
{
    public function index()
    {
        return view("manager.traffic_sources.list", [
            "sources"=>TrafficSource::all()
        ]);
    }

    public function create()
    {
        return view("manager.traffic_sources.new");
    }

    public function save(Request $request)
    {
        $title = $request->get("title","");
        $id = $request->get("id",null);
        if (empty($title)) {
            return redirect(route("manager.traffic.sources.new"))->withErrors(["Пустое название"]);
        }
        if($id) {
            $ts = TrafficSource::query()->where("id","=", $id)->firstOrFail();
            $ts->title=$title;
            $ts->save();
        } else {
            (new TrafficSource(["title" => $title]))->save();
        }

        return redirect(route("manager.traffic.sources"))->with("success", ["Сохранено"]);
    }

    public function edit(Request $request)
    {
        return view("manager.traffic_sources.new", [
            "source"=>TrafficSource::query()
                ->where("id","=", $request->get("id", null))
                ->firstOrFail()
        ]);
    }
}
