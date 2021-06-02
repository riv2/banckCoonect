<?php

/**
 * Project qpartners
 * Created by danila 23.12.2020 @ 0:14
 */

namespace App\Processors;

use App\Models\Link;
use App\Models\XmlfeedCategory;
use App\Models\XmlfeedOffer;
use Illuminate\Support\Facades\Log;

class PartnerFeedProcessor
{
    /** @var string */
    public $logPrefix = '';

    /** @var Link */
    public $link;

    /** @var resource */
    public $fileHandler;

    public function __construct(Link $link)
    {
        $this->link = $link;
        $this->logPrefix = 'PartnerFeed #' . $link->id . ': ';
    }

    public function process()
    {
        $this->doLog('Старт записи');
        $bp = parse_url($this->link->link);
        $bp = explode('/', $bp['path']);
        $filename = array_pop($bp);
        $dirname = public_path() . '/feeds/';
        if (!file_exists($dirname)) {
            mkdir($dirname, 0755, true);
        }
        $this->startXMLWrite($dirname . $filename);
        $this->doLog('Сохраняем категории');
        $this->writeCategories();
        $this->doLog('Сохраняем офферы');
        $this->writeOffers();
        $this->closeFile();
        $this->doLog('Успешно');
    }

    protected function doLog($message)
    {
        Log::info($this->logPrefix . $message);
    }

    protected function startXMLWrite($filename)
    {
        $this->fileHandler = fopen($filename, 'w');
        fwrite($this->fileHandler, '<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="' . now()->format('Y-m-d H:i') . '">
<shop>
<name>' . $this->link->offer->offer_name . '</name>
<company></company>
<url>' . $this->link->link . '</url>
<currencies>
<currency id="RUR" rate="1"/>
</currencies>
');
    }

    protected function writeCategories()
    {
        fwrite($this->fileHandler, '<categories>');
        foreach (XmlfeedCategory::query()
            ->where('pp_id', '=', $this->link->pp_id)
            ->get() as $category) {
            fwrite($this->fileHandler, '<category id="' . $category->category_id . '">' . $category->name . '</category>' . PHP_EOL);
        }
        fwrite($this->fileHandler, '</categories>' . PHP_EOL);
    }

    protected function closeFile()
    {
        fwrite($this->fileHandler, '</shop></yml_catalog>');
        fclose($this->fileHandler);
    }

    protected function writeOffers()
    {
        fwrite($this->fileHandler, '<offers>');
        $offers = XmlfeedOffer::query()
            ->where('pp_id', '=', $this->link->pp_id)
            ->get();
        foreach ($offers as $offer) {
            $url = htmlspecialchars($this->link->generateUrlWithTemplate($offer->url, false));

            $xml = $offer->xml_data['offer'];
            $xml = str_replace('%%offer_url%%', $url, $xml);

            fwrite($this->fileHandler, $xml . PHP_EOL);
        }
        fwrite($this->fileHandler, '</offers>');
    }
}
