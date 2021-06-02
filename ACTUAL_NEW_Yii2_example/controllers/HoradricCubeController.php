<?php
namespace app\controllers;

use app\components\crud\controllers\ActiveController;
use app\components\DataProvider;
use app\models\cross\CategoryCategory;
use app\models\cross\CategoryItem;
use app\models\enum\HoradricCubeStatus;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\reference\Brand;
use app\models\reference\Category;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\register\HoradricCube;
use yii;
use yii\helpers\Html;

class HoradricCubeController extends ActiveController
{
    public $modelClass          = "app\\models\\register\\HoradricCube";
    public $searchModelClass    = "app\\models\\register\\HoradricCube";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'update'        => null,
            'view'          => null,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actionManual($competitors = null , $brands = null, $categories = null)
    {
        $groups = HoradricCube::find()
            ->alias('hc')
            ->andWhere([
                'horadric_cube_status_id' => HoradricCubeStatus::STATUS_NEW,
            ])
            ->select(['competitor_id','hc.item_id','MIN(sales_rank) sales_rank'])
            //->orderBy(new yii\db\Expression('MAX(updated_at) ASC'))
            ->orderBy(new yii\db\Expression('MIN(sales_rank) ASC, MAX(updated_at) ASC'))
            ->groupBy(['competitor_id','hc.item_id'])
            ->asArray();

        if ($competitors) {
            $groups->andFilterWhere(['hc.competitor_id' => explode(',', $competitors)]);
        }
        if ($brands) {
            $groups->andFilterWhere(['hc.brand_id' => explode(',', $brands)]);
        }
        if ($categories) {
            $groups->innerJoin(['ci' => CategoryItem::tableName()], 'ci.item_id = hc.item_id')
                ->andFilterWhere(['ci.category_id' => explode(',', $categories)]);
        }

        $dataProvider = new DataProvider([
            'query' => $groups,
        ]);

        $dataProvider->countBy = 'item_id';
        $dataProvider->noKeys = true;
        $dataProvider->pagination->pageSize = 200;

        $groups = $dataProvider->getModels();

        foreach ($groups as $i => $group) {
            $groups[$i]['matches']      = HoradricCube::find()
                ->andWhere([
                    'horadric_cube_status_id' => HoradricCubeStatus::STATUS_NEW,
                    'competitor_id' => $group['competitor_id'],
                    'item_id'       => $group['item_id'],
                ])
                ->orderBy(['percent' => SORT_ASC, 'sales_rank' => SORT_ASC])
                ->all();
            $groups[$i]['competitor']   = Competitor::findOne($group['competitor_id']);
            $groups[$i]['itemName']     = $groups[$i]['matches'][0]->renderViItemName();
        }

        $competitors = Yii::$app->cache->get('horadric_competitors');

        if (!$competitors) {
            $competitors = Competitor::find()
                ->andWhere([
                    'id' => HoradricCube::find()
                        ->andWhere([
                            'horadric_cube_status_id' => HoradricCubeStatus::STATUS_NEW,
                        ])
                        ->select('competitor_id')
                ])
                ->orderBy(['name' => SORT_ASC])
                ->select(['id','text' => 'name'])
                ->asArray()
                ->all();

            Yii::$app->cache->set('horadric_competitors',$competitors, 180);
        }

        $brands = Yii::$app->cache->get('horadric_brands');
        if (!$brands) {
            $brands = Brand::find()
                ->andWhere([
                    'id' => HoradricCube::find()
                        ->andWhere([
                            'horadric_cube_status_id' => HoradricCubeStatus::STATUS_NEW,
                        ])
                        ->select('brand_id')
                ])
                ->orderBy(['name' => SORT_ASC])
                ->select(['id','text' => 'name'])
                ->asArray()
                ->all();
            Yii::$app->cache->set('horadric_brands',$brands, 180);
        }

        $categories = Yii::$app->cache->get('horadric_categories');

        if (!$categories) {
            $categories = CategoryItem::find()
                ->alias('ci')
                ->andWhere([
                    'ci.item_id' => HoradricCube::find()
                        ->andWhere([
                            'horadric_cube_status_id' => HoradricCubeStatus::STATUS_NEW,
                        ])
                        ->select('item_id')
                        ->groupBy('item_id'),
                    'c.is_top' => true,
                ])
                ->innerJoin(['c' => Category::tableName()], 'c.id = ci.category_id')
                ->orderBy(['c.name' => SORT_ASC])
                ->select(['c.id','text' => 'c.name'])
                ->groupBy(['c.id'])
                ->asArray()
                ->all();

            Yii::$app->cache->set('horadric_categories', $categories, 180);
        }

        return $this->render('manual',[
            'competitors'   => $competitors,
            'categories'    => $categories,
            'brands'        => $brands,
            'groups'        => $groups,
            'dataProvider'  => $dataProvider
        ]);
    }

    /**
     * @param $id
     * @return array
     */
    public function actionLaterAll($item_id, $competitor_id) {
        \Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        HoradricCube::updateAll([
            'updated_at'                => new yii\db\Expression('NOW()')
        ], [
            'competitor_id'             => $competitor_id,
            'item_id'                   => $item_id,
            'horadric_cube_status_id'   => HoradricCubeStatus::STATUS_NEW
        ]);
        return ['ok' => true];
    }

