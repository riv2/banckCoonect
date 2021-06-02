<?php
namespace app\controllers\api;

use app\models\cross\ParsingProjectMasks;
use app\models\cross\ParsingProjectRegion;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\pool\ParsingError;
use app\models\reference\CompetitorItem;
use app\models\reference\Masks;
use app\models\reference\ParsingProject;
use app\models\reference\Robot;
use app\models\register\Parsing;
use yii\db\Expression;
use yii\db\JsonExpression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

class ParsingController extends Controller
{

    /**
     *
     * @param string $id        ID проекта парсинга
     * @param int $limit        Ограничить кол-во урлов этим значением
     * @param string $regions   Регионы через запятую
     * @return array
     * @throws \Exception
     */
    public function actionRunTestProject($id, $limit = 1000, $regions = '1') {

        \Yii::$app->response->format = Response::FORMAT_JSON;

        /** @var ParsingProject $parsingProject */
        $parsingProject = ParsingProject::find()
            ->andWhere([
                'id'        => $id,
                'status_id' => Status::STATUS_ACTIVE
            ])
            ->one();

        $parsingId = $parsingProject->execute([
            'regions' => $regions,
            'limit'   => $limit
        ], false, [], true);

        return ['parsingId' => $parsingId];
    }

    /**
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function actionCancel($id) {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        Robot::cancelParsing($id);

        return ['parsingId' => $id];
    }


    /**
     * @param $q
     * @return array
     * @throws \Exception
     */
    public function actionProjects($id = null, $q = null, $masksId = null, $page = 1, $perPage = 10) {

        \Yii::$app->response->format = Response::FORMAT_JSON;

        if ($id) {
            /** @var ParsingProject $project */
            $project = ParsingProject::findOne($id);

            return $project->getSettings();
        }
        $page = (int)$page;
        $perPage = (int)$perPage;

        if ($page < 1) {
            $page = 1;
        }

        $projects = ParsingProject::find()
            ->andWhere(['status_id' => Status::STATUS_ACTIVE])
            ->limit($perPage)
            ->offset(($page - 1 ) * $perPage);

        if ($masksId) {
            $projects->andWhere(['id' => ParsingProjectMasks::find()->andWhere(['masks_id' => $masksId])->select('parsing_project_id')]);
        }
        if ($q) {
            $masks = ['or'];
            $masks[] = ['ILIKE', 'm.domain', $q];
            $masks[] = ['ILIKE', 'm.name', $q];
            $ids = Masks::find()->alias('m')
                ->innerJoinWith('parsingProjectMasks ppm', false)
                ->andWhere($masks)
                ->select('ppm.parsing_project_id')
                ->column();

            $projects->andFilterWhere([
                'or',
                ['ilike','name', $q],
                ['id' => $ids]
            ]);

        }

        $count = (clone $projects)->count();

        $projects = $projects->asArray(true)
            ->orderBy(['name' => SORT_ASC])
            ->select(['id', 'name'])
            ->all();


        foreach ($projects as $k => $project) {
            $projects[$k]['masks'] =  Masks::find()
                ->alias('m')
                ->innerJoinWith('parsingProjectMasks ppm', false)
                ->andWhere([
                    'ppm.parsing_project_id' => $project['id']
                ])
                ->asArray()
                ->orderBy([
                    'm.name' => SORT_ASC
                ])
                ->distinct()
                ->select(['m.id','m.name'])
                ->all();
        }


        return [
            'data' => $projects,
            'meta' => [
                'page'=> $page,
                'perPage' => $perPage,
                'totalCount '=> $count,
                'pageCount' => floor($count/$perPage)
            ]
        ];

    }


