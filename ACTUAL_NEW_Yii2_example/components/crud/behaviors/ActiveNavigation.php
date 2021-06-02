<?php
/**
 * @link http://netis.pl/
 * @copyright Copyright (c) 2015 Netis Sp. z o. o.
 */

namespace app\components\crud\behaviors;

use app\components\crud\actions\Action;
use netis\crud\db\ActiveQuery;
use netis\crud\db\ActiveRecord;
use yii;

/**
 * ActiveNavigation provides method to build navigation items for a CRUD controller.
 * Those include breadcrumbs and context menus.
 * @package netis\crud\crud
 */
class ActiveNavigation extends \netis\crud\crud\ActiveNavigation
{

    /**
     * @var array Cached index  route
     */
    private $indexRoute;
    /**
     * @param \yii\base\Action $action
     * @param ActiveRecord $model
     * @return array
     */
    public function getBreadcrumbs(\yii\base\Action $action, $model)
    {
        $breadcrumbs = [];
        $id = null;
        if ($model !== null && !$model->isNewRecord) {
            $id = $action instanceof Action
                ? $action->exportKey($model->getPrimaryKey(true))
                : implode(';', $model->getPrimaryKey(true));
        }

        if ($action->id == 'index') {
            $breadcrumbs[] = $model->getCrudLabel();
        }
        if ($action->id == 'import') {
            $breadcrumbs[] = [
                'label' => $model->getCrudLabel('index'),
                'url' => $this->getIndexRoute($action),
            ];
            $breadcrumbs[] = $model->getCrudLabel('import');
        }
        if ($action->id == 'update') {
            $breadcrumbs[] = [
                'label' => $model->getCrudLabel('index'),
                'url' => $this->getIndexRoute($action),
            ];
            if (!$model->isNewRecord) {
                $breadcrumbs[] = [
                    'label' => $model->__toString(),
                    'url' => ['view', 'id' => $id],
                ];
                $breadcrumbs[] = Yii::t('app', 'Update');
            } else {
                $breadcrumbs[] = $model->getCrudLabel('create');
            }
        }
        if ($action->id == 'view' || $action->id == 'print') {
            $breadcrumbs[] = [
                'label' => $model->getCrudLabel('index'),
                'url' => $this->getIndexRoute($action),
            ];
            $breadcrumbs[] = $model->__toString();
        }
        return $breadcrumbs;
    }

    /**
     * @param \yii\base\Action $action
     * @return array
     */
    private function getIndexRoute($action)
    {
        if ($this->indexRoute !== null) {
            return $this->indexRoute;
        }

        if ($action instanceof Action) {
            $searchModel = $action->getSearchModel();
            /** @var ActiveQuery $query */
            $query = $action->getQuery($searchModel);
            if ($query instanceof ActiveQuery) {
                return $this->indexRoute = ['index', 'query' => implode(',', $query->getActiveQueries())];
            }
        }
        return $this->indexRoute = ['index'];
    }

}
