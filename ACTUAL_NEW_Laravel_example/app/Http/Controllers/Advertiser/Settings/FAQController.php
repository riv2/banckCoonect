<?php

namespace App\Http\Controllers\Advertiser\Settings;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Pp;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    /**
     * Список элементов
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index($id)
    {
//        $collection = Faq::query()
//            ->where('faq_category_id', '=', $id)
//            ->paginate();
//
//        return view('advertiser.settings.faq.index', [
//            'collection' => $collection,
//        ]);
    }

    /**
     * Форма добавления новой записи
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create($id)
    {
        $faqCategory = FaqCategory::query()
            ->where('id', '=', $id)
            ->first();

        return view('advertiser.settings.faq.categories.create')->with(['faqCategory' => $faqCategory]);
    }

    /**
     * Создание нового элемента
     * Метод отправки: POST
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'answer' => '',
        ]);

        $faq = new faq();
        $faq->question = $request->get('question');
        $faq->answer = $request->get('answer');
        $faq->faq_category_id = $request->get('faq_category_id');
        $faq->save();

        return redirect()
            ->route('advertiser.settings.faq.show', $request->get('faq_category_id'))
            ->withSuccess(__('Запись успешно создана!'));

    }

    /**
     * Страница просмотра элемента
     *
     * @param \App\Models\Faq $faq
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(faq $faq)
    {
        return view('settings.faq.show', [
            'faq' => $faq,
        ]);
    }

    /**
     * Страница редактирования элемента
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($faq_category_id, $id)
    {
        $faqCategory = FaqCategory::query()
            ->where('id', '=', $faq_category_id)
            ->first();
        $faq = Faq::query()
            ->where('id', '=', $id)
            ->first();

        return view('advertiser.settings.faq.categories.edit', [
            'faqCategory' => $faqCategory,
            'faq' => $faq,
        ]);
    }

    /**
     * Сохранение элемента
     * Метод отправки: PUT
     *
     * @param \Illuminate\Http\Request $request
     * @param $faq_category_id
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $faq_category_id, $id)
    {
//        dump($faq_category_id);
//        dump($id);
//        dd($request->toArray());
        $request->validate([
            'question' => 'required',
            'answer' => '',
        ]);

        $faq = Faq::query()
            ->where('id', '=', $id)
            ->first();
        $faq->question = $request->get('question');
        $faq->answer = $request->get('answer');
        $faq->faq_category_id = $faq_category_id;
        $faq->save();

        return redirect()
            ->route('advertiser.settings.faq.show', $faq_category_id)
            ->withSuccess(__('Запись успешно изменена!'));
    }

    /**
     * Удаление элемента
     * Метод отправки: DELETE
     *
     * @param \App\Models\Faq $faq
     * @return \Illuminate\Http\Response
     */
    public function destroy($faq_category_id, $id)
    {
        $faq = Faq::query()
            ->where('id', '=', $id)
            ->first();
        $faq->delete();

        return redirect()
            ->route('advertiser.settings.faq.show', $faq_category_id)
            ->withSuccess(__('Запись успешно удалена!'));
    }
}
