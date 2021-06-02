<?php

namespace App\Http\Controllers\Manager;

use App\Filters\PixelLogFilter;
use App\Http\Controllers\Controller;
use App\Jobs\RecalcPixelJob;
use App\Models\PixelLog;

class PixelController extends Controller
{
    /**
     * Список элементов
     *
     * @param \App\Filters\PixelLogFilter $filter
     * @return \Illuminate\Http\Response
     */
    public function index(PixelLogFilter $filter)
    {
        $collection = PixelLog::query()
            ->filter($filter)
            ->with(['pp', 'click', 'order'])
            ->orderBy('id', 'desc')
            ->paginate();

        return view('manager.pixel.index', [
            'collection' => $collection,
        ]);
    }

    /**
     * Пересчет пикселя
     *
     * @param \App\Filters\PixelLogFilter $filter
     * @return \Illuminate\Http\Response
     */
    public function recalc(PixelLogFilter $filter)
    {
        $collection = PixelLog::query()
            ->filter($filter)
            ->chunk(10000, [$this, 'recalcCollection']);

        return redirect()->back()->withSuccess('Пересчет ставок запущен!');
    }

    public function recalcCollection($collection)
    {
        RecalcPixelJob::dispatch($collection);
    }
}
