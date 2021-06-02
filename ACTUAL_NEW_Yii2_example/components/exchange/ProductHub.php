<?php

namespace app\components\exchange;

use app\components\DateTime;
use app\models\cross\CategoryCategory;
use app\models\cross\CategoryItem;
use app\models\enum\Status;
use app\models\reference\Brand;
use app\models\reference\Category;
use app\models\reference\Item;
use app\models\register\ExchangeImport;
use GuzzleHttp\Client;
use yii\base\InvalidParamException;
use yii\helpers\Json;

class ProductHub extends Exchange
{
    public $url;
    public $username;
    public $password;
    public $headers;
    public $site_guid;
    public $lastImportItems         = '2016-06-01 00:00:00';
    public $lastImportBrands        = '1988-01-01 00:00:00';
    public $lastImportCategories    = '1988-01-01 00:00:00';

    /** @var  Client $client */
    private $client     = null;
    private $options    = [];

    public static function systemName() {
        return 'ProductHub';
    }
    /**
     * @inheritdoc
     */
    public function getLabels() {
        return array_merge([
            'url'                       => 'URL',
            'site_guid'                 => 'GUID Сайта',
            'lastImportItems'           => 'Последний импорт Товаров',
            'lastImportBrands'          => 'Последний импорт Брендов',
            'lastImportCategories'      => 'Последний импорт Категорий',
        ], parent::getLabels());
    }