    /**
     * @param $id
     * @return array
     */
    public function actionOkAll($id) {
        \Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        /** @var HoradricCube $identifiedItem */
        $identifiedItem = HoradricCube::findOne($id);

        if ($identifiedItem) {
            $newId = $identifiedItem->createCompetitorItem();
            if ($newId) {
                $identifiedItem->horadric_cube_status_id = HoradricCubeStatus::STATUS_MATCHED;
                $identifiedItem->save();

                HoradricCube::updateAll([
                    'updated_at'              => new yii\db\Expression('NOW()'),
                    'horadric_cube_status_id' => HoradricCubeStatus::STATUS_WRONG,
                    'updated_user_id'         => Yii::$app->user->id,
                ], [
                    'competitor_id' => $identifiedItem->competitor_id,
                    'item_id'       => $identifiedItem->item_id,
                    'horadric_cube_status_id' => HoradricCubeStatus::STATUS_NEW
                ]);
                return ['ok' => true, 'id' => $newId];
            }
            return ['ok' => false, 'error' => 'no competitor'];
        }

        return ['ok' => false,'error'=>'no horadric'];
    }
    /**
     * @param $id
     * @return array
     */
    public function actionWrongAll($item_id, $competitor_id) {
        \Yii::$app->response->format = yii\web\Response::FORMAT_JSON;

        HoradricCube::updateAll([
            'updated_at'              => new yii\db\Expression('NOW()'),
            'horadric_cube_status_id' => HoradricCubeStatus::STATUS_WRONG,
            'updated_user_id'         => Yii::$app->user->id,
        ], [
            'competitor_id' => $competitor_id,
            'item_id'       => $item_id,
            'horadric_cube_status_id' => HoradricCubeStatus::STATUS_NEW
        ]);

        return ['ok' => true];
    }

    /**
     * @param $id
     * @return array
     */
    public function actionOk($id) {
        \Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        /** @var HoradricCube $identifiedItem */
        $identifiedItem = HoradricCube::findOne($id);

        if ($identifiedItem) {
            $newId = $identifiedItem->createCompetitorItem();
            if ($newId) {
                $identifiedItem->horadric_cube_status_id = HoradricCubeStatus::STATUS_MATCHED;
                $identifiedItem->save();
                return ['ok' => true, 'id' => $newId];
            }
            return ['ok' => false, 'error' => 'no competitor'];
        }

        return ['ok' => false,'error'=>'no horadric'];
    }



    /**
     * @param $id
     * @return array
     */
    public function actionRollback($id) {
        \Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        /** @var HoradricCube $identifiedItem */
        $identifiedItem = HoradricCube::findOne($id);

        if ($identifiedItem) {
            $identifiedItem->rollbackCompetitorItem();
            $identifiedItem->horadric_cube_status_id = HoradricCubeStatus::STATUS_NEW;
            $identifiedItem->save();
            return ['ok' => true];
        }

        return ['ok' => false,'error'=>'no horadric'];
    }

    /**
     * @param $id
     * @return array
     */
    public function actionWrong($id) {
        \Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        /** @var HoradricCube $identifiedItem */
        $identifiedItem = HoradricCube::findOne($id);

        if ($identifiedItem) {
            $identifiedItem->horadric_cube_status_id = HoradricCubeStatus::STATUS_WRONG;
            $identifiedItem->save();
            return ['ok' => true];
        }

        return ['ok' => false,'error'=>'no horadric'];
    }


    /**
     * Кнопки в колонке действий Index'а
     * @param $actionColumn
     * @return array
     */
    public function indexActionButtons($actionColumn) {
        $controller = $this;
        return [
            'ok' => function ($url, $model, $key) use ($controller, $actionColumn) {
                if ($model->horadric_cube_status_id != HoradricCubeStatus::STATUS_NEW) {
                    return null;
                }
                return Html::a('<i class="fa fa-check-circle-o"></i> Да', ['#'], [
                    'class' => 'btn btn-success matching-ok',
                    'data-pjax' => 0
                ]);
            },
            'wrong' => function ($url, $model, $key) use ($controller, $actionColumn) {
                if ($model->horadric_cube_status_id != HoradricCubeStatus::STATUS_NEW) {
                    return null;
                }
                return Html::a('<i class="fa fa-close"></i> Нет', ['#'], [
                    'class' => 'btn btn-danger matching-wrong',
                    'data-pjax' => 0
                ]);
            },
            'rollback' => function ($url, $model, $key) use ($controller, $actionColumn) {
                if ($model->horadric_cube_status_id != HoradricCubeStatus::STATUS_MATCHED &&
                    $model->horadric_cube_status_id != HoradricCubeStatus::STATUS_WRONG ) {
                    return null;
                }
                return Html::a('<i class="fa fa-undo"></i> Вернуть на разбор', ['#'], [
                    'class' => 'btn btn-warning matching-rollback',
                    'data-pjax' => 0
                ]);
            },
//            'look' => function ($url, $model, $key) use ($controller, $actionColumn) {
//                return Html::a('<i class="fa fa-eye"></i> Смотреть', ['#'], [
//                    'class' => 'btn btn-default matching-look',
//                    'data-matching' => Json::encode($model->toArray()),
//                    'onclick'   => "$.get('/matching/ok?id={$model->id}'); return false;",
//                ]);
//            },
            'view'   => function ($url, $model, $key) use ($controller, $actionColumn) {
                return null;
            },
            'update' => function ($url, $model, $key) use ($controller, $actionColumn) {
                return null;
            },
            'delete' => function ($url, $model, $key) use ($controller, $actionColumn) {
                return null;
            },
        ];
    }
}