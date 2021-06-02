<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\enum\HoradricCubeStatus;
use app\models\enum\PriceParsedStatus;
use app\models\pool\PriceParsed;
use app\models\reference\ConsoleTask;
use app\models\register\HoradricCube;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use yii\base\BaseObject;
use yii\helpers\Json;

class ViSearchedTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
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