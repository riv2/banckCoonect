<?php
namespace app\controllers;

use app\components\crud\controllers\ActiveController;
use app\components\DateTime;
use app\models\pool\ParsingProjectProxy;
use app\models\reference\JournalSettings;
use app\models\reference\ParsingProject;
use app\models\register\Proxy;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\Expression;

class ProxyController extends ActiveController
{
    public $modelClass          = 'app\models\register\Proxy';
    public $searchModelClass    = 'app\models\register\Proxy';

    public function actions()
    {
        $result = parent::actions();
        $result['update']['viewAction'] = 'index';
        $result['index'] = null;
        return $result;
    }

    public function actionIndex()
    {
        if (\Yii::$app->request->isPost) {
            $proxies = explode("\n", trim(\Yii::$app->request->post('proxy-list')));
            $parsingProjects = explode(',', \Yii::$app->request->post('proxy-parsing-projects'));
            $until = DateTime::createFromFormat('d.m.Y - d.m.Y', \Yii::$app->request->post('proxy-until'));
            $transaction = \Yii::$app->db->beginTransaction();
            $parsingProjectProxyInserts = [];
            try {
                foreach ($proxies as $proxy) {
                    $proxy = trim($proxy);
                    $newProxy = Proxy::findOne($proxy);
                    if (!$newProxy) {
                        $newProxy = new Proxy();
                        $newProxy->id = $proxy;
                    }
                    $newProxy->until = $until->format('Y-m-d H:i:s');
                    $newProxy->save();
                    foreach ($parsingProjects as $parsingProjectId) {
                        if (trim($parsingProjectId) === '') {
                            continue;
                        }
                        $parsingProjectProxyInserts[] = [
                            'id' => \Yii::$app->getDb()->createCommand("select uuid_generate_v4()")->queryScalar(),
                            'proxy_id' => $proxy,
                            'parsing_project_id' => $parsingProjectId
                        ];
                    }
                }
                if (count($parsingProjectProxyInserts) > 0) {
                    ParsingProjectProxy::getDb()
                        ->createCommand()
                        ->batchInsert(
                            ParsingProjectProxy::tableName(),
                            array_keys($parsingProjectProxyInserts[0]),
                            $parsingProjectProxyInserts
                        )->execute();
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
        $searchModel = $this->getSearchModel();
        $journalSettings = JournalSettings::getUserJournalSettings($searchModel->className());
        $dataProvider = new ActiveDataProvider([
            'query'         => $searchModel->crudSearch(array_merge($_GET,$_POST)),
            'sort'          => $searchModel->getSort(['defaultOrder' => $journalSettings->sortOrder]),
            'pagination'    => [
                'pageSizeLimit'     => [-1, 0x7FFFFFFF],
                'defaultPageSize'   => $journalSettings->per_page,
            ],
        ]);

        if (\Yii::$app->request->get('parsing-projects')) {
            $dataProvider->query
                ->alias('t')
                ->leftJoin(['ppp' => ParsingProjectProxy::tableName()], 'ppp.proxy_id = t.id')
                ->andWhere([
                    'ppp.parsing_project_id' => explode(',', \Yii::$app->request->get('parsing-projects')),
                ])
            ;
        }
        if (\Yii::$app->request->get('is_out')) {
            $dataProvider->query
                ->andWhere('until < NOW()');
        }
        if (\Yii::$app->request->get('is_out_of_7')) {
            $dataProvider->query
                ->andWhere('until > NOW() AND until < (NOW() + interval \'7 days\')');
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $searchModel->crudIndexColumns(),
        ]);
    }

    public function actionDeleteAll($ids = null)
    {
        $ids = array_map('trim', explode(",", trim($ids)));
        \app\models\pool\ProxyParsingProject::deleteAll(['proxy_id' => $ids]);
        \app\models\pool\ParsingProjectProxy::deleteAll(['proxy_id' => $ids]);
        \app\models\pool\ParsingProjectProxyBan::deleteAll(['proxy_id' => $ids]);
        \app\models\register\Proxy::deleteAll(['id' => $ids]);

        return $this->redirect('index');
    }

    public function actionPublicAll($ids = null)
    {
        $ids = explode(',', $ids);
        Proxy::updateAll([
            'is_public' => new \yii\db\Expression('NOT(is_public)')
        ], ['id' => $ids]);
        return $this->redirect('index');
    }
}