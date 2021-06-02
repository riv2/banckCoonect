<?php

namespace App\Console\Commands;

use App\Mail\NewsMail;
use App\Models\News;
use App\Notifications\NewsNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NewsSendCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:send {news_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправляет письма для новости';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $f = fopen(storage_path('logs/news_send.lock'), 'w+');
        if (!flock($f, LOCK_EX | LOCK_NB)) {
            Log::channel('news_send')->warning('Уже отправляется!');
            $this->warn('Уже отправляется!');
            exit(0);
        }
        fwrite($f, "Locked\n");
        fflush($f);

        $news = News::findOrFail($this->argument('news_id'));
        $news->loadCount(['recipients', 'emailRecipients', 'sending', 'sent'])->load(['sending']);
        $this->output->title('Отправка новости "' . $news->news_title . '"');
        $this->info('Получателей: ' . $news->recipients_count);
        $this->info('Из них подписаны на рассылку: ' . $news->email_recipients_count);
        $this->info('Ожидают отправки: ' . $news->sending_count);
        $this->info('Отправлено: ' . $news->sent_count);

        foreach ($news->sending as $recipient) {
            $this->line($recipient->email);
            try {
                $recipient->notify(new NewsNotification($news));
                /** @phpstan-ignore-next-line */
                $recipient->pivot->sended_at = now();
                Log::channel('news_send')->info('Отправляю новость #'.$this->argument('news_id').' по адресу ' . $recipient->email);
            } catch (\Throwable $th) {
                throw $th;
            }
            /** @phpstan-ignore-next-line */
            $recipient->pivot->save();
        }
        flock($f, LOCK_UN);
        fclose($f);
    }
}
