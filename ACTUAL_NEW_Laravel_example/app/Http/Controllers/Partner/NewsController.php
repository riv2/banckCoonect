<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     *
     * @todo https://rt.gocpa.ru/task/2394
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        $news = News::query()
            ->join('news_users', 'news_users.news_id', 'news.id')
            ->where('news_users.user_id', '=', auth()->id())
            ->orderBy('created_at', 'DESC')
            ->paginate();

        return view('partner.news.index', [
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

        return view('partner.news.show', ['item' => $news]);
    }
}
