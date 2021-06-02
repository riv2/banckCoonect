<?php

namespace App\Console\Commands\Checkers;

use App\Models\Link;
use Illuminate\Console\Command;
use Throwable;

class LinkTemplateChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checker:link_template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет то, что все ссылки у партнеров созданы в соответствии с link_template у оффера';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $links = Link::all();
        foreach ($links as $link) {
            if (empty($link->link)) {
                $this->warn('Пустой link в ' . $link->id . '!');
                continue;
            }

            try {
                $generated_link = $link->generateLink();

                if ($link->link !== $generated_link) {
                    $url1 = parse_url($link->link);
                    $url2 = parse_url($generated_link);

                    if ($url1['host'] != $url2['host']) {
                        $this->warn('Хосты ' . $url1['host'] . ' и ' . $url2['host'] . ' не совпадают в ссылке ' . $link->id . '!');
                        continue;
                    }

                    $url1['query'] = str_replace('{{cpa_click_id}}', '{CLICK_ID}', $url1['query']);
                    $url1['query'] = str_replace('{{cpa_partner_id}}', '{WEB_ID}', $url1['query']);

                    parse_str(($url1['query'] ?? ''), $query_string_1);
                    parse_str(($url2['query'] ?? ''), $query_string_2);
                    ksort($query_string_1);
                    ksort($query_string_2);

                    if (!isset($query_string_1['utm_term']) && !isset($query_string_1['click_id'])) {
                        unset($query_string_1['utm_term']);
                        unset($query_string_1['click_id']);
                        unset($query_string_2['utm_term']);
                        unset($query_string_2['click_id']);
                    }

                    $query_string_1 = urldecode(http_build_query($query_string_1));
                    $query_string_2 = urldecode(http_build_query($query_string_2));
                    if ($query_string_1 != $query_string_2) {
                        $this->warn('Query string не совпадают в ссылке ' . $link->id . '!');
                        $this->line('O: ' . $query_string_1);
                        $this->line('G: ' . $query_string_2);
                        $this->line('');
                        continue;
                    }
                }
            } catch (Throwable $th) {
                dump($th->getMessage());
                // throw $th;
            }
        }
        return 0;
    }
}
