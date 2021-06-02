<?php
namespace App\Http\Controllers\Advertiser;

use App\Filters\ServicedeskTaskTemplateFilter;
use App\Http\Controllers\Controller;
use App\Models\ServicedeskTaskTemplate;
use Illuminate\Http\Request;

class ServicedeskTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(ServicedeskTaskTemplateFilter $filter)
    {
        $collection = ServicedeskTaskTemplate::query()
            ->filter($filter)
            ->get();

        return view('advertiser.servicedesk.templates.index', [
            'collection' => $collection,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('advertiser.servicedesk.templates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $item = new ServicedeskTaskTemplate();
        $item->title = $request->get('title', 'new template');
        $item->body = $request->get('body', 'new template');
        $item->is_favorite = $request->get('is_favorite', 0);
        $item->save();
        return redirect()->route('advertiser.servicedesk.templates.show', $item)->withSuccess(['Успешно создано!']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        $item = ServicedeskTaskTemplate::find($id);

        return view('manager.servicedesk.templates.show', [
            'item' => $item,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ServicedeskTaskTemplate  $item
     * @return \Illuminate\View\View
     */
    public function edit(ServicedeskTaskTemplate $item)
    {
        return view('manager.servicedesk.templates.show', [
            'item' => $item,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $item = ServicedeskTaskTemplate::findOrFail($id);
        $item->title = $request->get('title');
        $item->body = $request->get('body');
        $item->is_favorite = $request->get('is_favorite');
        $item->save();
        return redirect()->route('manager.servicedesk.templates.show', $item)->withSuccess('Успешно сохранено!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        $item = ServicedeskTaskTemplate::findOrFail($id);
        if ($item->delete()) {
            return redirect()->route('manager.servicedesk.templates.index')->withSuccess('Успешно удалено!');
        }
        return redirect()->route('manager.servicedesk.templates.index')->withErrors('Ошибка при удалении!');
    }
}
