<?php

namespace App\Http\Controllers\Advertiser\Settings;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class FAQCategoriesController extends Controller
{
    /**
     * Список элементов
     *
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        $collection = FaqCategory::query()
            ->where('pp_id', '=', auth()->user()->pp->id)
            ->paginate();

        return view('advertiser.settings.faq.index', [
            'collection' => $collection,
        ]);
    }

    /**
     * Форма добавления новой записи
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        return view('advertiser.settings.faq.create');
    }

    /**
     * Создание нового элемента
     * Метод отправки: POST
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $faqCategory = new FaqCategory();
        $faqCategory->title = $request->get('title');
        $faqCategory->pp_id = auth()->user()->pp->id;
        $faqCategory->save();

        return redirect()
            ->route('advertiser.settings.faq.show', $faqCategory)
            ->withSuccess(__('Запись успешно создана!'));
    }

    /**
     * Страница просмотра элемента
     *
     * @param $id
     * @return Application|Factory|Response|View
     */
    public function show($id)
    {
        $faqCategory = FaqCategory::query()
            ->where('id', '=', $id)
            ->first();
        $collection = Faq::query()
            ->where('faq_category_id', '=', $id)
            ->paginate();

        return view('advertiser.settings.faq.categories.index', [
            'collection' => $collection,
            'faqCategory' => $faqCategory,
        ]);
    }

    /**
     * Страница редактирования элемента
     *
     * @param $id
     * @return Application|Factory|Response|View
     */
    public function edit($id)
    {
        $faqCategory = FaqCategory::query()
            ->where('id', '=', $id)
            ->first();

        return view('advertiser.settings.faq.edit', [
            'faqCategory' => $faqCategory,
        ]);
    }

    /**
     * Сохранение элемента
     * Метод отправки: PUT
     *
     * @param Request $request
     * @param FaqCategory $faqCatigory
     * @return Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $faqCategory = FaqCategory::query()
            ->where('id', '=', $request->get('id'))
            ->first();
        $faqCategory->title = $request->get('title');
        $faqCategory->save();

        return redirect()
            ->route('advertiser.settings.faq.show', $faqCategory)
            ->withSuccess(__('Запись успешно изменена!'));
    }

    /**
     * Удаление элемента
     * Метод отправки: DELETE
     *
     * @param $id
     * @return Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        $faqCategory = FaqCategory::query()
            ->where('id', '=', $id)
            ->first();
        $faqCategory->delete();

         return redirect()
            ->route('advertiser.settings.faq.index')
            ->withSuccess(__('Запись успешно удалена!'));
    }
}
