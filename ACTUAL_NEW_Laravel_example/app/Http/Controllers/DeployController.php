<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    public function deploy(Request $request)
    {
        debugbar()->disable();

        $current_branch = explode('/', shell_exec('git -C "' . base_path() . '" symbolic-ref HEAD'));
        $current_branch = trim(end($current_branch));

        $branches = $request->json('push.changes.*.new.name');
        if (!in_array($current_branch, $branches)) {
            return response()->noContent();
        }

        $msg = [];
        $msg[] = 'Коммит в репозиторий ' . $request->json('repository.full_name');

        $commits = $request->json('push.changes.*.commits.*', []);
        foreach ($commits as $commit) {
            $author_link = $commit['author']['user']['links']['html']['href'] ?? '#';
            $author_name = $commit['author']['user']['display_name'] ?? 'no data';
            $commit_hash = Str::substr($commit['hash'], 0, 7);
            $commit_link = $commit['links']['html']['href'];
            $commit_message = trim($commit['message']);
            $msg[] = '<a href="' . $commit_link . '">' . $commit_hash . '</a> ' . $commit_message;
            $msg[] = '👨🏻‍💻 by <a href="' . $author_link . '">' . $author_name . '</a>';
            $msg[] = '';
        }

        $msg[] = '💻 Сервер: ' . gethostname();
        $msg[] = '🔨 Папка <code>' . base_path() . '</code>';
        $msg[] = '🌳 Ветка: ' . $current_branch . '';
        $msg[] = '';

        $porcelain = shell_exec('git -C "' . base_path() . '" status --untracked-files=no --porcelain');
        if ($porcelain !== null) {
            $msg[] = 'На сервере есть незакомиченные изменения, необходимо применить их и вручную выполнить git pull' . PHP_EOL . $porcelain;
        } else {
            $pull = trim(shell_exec('git -C "' . base_path() . '" pull'));

            $msg[] = 'Результат выполнения команды git pull:';
            $msg[] = '<pre>' . $pull . '</pre>';
        }

        $this->telegram($msg);
        return response()->noContent(200);
        // return response()->make(join('<br>', $msg), 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function telegram($msg)
    {
        if (is_array($msg)) {
            $msg = join(PHP_EOL, $msg);
        }

        $fields = [
            'chat_id' => env('TELEGRAM_LOGGER_CHAT_ID', -260979041),
            'text' => $msg,
            'parse_mode' => 'html',
            'disable_notification' => false,
            'disable_web_page_preview' => true,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot649665586:AAHAINKwdW7ex0DS5nxKZ5TCwmZS5zuOR0U/sendMessage');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        // dump($result);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            // dump($error_msg);
        }
        curl_close($ch);
    }
}