    /**
     * @param null $id
     * @param null $q
     * @param null $project_id
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function actionMasks($id = null, $q = null, $project_id = null, $page = 1, $perPage = 20) {

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $page = (int)$page;
        $perPage = (int)$perPage;

        if ($page < 1) {
            $page = 1;
        }

        $find = Masks::find()
            ->alias('m')
            ->andWhere([
                'm.status_id' => Status::STATUS_ACTIVE
            ])
            ->limit($perPage)
            ->offset(($page - 1 ) * $perPage);

        $req = \Yii::$app->request;
        if ($req->isDelete) {
            if ($id && $id !== 'new') {
                /** @var Masks $masks */
                $masks = Masks::findOne($id);
                if ($masks) {
                    $masks->delete();
                    return [
                        'deleted' => $id
                    ];
                }
            }
            return null;
        }
        if ($req->isPost) {
            if (!$id || $id === 'new') {
                $masks = new Masks;
                $masks->loadDefaultValues();
            } else {
                $masks = Masks::findOne($id);
                if (!$masks) {
                    $masks = new Masks;
                    $masks->loadDefaultValues();
                }
            }
            $params = $req->getBodyParams();
            if (isset($params['Masks']['masks'] )) {
                $params['Masks']['masks'] = new JsonExpression(Json::decode($params['Masks']['masks']));
            }
            if ($masks->load($params) && $masks->validate()) {
                if (!$masks->id || $masks->id === 'new') {
                    $masks->id = Masks::getDb()->createCommand("select uuid_generate_v4()")->queryScalar();
                }
                $masks->save();
                if (!$id || $id === 'new') {
                    if ($req->post('bindToProject')) {
                        $bind = new ParsingProjectMasks();
                        $bind->parsing_project_id = $req->post('bindToProject');
                        $bind->masks_id = $masks->id;
                        $bind->save();
                    }
                }
                $id = $masks->id;
            } else {
                return ['errors' => $masks->errors];
            }
        }
        if ($id) {
            $masks = Masks::findOne($id);
            return [
                'id'              => $masks->id,
                'name'            => $masks->name,
                'domain'          => $masks->domain,
                'masks'           => $masks->masks,
                'test_urls'       => $masks->test_urls,
                'projects'        => $masks->getParsingProject()
                    ->select('name')
                    ->orderBy([
                        'updated_at' => SORT_DESC
                    ])
                    ->indexBy('id')
                    ->column(),
            ];
        }
        if ($project_id) {
            /** @var Masks[] $resultMasks */
            $find->andWhere([
                'm.id' => ParsingProjectMasks::find()
                            ->andWhere([
                                'parsing_project_id' => $project_id
                            ])
                            ->select('masks_id')
            ]);
        } else {
            if ($q) {
                $or = ['or'];
                $or[] = ['ILIKE', 'domain', $q];
                $or[] = ['ILIKE', 'name', $q];
                $find->andWhere($or);
            }
            $find->innerJoin(['ppm' => ParsingProjectMasks::tableName()], 'ppm.masks_id = m.id');
        }

        $count = (clone $find)->count();
        $masks = $find->distinct()
            ->orderBy(['m.name' => SORT_ASC])
            ->select(['m.id','m.name','m.domain'])
            ->asArray()
            ->all();

        return [
            'data' => $masks,
            'meta' => [
                'page'=> $page,
                'perPage' => $perPage,
                'totalCount '=> $count,
                'pageCount' => floor($count/$perPage)
            ]
        ];
    }

    /**
     * @param $id
     * @param null $columns
     * @return array|null
     * @throws \yii\base\UserException
     */
    public function actionInfo($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $columns = \Yii::$app->request->get('columns', []);

        $parsing = Parsing::find()
            ->select($columns)
            ->andWhere([
                'status_id' => Status::STATUS_ACTIVE,
                'id' => $id
            ])
            ->asArray()
            ->one();

        if (!$parsing) {
            throw new \yii\base\UserException('Не найдена модель парсинга');
        }

        return $parsing;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        header('Access-Control-Allow-Origin: *');
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'     => true,
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get','post'],
                ],
            ],
        ];
    }




}