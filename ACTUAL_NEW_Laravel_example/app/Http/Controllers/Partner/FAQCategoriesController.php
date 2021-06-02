<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FAQCategoriesController extends Controller
{
    /**
     * Список элементов
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $collection = FaqCategory::query()->where('pp_id','=', auth()->user()->pp->id)->get();
        foreach ($collection as $item) {
            $item->faq = Faq::query()->where('faq_category_id', '=', $item->id)->get();
        }

        return view('partner.faq.index', [
            'collection' => $collection,
        ]);
    }

    /**
     * Форма добавления новой записи
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('controllers.faq.create');
    }

    /**
     * Создание нового элемента
     * Метод отправки: POST
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'element' => 'required',
        ]);

        $faqCategory = new FaqCategory();
        $faqCategory->element = $request->get('element');
        $faqCategory->save();

        return redirect()
            ->route('controllers.faq.show', $faqCategory)
            ->withSuccess(__('Запись успешно создана!'));
    }

    /**
     * Страница просмотра элемента
     *
     * @param  \App\Models\FaqCategory  $faqCategory
     * @return \Illuminate\Http\Response
     */
    public function show(FaqCategory $faqCategory)
    {
        return view('controllers.faq.show', [
            'faqCategory' => $faqCategory,
        ]);
    }

    /**
     * Страница редактирования элемента
     *
     * @param  \App\Models\FaqCategory  $faqCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(FaqCategory $faqCategory)
    {
        return view('controllers.faq.edit', [
            'faqCategory' => $faqCategory,
        ]);
    }

    /**
     * Сохранение элемента
     * Метод отправки: PUT
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FaqCategory  $faqCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FaqCategory $faqCategory)
    {
        $request->validate([
            'element' => 'required',
        ]);

        $faqCategory->element = $request->get('element');
        $faqCategory->save();

        return redirect()
            ->route('controllers.faq.show', $faqCategory)
            ->withSuccess(__('Запись успешно сохранена!'));
    }

    /**
     * Удаление элемента
     * Метод отправки: DELETE
     *
     * @param  \App\Models\FaqCategory  $faqCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(FaqCategory $faqCategory)
    {
        // $faqCategory->delete();

        // return redirect()
        //    ->route('controllers.faq.show', $faqCategory)
        //    ->withSuccess(__('Запись успешно удалена!'));
    }
}
