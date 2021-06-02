<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 2019-06-28
 * Time: 11:51
 */

namespace app\commands;

use app\components\base\Entity;
use app\models\enum\ErrorType;
use app\models\enum\HoradricCubeStatus;
use app\models\enum\PriceParsedStatus;
use app\models\pool\PriceParsed;
use app\models\reference\BrandFilter;
use app\models\reference\Masks;
use app\models\reference\ParsingProject;
use app\models\register\Error;
use app\models\register\HoradricCube;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function GuzzleHttp\Psr7\build_query;
use yii\db\Exception;
use yii\db\IntegrityException;
use yii\helpers\Json;
use yii\console\Controller;

class MatchingController extends Controller
{
    const YANDEX_TAG = 'Sys.Matching.Yandex.Search';
    const AUTOMATCH_PERCENT = 0.99;

    public $forYandexSearch = [];
    public $yandexMasksId = null;
    public $brandsFilters = [];

    /** @var ParsingProject */
    public $yandexParsingProject = null;

    /** @var Client */
    public $matchingApiClient = null;


    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->yandexMasksId = Masks::find()
            ->andWhere(['name' => self::YANDEX_TAG])
            ->select('id')
            ->scalar();

        $this->yandexParsingProject = ParsingProject::find()
            ->andWhere(['name' => self::YANDEX_TAG])
            ->one();

        $this->brandsFilters = BrandFilter::find()
            ->select('name')
            ->column();

        foreach ($this->brandsFilters as $i => $brandsFilter) {
            $this->brandsFilters[$i] = str_ireplace('/','\/', preg_quote($brandsFilter));
        }

