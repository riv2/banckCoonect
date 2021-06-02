<?php

namespace app\components\exchange;

use app\components\DateTime;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\pool\PriceRefined;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\Item;
use app\models\reference\PriceFormerType;
use app\models\reference\ProjectCompetitor;
use GuzzleHttp\Client;
use yii;
use yii\helpers\Json;

class PriceFormer extends Exchange
{
    public $url;
    public $username;
    public $password;
    public $headers;

    public $lastImportPriceFormerTypes;

    /** @var  Client $client */
    private $client     = null;
    private $options    = [];


    /**
     * @inheritdoc
     */
    public static function systemName() {
        return 'PriceFormer';
    }

    /**
     * @inheritdoc
     */
    public function getLabels() {
        return array_merge([
            'url'                           => 'URL',
            'headers'                       => 'HTTP HEADERS',
            'lastImportPriceFormerTypes'    => 'Последний импорт',
        ], parent::getLabels());
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->client = new Client([
            'base_uri' => $this->url,
            'timeout'  => 600.0,
        ]);
        $this->options = [
            'headers'   => $this->headers,
            'auth'      => [
                $this->username,
                $this->password
            ]
        ];
    }

    public function exportPrices($options) {

        if (!isset($options['live_date']) || !($options['live_date'] instanceof DateTime)) {
            throw new yii\base\InvalidValueException("Не верный формат даты ");
        }

        if (!isset($options['prices']) || !is_array($options['prices'])) {
            throw new yii\base\InvalidValueException("Не верный формат цены. Нужен [[item_id => price],...]");
        }

        if (!isset($options['price_former_type_id']) || !PriceFormerType::find()->andWhere(['id' => $options['price_former_type_id']])->exists()) {
            throw new yii\base\InvalidValueException("Не верный тип выгружаемой цены.");
        }

        $prices             = $options['prices'];
        $priceFormerTypeId  = $options['price_former_type_id'];
        /** @var DateTime $liveDate */
        $liveDate           = $options['live_date'];

        $goods = [];

        foreach ($prices as $itemId => $price) {
            $goods[] = [
                'GUID'  => $itemId,
                'price' => round($price)
            ];
        }
        $response = $this->client->post('pricestat_import', array_merge($this->options,[
            'json' => [
                'goods'             => $goods,
                'user_guid'         => 0,
                'live_date'         => $liveDate->format('Y-m-d'),
                'price_type_guid'   => $priceFormerTypeId
            ],
            'timeout'  => 120.0,
        ]));

        return $response;
    }

    public function importPricesSupply($ids) {
        $response = $this->client->post('pricestat_goods_info', array_merge($this->options,[
            'json' => ['goods' => $ids]
        ]));
        $result = Json::decode($response->getBody()->getContents(), true);
        foreach ($result['data'] as $product) {
            $this->importOne('PricesSupply', $product['GUID'], $product);
        }
        return $result['data'];
    }

    public function importPricesSupplyOne($product) {
        Item::updateAll([
            'price_supply'              => $product['purchase_price'],
            'price_recommended_retail'  => $product['rrc'],
            'price_weighted'            => $product['weighted_price']
        ], [
            'id' => $product['GUID']
        ]);
        return $product;
    }

    public function importPricesVi($ids) {
        $response = $this->client->post('pricestat_goods_purchase_price', array_merge($this->options,[
            'json' => ['goods' => $ids]
        ]));
        $result = Json::decode($response->getBody()->getContents(), true);
        foreach ($result['data'] as $product) {
            $this->importOne('PricesVi', $product['GUID'], $product);
        }
        return $result['data'];
    }

    static $viCompetitorGuid = null;

    public function importPricesViOne($product) {
        /** Костыль, заполнение цен ВИ МСК в Обработанные цены */
        if (!self::$viCompetitorGuid) {
            self::$viCompetitorGuid = Competitor::find()->andWhere(['name' => 'ВсеИнструменты.ру'])->select('id')->scalar();
        }

        $priceRefinedVi = new PriceRefined();
        $priceRefinedVi->loadDefaultValues();
        $priceRefinedVi->extracted_at = new DateTime();
        $priceRefinedVi->price = $product['vicost'];
        $priceRefinedVi->regions = [1];
        $priceRefinedVi->item_id = $product['GUID'];
        $priceRefinedVi->competitor_id = self::$viCompetitorGuid;
        $priceRefinedVi->source_id = Source::SOURCE_WEBSITE;
        $priceRefinedVi->out_of_stock = false;
        $priceRefinedVi->save();
        /** --------------------- */

        Item::updateAll([
            'price_supply'              => $product['price'],
            'price_default'             => $product['vicost'],
        ], [
            'id' => $product['GUID']
        ]);
        return $product;
    }


    public function importPriceFormerTypesOne($item)
    {
        $priceFormerType = PriceFormerType::findOne($item['guid']);

        if (!$priceFormerType) {
            $priceFormerType            = new PriceFormerType;
            $priceFormerType->id        = $item['guid'];
        }
        $priceFormerType->name      = $item['name'];
        $priceFormerType->status_id = (intval($item['active']) == 1) ? Status::STATUS_ACTIVE : Status::STATUS_REMOVED;

        if ($priceFormerType->validate()) {
            $priceFormerType->save();
            return $priceFormerType;
        } else {
            $er = print_r($priceFormerType->getErrors(),true);
            echo $er;
            throw new \Exception($er);
        }
    }

    public function importPriceFormerTypes() {
        $this->lastImportPriceFormerTypes = (new DateTime())->format(DateTime::DB_DATETIME_FORMAT);
        $response = $this->client->request('GET', 'price_type', $this->options);
        $result = Json::decode($response->getBody()->getContents(),true);
        $types = [];
        foreach ($result as $type) {
            $types[$type['guid']] = $this->importOne('PriceFormerTypes', $type['guid'], $type);
        }
        return $types;
    }
}