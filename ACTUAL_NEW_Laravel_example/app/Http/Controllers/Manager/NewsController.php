<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $news = News::query()
            ->withCount([
                'recipients',
                'emailRecipients',
                'sent',
                'readed',
            ])
            ->orderBy("id", "DESC")
            ->paginate(20);

        return view('manager.news.index', [
            'news' => $news,
        ]);
    }

    public function show(News $news)
    {
        $news->loadCount([
            'recipients',
            'emailRecipients',
            'sent',
            'readed',
        ]);

        return view('manager.news.show', ['item' => $news]);
    }

    public function create()
    {
        return view('manager.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'news_title' => 'required|string|min:5',
            'news_text' => 'required|string|min:15',
            'send_to' => ['required', Rule::in(News::$send_to_list)],
        ]);

        $news = new News;
        $news->created_at = now();
        $news->news_title = $request->get('news_title');
        $news->news_text = $request->get('news_text');
        $news->send_to = $request->get('send_to');
        $news->send_to_value = $request->get('send_to_value');
        $news->save();

        return redirect()
            ->route(auth()->user()->role . '.news.show', ['news' => $news])
            ->withSuccess(['Новость#' . $news->id . ' успешно добавлена!']);
    }

    public function send(News $news)
    {
        Artisan::queue('news:send', ['news_id' => $news->id]);
        return redirect()->back()->withSuccess(['Новость#' . $news->id . ' поставлена в очередь на отправку!']);
    }
}