        $this->matchingApiClient = new Client([
            'base_uri'  => 'http://evgenyto.pythonanywhere.com',
            'timeout'   => 300.0,
            'headers'   => [
                'Accept'     => 'application/json',
            ]
        ]);

    }

    public function actionAutomatch() {

        foreach (HoradricCube::find()
                     ->andWhere([
                         'horadric_cube_status_id' => HoradricCubeStatus::STATUS_NEW,
                     ])
                     ->andWhere([
                         '>=',
                         'predict',
                         self::AUTOMATCH_PERCENT,
                     ])
                     ->each() as $hcItem) {
            /** @var HoradricCube $hcItem */
            if ($hcItem->predict && $hcItem->predict >= self::AUTOMATCH_PERCENT) {
                $hcItem->createCompetitorItem();
                $hcItem->auto_match = true;
                $hcItem->save();

                echo "[{$hcItem->predict}] $hcItem->competitor_item_name \n";

                HoradricCube::updateAll([
                    'updated_at'              => new \yii\db\Expression('NOW()'),
                    'horadric_cube_status_id' => HoradricCubeStatus::STATUS_WRONG,
                    'auto_match'              => true,
                ], [
                    'competitor_id' => $hcItem->competitor_id,
                    'item_id'       => $hcItem->item_id,
                    'horadric_cube_status_id' => HoradricCubeStatus::STATUS_NEW
                ]);
            }
        }
    }


    /**
     * @throws \Exception
     */
    public function actionProcessParsed($thread = 0) {

        if ($this->processIsRun('matching/process-parsed '.$thread) > 1) {
            return;
        }

        $thread = intval($thread,10);

        /** @var PriceParsed $parsedPrice */
        foreach (PriceParsed::find()
                     ->andWhere([
                         //'thread'                 => [$thread, $thread + 5],
                         'price_parsed_status_id' => PriceParsedStatus::COLLECTING_NEW,
                     ])
                     ->orderBy(['extracted_at' => SORT_ASC])
                     ->batch(3000) as $parsedPrices) {

            /** @var PriceParsed[][] $byProjects */
            $byProjects = [];

            if ($parsedPrices) {
                echo "\nTotal: ";
                echo count($parsedPrices);

                foreach ($parsedPrices as $parsedPrice) {
                    if (!$parsedPrice->parsing_id) {
                        if (!isset($byProjects['n'])) {
                            $byProjects['n'] = [];
                        }
                        $byProjects['n'][] = $parsedPrice;
                    } else {
                        if (!isset($byProjects[$parsedPrice->parsing_id])) {
                            $byProjects[$parsedPrice->parsing_id] = [];
                        }
                        $byProjects[$parsedPrice->parsing_id][] = $parsedPrice;
                    }
                }

                foreach ($byProjects as $project) {
                    if (!$project[0]->parsingProject){
                        continue;
                    }
                    $projectName = $project[0]->parsingProject->name;
                    $isApiEnabled = $project[0]->parsingProject->matching_api_enabled;

                    echo "\n  Project $projectName  $isApiEnabled : ";
                    echo count($project);


                    foreach ($project as $parsedPrice) {

                        $name = $parsedPrice->competitor_item_name;

                        if ($parsedPrice->out_of_stock) {
                            $parsedPrice->price_parsed_status_id = PriceParsedStatus::COLLECTING_FILTERED_OUT;
                            $parsedPrice->error_message = 'Товар не в наличии';
                            $parsedPrice->save();
                            continue;
                        }

                        if (!$parsedPrice->competitor_item_url) {
                            $parsedPrice->price_parsed_status_id = PriceParsedStatus::COLLECTING_FILTERED_OUT;
                            $parsedPrice->error_message = 'Не найден УРЛ конкурента';
                            $parsedPrice->save();
                            continue;
                        }

                        if (preg_match('/^https?:\/\/market\.yandex/ui', $parsedPrice->competitor_item_url) ||
                            !preg_match('/^http/i', $parsedPrice->competitor_item_url)) {
                            $parsedPrice->price_parsed_status_id = PriceParsedStatus::COLLECTING_FILTERED_OUT;
                            $parsedPrice->error_message = 'Карточка ЯМ вместо магазина конкурента '.$parsedPrice->competitor_item_url;
                            $parsedPrice->save();
                            continue;
                        }

                        // Фильтрация по бренду
//                        if (!BrandFilter::filter($name)) {
//                            $parsedPrice->price_parsed_status_id = PriceParsedStatus::COLLECTING_FILTERED_OUT;
//                            $parsedPrice->error_message = 'Справочник брендов для фильтрации';
//                            $parsedPrice->save();
//                            continue;
//                        }
                        // -----

                        if (!$isApiEnabled) {
                            $this->toYandexSearch($parsedPrice);
                        }
                    }

                    if (!$isApiEnabled) {
                        $this->launchYandexSearchProject($projectName);
                    }
                }
            }

        }

    }

    /**
     * @param $name
     * @return string
     */
    public static function refineNameForApi($name) {
        return preg_replace('/[^a-zа-яё\d_\-]+/iu', ' ', $name);
    }

    private $toApi = [];

    /**
     * @param PriceParsed[] $parsedPrice
     */
    private function matchByApi($parsedPrices) {

        $toApi = [];

        foreach ($parsedPrices as $id => $parsedPrice) {
            $name = self::refineNameForApi($parsedPrice->competitor_item_name);

            // Добавить бренд если был спарсен маской, на случай если отсутствует в имени
            if ($parsedPrice->competitor_item_brand) {
                $name .= ' '. $parsedPrice->competitor_item_brand;
            }

            $toApi[] = [
                'request_id' => $id,
                'product' => $name,
                'price' => $parsedPrice->getFloatPrice(),
            ];
        }

        try {
            $response = $this->matchingApiClient->request('POST', '/post/', [
                'json' => $toApi
            ]);

            $contents = $response->getBody()->getContents();
            
            $matches = Json::decode($contents, true);

            //print_r($contents);

            foreach ($matches as $id => $viItems) {
                if (!isset($parsedPrices[$id])) continue;
                /** @var PriceParsed $parsedPrice */
                $parsedPrice = $parsedPrices[$id];
                try {
                    $url =  str_replace('http:', 'https:', strtolower($parsedPrice->competitor_item_url));
                    if (is_array($viItems) && count($viItems) > 0) {
                        foreach ($viItems as $viItemId => $viItem) {
                            $horadricItem = new HoradricCube;
                            $horadricItem->competitor_shop_name = $parsedPrice->competitor_shop_name;
                            $horadricItem->competitor_id = $parsedPrice->competitor_id;
                            $horadricItem->competitor_item_name = $parsedPrice->competitor_item_name;
                            $horadricItem->competitor_item_price = $parsedPrice->getFloatPrice();
                            $horadricItem->competitor_item_sku = $parsedPrice->competitor_item_sku;
                            $horadricItem->competitor_item_url = $url;
                            $horadricItem->competitor_item_seller = $parsedPrice->competitor_item_seller;
                            $horadricItem->vi_item_url =  isset($viItem['url']) ? $viItem['url'] : null;
                            $horadricItem->vi_item_name = $viItem['name'];
                            $horadricItem->vi_item_brand_name = isset($viItem['brand']) ? $viItem['brand'] : null;
                            $horadricItem->vi_item_price = isset($viItem['price']) ? floatval($viItem['price']) : null;
                            $horadricItem->vi_item_sku = isset($viItem['sku']) ? floatval($viItem['sku']) : null;
                            $horadricItem->predict = isset($viItem['predict_proba']) ? floatval($viItem['predict_proba']) : null;
                            $horadricItem->vi_item_id = $viItemId;
                            $horadricItem->parsing_id = $parsedPrice->parsing_id;
                            $horadricItem->parsing_project_id = $parsedPrice->parsing_project_id;
                            $horadricItem->horadric_cube_status_id = HoradricCubeStatus::STATUS_NEW;

                            if ($horadricItem->validateMatching()) {
                                if ($horadricItem->predict && $horadricItem->predict >= self::AUTOMATCH_PERCENT) {
                                    $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_AUTOMATCHED;
                                    $horadricItem->createCompetitorItem();
                                    $horadricItem->auto_match = true;
                                } else {
                                    $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_TO_MANUAL;
                                }
                                $horadricItem->save();
                                //echo date('i:s')." API-MATCHING: {$horadricItem->predict} \n";
                            } else {
                                $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_FILTERED_OUT;
                                $parsedPrice->error_message = $horadricItem->filter_reason;
                                // echo date('i:s')." API-MATCHING: FILTERED\n";
                            }
                        }
                    }
                    else {
                        $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_FILTERED_OUT;
                        $parsedPrice->error_message = 'API не нашел такой товар: ' . $parsedPrice->competitor_item_name;
                        //echo date('i:s')." API-MATCHING: ITEM NOT FOUND $contents\n";
                    }
                    $parsedPrice->save();
                } catch (\yii\db\Exception $e) {
                    $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_FILTERED_OUT;
                    $parsedPrice->error_message = 'Урл уже есть/был на разборе';
                    // echo date('i:s')." API-MATCHING: EXISTS\n";
                    $parsedPrice->save();
                }
            }
        }
        catch (\Exception $e) {
            Error::logError($e, ErrorType::TYPE_EXTERNAL_API);
            print_r($e->getMessage());
            echo date('i:s')." API-MATCHING: ERROR\n";
        }

    }

    /**
     * @param PriceParsed $parsedPrice
     */
    private function toYandexSearch($parsedPrice) {

        $name = self::refineNameForApi($parsedPrice->competitor_item_name);

        $yandexSearchItem = [
            'url' => 'https://yandex.ru/search/site/?searchid=2243140&text=' . $name ,
            'attributes' => [
                'original_url' => $parsedPrice->competitor_item_url,
                'competitor_shop_name' => $parsedPrice->competitor_shop_name,
                'competitor_item_price' => $parsedPrice->price,
                'competitor_item_sku' => $parsedPrice->competitor_item_sku,
                'competitor_item_name' => $parsedPrice->competitor_item_name,
                'competitor_id' => $parsedPrice->competitor_id,
                'price' => $parsedPrice->price,
            ],
            'perPage' => 5,
            'masks_id' => $this->yandexMasksId
        ];

        $this->forYandexSearch[] = $yandexSearchItem;

        $parsedPrice->price_parsed_status_id = PriceParsedStatus::COLLECTING_IDENTIFY;
        $parsedPrice->save(false);
    }

    /**
     * @param $projectName
     * @throws \Exception
     */
    private function launchYandexSearchProject($projectName) {
        if (count($this->forYandexSearch) > 0) {
            $this->yandexParsingProject->execute([
                'name' => 'Яндекс.Поиск: '.$projectName
            ], false, $this->forYandexSearch);
        }

        $this->forYandexSearch = [];
    }



    /**
     * Проверяем, что процесс запущен
     * @param string $processName Имя процесса
     * @return bool
     * @return boolean
     */
    public function processIsRun($processName)
    {
        $result = [];
        exec("ps aux | grep -v grep | grep \"$processName\"", $result);
        $count = count($result);
        return ceil($count);
    }



    /**
     * Перенести в отдельный контроллер Matching
     */
    public function actionViSearched() {

        if ($this->processIsRun('matching/vi-searched') > 2) {
            return;
        }

        $client = new Client([
            'base_uri'  => 'http://export.vseinstrumenti.ru',
            'timeout'   => 5.0,
            'headers' => [
                'Accept'     => 'application/json',
            ]
        ]);

        /** @var PriceParsed $parsedPrice */
        foreach (PriceParsed::find()
                     ->andWhere([
                         'price_parsed_status_id' => PriceParsedStatus::MATCHING_NEW
                     ])
                     ->orderBy(['extracted_at' => SORT_DESC])
                     ->batch(5000) as $parsedPrices) {

            if ($parsedPrices) {
                foreach ($parsedPrices as $parsedPrice) {
                    //$url = preg_replace('/https?:\/\/[\w]+\.vseinstrumenti\.ru(.*?)/i','$1',$parsedPrice->competitor_item_url);
                    $url =  str_replace('http:', 'https:', strtolower($parsedPrice->competitor_item_url));

                    try {
                        $response = $client->request('GET','/ws/1/json/getGoodShortInfo/', [
                            'query' => [
                                'auth_key' => 'mklY1fd5b',
                                'good_url' => $url,
                            ]
                        ]) ;

                        $contents = $response->getBody()->getContents();

                        $viItems = Json::decode($contents, true);

                        if (count($viItems) > 0) {
                            foreach ($viItems as $viItem) {

                                $horadricItem = new HoradricCube;
                                $horadricItem->competitor_shop_name = $parsedPrice->competitor_shop_name;
                                $horadricItem->competitor_id = $parsedPrice->competitor_id;
                                $horadricItem->competitor_item_name = $parsedPrice->competitor_item_name;
                                $horadricItem->competitor_item_price = $parsedPrice->getFloatPrice();
                                $horadricItem->competitor_item_sku = $parsedPrice->competitor_item_sku;
                                $horadricItem->competitor_item_url = preg_replace('/(^[\n\r\s]+)|([\n\r\s]+$)/i', '', $parsedPrice->original_url);
                                $horadricItem->vi_item_url = $parsedPrice->competitor_item_url;
                                $horadricItem->vi_item_name = $viItem['good_name'];
                                $horadricItem->vi_item_brand_name = $viItem['make_name'];
                                $horadricItem->vi_item_price = floatval($viItem['price']);
                                $horadricItem->vi_item_sku = $viItem['good_sku'];
                                $horadricItem->vi_item_id = $viItem['good_id'];
                                $horadricItem->parsing_id = $parsedPrice->parsing_id;
                                $horadricItem->horadric_cube_status_id = HoradricCubeStatus::STATUS_NEW;

                                if ($horadricItem->validateMatchingYandex()) {
                                    $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_TO_MANUAL;
                                    $horadricItem->save();
                                    echo "ok";
                                } else {
                                    $parsedPrice->error_message = $horadricItem->filter_reason;
                                    $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_FILTERED_OUT;
                                    echo "no";
                                }

                            }
                        }
                        else {
                            $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_FILTERED_OUT;
                            $parsedPrice->error_message = 'Сайт ВИ не нашел такой товар: ' . $url;
                            echo "no";
                        }
                        $parsedPrice->save();
                    } catch (\Exception $e) {
                        print_r($e->getMessage());
                        $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_FILTERED_OUT;
                        $parsedPrice->error_message = $e->getMessage();
                        $parsedPrice->save();
                    } catch (GuzzleException $e) {
                        print_r($e->getMessage());
                    }
                }
            }
        }
    }
}