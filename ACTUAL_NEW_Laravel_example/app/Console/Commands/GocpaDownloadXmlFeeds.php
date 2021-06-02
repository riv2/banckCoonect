<?php

namespace App\Console\Commands;

use App\Jobs\XMLFeedCreate;
use App\Models\OfferMaterial;
use App\Models\XmlfeedCategory;
use App\Models\XmlfeedOffer;
use App\Processors\PartnerFeedProcessor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Exception;

class GocpaDownloadXmlFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gocpa:downloadxmlfeeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсим фиды рекламодателя и укладываем данные в БД';

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
        $offerMaterials = OfferMaterial::query()
            ->where('material_type', '=', 'xmlfeed')
            ->where('status', '=', 1)
            ->get();

        foreach ($offerMaterials as $offerMaterial) {
            $this->process($offerMaterial);
        }
        return 0;
    }



    public function process($offerMaterial)
    {
        $link = $offerMaterial->material_params['link'];
        $md5 = (isset($offerMaterial->material_params['md5'])) ? $offerMaterial->material_params['md5'] : 'none';
        $this->info(sprintf('Начало обработки XML фида #%s (%s , %s) hash:%s', $offerMaterial->offer_material_id, $offerMaterial->name, $link, $md5));
        $this->logPrefx = 'Фид #' . $offerMaterial->offer_material_id . ': ';
        $f = file_get_contents($link);
        if (!$f) {
            throw new Exception('Пустой файл');
        }

        $hash = md5($f);
        if ($hash == $md5) {
            $this->warn('Файл не обновлялся с момента прошлого запуска, пропускаем');
            return false;
        }
        $filename = 'offersxml/' . $offerMaterial->offer_material_id . date('.YmdHi') . '.xml';
        Storage::put($filename, $f);
        $pp_id = $offerMaterial->offer->pp_id;

        // Удалим старые записи
        XmlfeedCategory::query()
            ->where('pp_id', '=', $pp_id)
            ->where('offer_material_id', '=', $offerMaterial->offer_material_id)
            ->delete();
        XmlfeedOffer::query()
            ->where('pp_id', '=', $pp_id)
            ->where('offer_material_id', '=', $offerMaterial->offer_material_id)
            ->delete();

        $xml = simplexml_load_string($f);
        $this->info('Собираем категории офферов');
        foreach ($xml->shop->categories->category as $cat) {
            $id = (string) $cat->attributes()['id'];
            $name = (string) $cat[0];
            $xmlfeed_category = new XmlfeedCategory();
            $xmlfeed_category->pp_id = $pp_id;
            $xmlfeed_category->category_id = $id;
            $xmlfeed_category->offer_material_id = $offerMaterial->offer_material_id;
            $xmlfeed_category->name = $name;
            $xmlfeed_category->save();
        }

        $this->info('Собираем офферы');
        foreach ($xml->shop->offers->offer as $offer) {
            $url = (string) $offer->url;
            $offer->url = '%%offer_url%%';

            $xmlfeed_offer = new XmlfeedOffer();
            $xmlfeed_offer->pp_id = $pp_id;
            $xmlfeed_offer->category_id = (string) $offer->categoryId;
            $xmlfeed_offer->url = $url;
            $xmlfeed_offer->xml_data = ['offer' => $offer->asXML()];
            $xmlfeed_offer->offer_material_id = $offerMaterial->offer_material_id;
            $xmlfeed_offer->save();
        }
        $offerMaterial->setMaterialParam('md5', $hash);
        $offerMaterial->save();

        foreach ($offerMaterial->links as $link) {
            // XMLFeedCreate::dispatch($link);
            $processor = new PartnerFeedProcessor($link);
            $processor->process($link);
        }
    }
}