    public function deduplicate($guid) {
        $config = $this->config();
        try {
            $r = $this->client->request('GET', $config['Duplicate']['url'].'/'.$guid, $this->options);
            if ($r->getStatusCode() == 200){
                $data = Json::decode($r->getBody()->getContents());
                return $data['main_product_guid'];
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }

    public function init()
    {
        parent::init();
        $this->client = new Client([
            'base_uri' => $this->url,
            'timeout'  => 60.0,
        ]);
        $this->options = [
            'headers'   => $this->headers,
            'auth'      => [
                $this->username,
                $this->password
            ]
        ];
    }

    /**
     * Настройки урлов
     * @return array
     */
    public function config() {
        return [
            'Items' => [
                'url_by_date'   => 'products/'.$this->lastImportItems,
                'url_by_id'     => 'products',
            ],
            'Categories' => [
                'url_by_date'   => 'rubrics/'.$this->lastImportCategories.'/'.$this->site_guid.'',
                'url_by_id'     => 'rubrics',
            ],
            'Brands' => [
                'url_by_date'   => 'manufacturers/'.$this->lastImportBrands,
                'url_by_id'     => 'manufacturers',
            ],
            'Duplicate' => [
                'url' => 'double_info'
            ]
        ];
    }

    /**
     * @param array|ExchangeImport[] $ids
     * @return Item[]
     * @throws \Exception
     */
    public function checkItems($ids = []) {
        return Item::find()->andWhere(['id' => $ids])->all();
    }

    /**
     * @param array|ExchangeImport[] $ids
     * @return Category[]
     * @throws \Exception
     */
    public function checkCategories($ids = []) {
        return Category::find()->andWhere(['id' => $ids])->all();
    }

    /**
     * @param array|ExchangeImport[] $ids
     * @return Brand[]
     * @throws \Exception
     */
    public function checkBrands($ids = []) {
        return Brand::find()->andWhere(['id' => $ids])->all();
    }

    /**
     * @param array|ExchangeImport[] $ids
     * @return Item[]
     * @throws \Exception
     */
    public function importItems($ids = []) {
        return $this->importHub('Items', $ids);
    }

    /**
     * @param array|ExchangeImport[] $ids
     * @return Category[]
     * @throws \Exception
     */
    public function importCategories($ids = []) {
        return $this->importHub('Categories', $ids);
    }

    /**
     * @param array|ExchangeImport[] $ids
     * @return Brand[]
     * @throws \Exception
     */
    public function importBrands($ids = []) {
        return $this->importHub('Brands', $ids);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function importItemsEnqueue() {
        return $this->importHubEnqueue('Items');
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function importCategoriesEnqueue() {
        return $this->importHubEnqueue('Categories');
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function importBrandsEnqueue() {
        return $this->importHubEnqueue('Brands');
    }

    private static $categoriesCache = [];


    /**
     * Импорт номенклатуры товаров
     * @param array $item
     * @return Item[]
     * @throws \Exception
     */
    public function importItemsOne($item) {
        $model = Item::findOne($item['guid']);
        if (!$model) {
            $model                  = new Item;
            $model->id              = $item['guid'];
        }
        $model->name                = $item['name'];
        $status = intval($item['status'], 10);
//0 - активный
//1 - удален
//2 - дубль
//3 - помечен на удаление (считай активный пока, но его нельзя заказать у поставщика, но есть остатки на нашем складе)

        switch ($status) {
            case 1:
                $model->status_id = Status::STATUS_REMOVED;
                $model->is_duplicate = false;
                break;
            case 2:
                $model->status_id = Status::STATUS_DISABLED;
                $model->is_duplicate = true;
                break;
            case 3:
            case 0:
            default:
                $model->status_id = Status::STATUS_ACTIVE;
                $model->is_duplicate = false;
                break;
        }

        $model->status_id           = (intval($item['status'], 10) == 0) ? Status::STATUS_ACTIVE : Status::STATUS_REMOVED;
        //$model->wtis_index          = $item['wtis_id'];
        $model->site_index          = $item['site_id'];
        $model->vendor_type_text    = $item['vendor_type_text'];
        $model->sku                 = $item['sku'];
        $model->is_expendable       = intval($item['is_rashodka'],10)?true:false;
        $model->is_liquid           = intval($item['is_liquid'],10)?true:false;

        $model->brand_id            = $item['manufacturer_guid'];
        # Подгружаем бренд
//        $brands = $this->importHub('Brands', [$item['manufacturer_guid']]);
//        foreach ($brands as $brand) {
//            $model->brand_id            = $brand->id;
//        }

        $model->save(false);

        # Подгружаем рубрики
        CategoryItem::deleteAll([
            'item_id' => $model->id
        ]);
        $rubricGuids = [];
        foreach($item['rubrics'] as $rubric) {
            $rubricGuids[]  = $rubric['guid'];
        }

        $categories = [];
        $toLoad = [];

        foreach ($rubricGuids as $guid) {
            if (isset(self::$categoriesCache[$guid])) {
                $categories[] = self::$categoriesCache[$guid];
            } else {
                $toLoad[] = $guid;
            }
        }

        if (count($toLoad) > 0) {
            $notCachedRubrics = Category::find()
                ->andWhere(['id' => $toLoad])
                ->indexBy('id')
                ->all();
            foreach ($notCachedRubrics as $cId => $exc) {
                self::$categoriesCache[$cId] = $exc;
                $categories[] = $exc;
            }
            $toLoad = array_diff($toLoad, array_keys($notCachedRubrics));
        }

        if (count($toLoad) > 0) {
            $categories = array_merge($categories, $this->importHub('Categories', $toLoad));
        }

        if ($categories) {
            /** @var Category $category */
            foreach ($categories as $category) { 
                CategoryItem::createItemCategoryTree($model->id, $category->id, $category->is_top, $category->status_id);
            }
        }

        return $model;
    }

    /**
     * Импорт рубрик
     * @param array $item
     * @return Category[]
     * @throws \Exception
     */
    public function importCategoriesOne($item) {
        $model = Category::findOne($item['guid']);

        if (!$model) {
            $model        = new Category;
            $model->id    = $item['guid'];
        }

        # Подгружаем рубрики
        CategoryCategory::deleteAll([
            'child_id' => $model->id
        ]);



        $rubricGuids = [];
        foreach ($item['parents'] as $rubric) {
            if (intval($rubric['default'],10) === 1) {
                $rubricGuids[] = $rubric['guid'];
            }
        }

        if (count($rubricGuids) > 0) {
            $categories = $this->importHub('Categories', $rubricGuids);
            if ($categories) {
                foreach ($categories as $category) {
                    $categoryCategory = new CategoryCategory();
                    $categoryCategory->child_id = $model->id;
                    $categoryCategory->parent_id = $category->id;
                    $categoryCategory->save();
                }
            }
            $model->is_top = false;
        } else {
            $model->is_top = true;
        }

        $model->name                = $item['name'];
        $is_active                  = intval($item['is_active'], 10);
        $is_enabled                 = intval($item['is_enabled'], 10);

        //Грусть печаль
        if ($model->id != '7c8dbf9f-9151-4184-a7e8-284814bcc2de') {
            $model->status_id = $is_active ? ($is_enabled ? Status::STATUS_ACTIVE : Status::STATUS_DISABLED) : Status::STATUS_REMOVED;
        }

        if ($model->validate()) {
            $model->save();
            return $model;
        } else {
            throw new \Exception(Json::encode($model->getErrors()));
        }
    }

    /**
     * Импорт брендов
     * @param array $item
     * @return Brand[]
     * @throws \Exception
     */
    public function importBrandsOne($item) {
        $brand = Brand::findOne($item['guid']);
        if (!$brand) {
            $brand        = new Brand;
            $brand->id    = $item['guid'];
        }
        $brand->name                = $item['name'];
        $brand->status_id           = (intval($item['status'], 10) == 0) ? Status::STATUS_ACTIVE : Status::STATUS_REMOVED;
        $brand->save();
        return $brand;
    }

    
    public function importHubEnqueue($remoteEntity) {
        $now                = (new DateTime())->format('Y-m-d H:i:s');
        $config             = $this->config();
        $response           = $this->client->request('GET', $config[$remoteEntity]['url_by_date'], $this->options);
        $result             = Json::decode($response->getBody()->getContents(), true);
//        if (!isset($result['items'])) {
//            throw new \Exception("Не верный формат ответа от PHub. [{$this->url}{$config[$remoteEntity]['url_by_date']}]");
//        }
        if (count($result) > 0) {
            $date = 'lastImport'.$remoteEntity;
            $this->$date = $now;
        }
        return $result;
    }

    /**
     * @param $remoteEntity
     * @param array|ExchangeImport[] $identifiers
     * @return array
     * @throws \Exception
     */
    private function importHub($remoteEntity, $identifiers = []) {
        $exchangeImports = null;

        if (!is_array($identifiers)) {
            $identifiers = [$identifiers];
        }
        if (count($identifiers) > 0 && end($identifiers) instanceof ExchangeImport) {
            $exchangeImports    = [];
            $ids                = [];
            foreach ($identifiers as $exchangeImport) {
                $ids[] = $exchangeImport->remote_id;
                $exchangeImports[$exchangeImport->remote_id] = $exchangeImport;
            }
        } else {
            $ids = $identifiers;
        }

        if (count($ids) > 0) {

            $config     = $this->config();
            $response   = $this->client->request('POST', $config[$remoteEntity]['url_by_id'], array_merge($this->options, [
                'json' => $ids
            ]));

            $result = Json::decode($response->getBody()->getContents(), true);

//            if (!isset($result['items'])) {
//                throw new \Exception("Не верный формат ответа от PHub. [{$this->url}/{$config[$remoteEntity]['url_by_id']}]");
//            }
            $importedItems = [];
            $ids = array_combine($ids, $ids);
            foreach ($result as $item) {

                unset($ids[$item['guid']]);
                $importedItem = $this->importOne($remoteEntity, $item['guid'], $item);
                if ($importedItem) {
                    $importedItems[$importedItem->id] = $importedItem;
                }
            }
            foreach ($ids as $id) {
                $this->importOne($remoteEntity,$id, null);
            }
            return $importedItems;
        }
        return [];
    }
}