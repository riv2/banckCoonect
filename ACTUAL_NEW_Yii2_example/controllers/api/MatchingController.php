<?php
namespace app\controllers\api;

use app\models\enum\HoradricCubeStatus;
use app\models\enum\PriceParsedStatus;
use app\models\pool\PriceParsed;
use app\models\register\HoradricCube;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;

class MatchingController extends Controller
{
    const AUTOMATCH_PERCENT = 0.99;

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    /**
     * @param int $limit
     * @return array
     */
    public function actionGetMatch($limit = 10)
    {
        /** @var PriceParsed[] $parsedPrices */
        $parsedPrices = PriceParsed::find()
            ->andWhere([
                'price_parsed_status_id' => PriceParsedStatus::COLLECTING_API,
            ])
            ->orderBy(['extracted_at' => SORT_ASC])
            ->limit($limit)
            ->all();

        $result = [];
        foreach ($parsedPrices as $parsedPrice) {
            $name = $parsedPrice->competitor_item_name;

            // Добавить бренд если был спарсен маской, на случай если отсутствует в имени
            if ($parsedPrice->competitor_item_brand) {
                $name .= ' ' . $parsedPrice->competitor_item_brand;
            }

            $result[$parsedPrice->id] = [
                'goods_name' => trim($name),
                'price' => $parsedPrice->getFloatPrice(),
            ];
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function actionSetMatch()
    {
        $inputData = file_get_contents('php://input');
        $data = mb_strlen($inputData) > 0 ? Json::decode($inputData, true) : [];

        if (count($data) === 0) {
            return true;
        }

        /** @var PriceParsed[] $parsedPrices */
        $parsedPrices = PriceParsed::find()->andWhere(['id' => array_keys($data)])->all();

        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            foreach($parsedPrices as $parsedPrice) {
                $viItems = $data[$parsedPrice->id];
                $parsedPrice->error_message = '';
                if (is_array($viItems) && count($viItems) > 0) {
                    foreach ($viItems as $viItemId => $viItem) {
                        $horadricItem = new HoradricCube;
                        $horadricItem->competitor_shop_name     = $parsedPrice->competitor_shop_name;
                        $horadricItem->competitor_id            = $parsedPrice->competitor_id;
                        $horadricItem->competitor_item_name     = $parsedPrice->competitor_item_name;
                        $horadricItem->competitor_item_price    = $parsedPrice->getFloatPrice();
                        $horadricItem->competitor_item_sku      = $parsedPrice->competitor_item_sku;
                        $horadricItem->competitor_item_url      = str_replace('http:', 'https:', strtolower($parsedPrice->competitor_item_url));
                        $horadricItem->competitor_item_seller   = $parsedPrice->competitor_item_seller;
                        $horadricItem->predict                  = isset($viItem['predict_proba']) ? floatval($viItem['predict_proba']) : null;
                        $horadricItem->vi_item_id               = $viItemId;
                        $horadricItem->vi_item_name             = $viItem['name'];
                        $horadricItem->vi_item_url              = $viItem['url'];
                        $horadricItem->parsing_id               = $parsedPrice->parsing_id;
                        $horadricItem->parsing_project_id       = $parsedPrice->parsing_project_id;
                        $horadricItem->horadric_cube_status_id  = HoradricCubeStatus::STATUS_NEW;

                        if ($horadricItem->validateMatching()) {
                            if ($horadricItem->predict && $horadricItem->predict >= self::AUTOMATCH_PERCENT) {
                                $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_AUTOMATCHED;
                                $horadricItem->createCompetitorItem();
                                $horadricItem->auto_match = true;
                            } else {
                                $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_TO_MANUAL;
                            }
                            $horadricItem->save();
                        } else {
                            $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_FILTERED_OUT;
                            $parsedPrice->error_message = $horadricItem->filter_reason;
                        }
                    }
                } else {
                    $parsedPrice->price_parsed_status_id = PriceParsedStatus::MATCHING_FILTERED_OUT;
                    $parsedPrice->error_message = 'API не нашел такой товар: ' . $parsedPrice->competitor_item_name . '(' . print_r($viItems, true) . ')';
                }
                $parsedPrice->save();
            }
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
        return true;
    }
}