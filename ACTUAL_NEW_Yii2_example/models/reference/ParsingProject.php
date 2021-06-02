<?php
namespace app\models\reference;

use app\components\base\Entity;
use app\components\base\ScheduleInterface;
use app\components\base\ScheduleTrait;
use app\components\base\type\Reference;
use app\components\DateTime;
use app\components\ValidationRules;
use app\models\cross\ParsingProjectMasks;
use app\models\cross\ParsingProjectProject;
use app\models\cross\ParsingProjectRegion;
use app\models\cross\ProjectRegion;
use app\models\cross\ProjectSource;
use app\models\enum\ErrorType;
use app\models\enum\ParsingStatus;
use app\models\enum\PriceParsedStatus;
use app\models\enum\Region;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\pool\NomenclatureDocumentItem;
use app\models\pool\ParsingError;
use app\models\pool\ParsingProjectProxy;
use app\models\pool\ParsingProjectProxyBan;
use app\models\pool\PriceParsed;
use app\models\pool\ProxyParsingProject;
use app\models\pool\VpnCompetitor;
use app\models\register\Error;
use app\models\register\Parsing;
use app\models\register\Proxy;
use app\models\register\Task;
use app\widgets\FormBuilder;
use yii;
use yii\helpers\ArrayHelper;

/**
 * Class ParsingProject
 * @package app\models\reference
 *
 * @property int source_id
 * @property string competitor_id
 *
 * @property string last_parsing_id
 * @property int split_by
 * @property bool is_our_regions
 *
 * @property string proxies
 * @property int    proxy_bantime
 * @property string user_agents
 * @property bool tor_enabled
 * @property bool save_browser_cookies
 * @property string cookies
 * @property string cookies_domain
 * @property string domain
 * @property string urls
 * @property string ping_url
 * @property string droid_type
 * @property bool parsing_type
 * @property bool prepare_pages
 * @property string comment
 * @property string vpn_type
 * @property array vpns
 * @property bool used_by_calc
 * @property bool matching_api_enabled
 *
 * @property int max_projects
 * @property int max_connections
 * @property int rate_limit
 * @property int retry_timeout
 * @property int timeout
 * @property int retries
 * @property int parallel_droids
 * @property bool is_phantom
 * @property string browser
 * @property string blocked_domains
 * @property int restart_browser
 * @property string url_replace_from
 * @property string url_replace_to
 * @property boolean disable_images
 * @property boolean check_unique_name
 *
 * @property bool signals_enabled
 * @property int items_per_hour_available
 * @property int errors_per_hour_available
 *
 * @property Masks[]  masks
 *
 * @property Competitor competitor
 * @property Parsing    lastParsing
 * @property Source     source
 * @property ParsingProjectRegion[] regions
 * @property ParsingProjectProject[] projects
 * @property ParsingProjectMasks[] parsingProjectMasks
 * 
 */
class ParsingProject extends Reference implements ScheduleInterface
{
    use ScheduleTrait;

    public $schedules_count = 0;
    private $urlReplacements = false;

    /**
     * Оверрайдовые настройки робота
     * @return array
     */
    public function getRobotSettings($robotId = null) {
        $robot = null;

        if ($robotId) {
            /** @var Robot $robot */
            $robot = Robot::find()
                ->andWhere(['id' => $robotId])
                ->one();
        }

        if (!$robot) {
            /** @var Robot $robot */
            $robot = Robot::find()
                ->andWhere(['status_id' => Status::STATUS_ACTIVE])
                ->limit(1)
                ->one();
        }

        $settings = $robot->getSettings();

        if ($this->max_connections) {
            $settings['maxThreads'] = $this->max_connections;
        }
        if ($this->rate_limit) {
            $settings['delay'] = $this->rate_limit;
        }
        if ($this->retry_timeout) {
            $settings['retryDelay'] = $this->retry_timeout;
        }
        if ($this->timeout) {
            $settings['timeout'] = $this->timeout;
        }
        if ($this->retries) {
            $settings['retries'] = $this->retries;
        }

        $proxies = ParsingProjectProxy::find()
            ->alias('ppp')
            ->select('proxy_id')
            ->andWhere([
                '(' . ParsingProjectProxyBan::find()
                    ->alias('pppb')
                    ->select('count(*)')
                    ->andWhere(['pppb.parsing_project_id' => $this->id])
                    ->andWhere('trim(pppb.proxy_id) = regexp_replace(ppp.proxy_id, E\'[\\n\\r]+\', \'\', \'g\')')
                    ->andWhere('(pppb.banned_at + interval \'' . $this->proxy_bantime . ' minutes\') >= \'' . date('Y-m-d H:i:s') . '\'')
                    ->createCommand()
                    ->getRawSql()
                . ')' => 0,
                'ppp.parsing_project_id' => $this->id
            ])
            ->column();

        if ($proxies && count($proxies) > 0) {
            $settings['proxies'] = array_map(function($a) {return trim($a);}, $proxies);
        }

        if (trim($this->user_agents)) {
            $settings['userAgents'] = explode("\n", str_replace("\r", "", $this->user_agents));
        }

        $settings['torEnabled'] = $this->tor_enabled;
        $settings['saveBrowserCookies'] = $this->save_browser_cookies;
        $settings['disableImages'] = $this->disable_images;
        $settings['check_unique_name'] = $this->check_unique_name;

        $settings['blockedDomains'] = !empty($this->blocked_domains) ? explode("\n", str_replace("\r", "", trim($this->blocked_domains))) : [];

        if (isset($settings['blockedDomains'][0]) && $settings['blockedDomains'][0] === '[default]') {
            $settings['blockedDomains'] = array_merge($settings['blockedDomains'], [
                'mc.yandex.ru',
                'g.doubleclick.net',
                'www.googleadservices.com',
                'hit.acstat.com',
                'code.acstat.com',
                'statad.ru',
                'rockcnt.com',
                'api.flocktory.com',
                'tracking.retailrocket.net',
                'rockcnt.com',
                'dsp.retailrocket.net',
                'tracking.retailrocket.net',
                'cdn.retailrocket.net',
                'www.googletagmanager.com',
                'yastatic.net',
                'www.youtube.com',
                'staticxx.facebook.com',
                'browser-updater.yandex.net',
                'connect.facebook.net',
                'counter.yadro.ru',
                'gstatic.com',
            ]);
            array_shift($settings['blockedDomains']);
        }


        return $settings;
    }

    public function getSettings($withMasks = true, $robotId = null) {
        return array_merge($this->getRobotSettings($robotId),
            [
                'id'                => $this->id,
                'name'              => $this->name,
                'ping_url'          => $this->ping_url ? : null,
                'cookies'           => $this->getRegionsCookies(null),
                'cookies_domain'    => $this->cookies_domain,
                'browser'           => $this->browser,
                'restart_browser'   => $this->restart_browser,
                'droid_type'        => $this->droid_type,
                'parsing_type'      => $this->parsing_type,
                'prepare_pages'     => $this->prepare_pages,
                'matching_api_enabled' => $this->matching_api_enabled,
                'skip'              => null
            ], $withMasks ? ['projectMasks'      => $this->getProjectMasks()] : []);
    }

    private $_PPRegions = false;
    /**
     * Настройки робота для регионов
     * @return array
     */
    public function getRegionsSettings() {
        return ArrayHelper::getColumn($this->getPPRegions(), 'cookies');
    }

    public function getPPRegions() {
        if ($this->_PPRegions === false) {
            $this->_PPRegions = $this->getParsingProjectRegions()
                ->innerJoin(['r'=>Region::tableName()],'r.id = region_id')
                ->indexBy('region_id')
                ->select(['r.name','region_id','cookies'])
                ->asArray()
                ->all();
        }
        return  $this->_PPRegions;
    }
    /**
     * Настройки масок проекта
     * @return array
     */
    public function getProjectMasks() {
        $projectMasks = [];
        foreach ($this->masks as $mask) {
            $projectMasks[$mask->id] = $mask->masks;
        }
        return $projectMasks;
    }

    /**
     * Куки для региона
     * @return string
     */
    public function getRegionsCookies($regionId) {
        $regionSettings = $this->getRegionsSettings();
        if ($regionId && isset($regionSettings[$regionId])) {
            return $regionSettings[$regionId];
        } else {
            return $this->cookies;
        }
    }
    
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Проект парсинга';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Проекты парсинга';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::ruleUuid('last_parsing_id'),
            ValidationRules::ruleUuid('competitor_id'),
            parent::rules(),
            [

                [['cookies', 'cookies_domain', 'urls', 'domain', 'blocked_domains', 'proxies', 'user_agents', 'ping_url', 'comment', 'droid_type'], 'safe'],
                [['split_by','restart_browser', 'proxy_bantime'], 'number'],
                [['parsing_type'],'in', 'range' => ['normal', 'collecting', 'matching']],
                [['parallel_droids','max_connections','rate_limit','retry_timeout','timeout','retries', 'items_per_hour_available', 'errors_per_hour_available'], 'number'],
                [
                    [
                        'is_phantom', 'is_our_regions', 'prepare_pages',
                        'used_by_calc', 'matching_api_enabled', 'tor_enabled',
                        'signals_enabled', 'save_browser_cookies', 'disable_images',
                        'check_unique_name',
                    ], 'boolean'
                ],
                [['browser', 'url_replace_from', 'url_replace_to'], 'string'],
                [['vpn_type'], 'in', 'range' => array_keys(self::getVpnTypes())],
                [['vpns'], function () {
                    if ($this->vpn_type && empty($this->vpns)) {
                        $this->addError('vpns', "Вы должны указать VPN-сервера");
                    }
                }],
                [['vpns'], 'safe'],
            ],
//            ValidationRules::ruleDefault('max_connections', 5),
//            ValidationRules::ruleDefault('rate_limit', 2000),
//            ValidationRules::ruleDefault('retry_timeout', 100),
//            ValidationRules::ruleDefault('timeout', 5000),
            ValidationRules::ruleDefault('parallel_droids', 1),
            ValidationRules::ruleEnum('source_id', Source::className())
        );
    }

    /**
     * Типы для поля vpn_type
     * @return array
     */
    public static function getVpnTypes()
    {
        return [
            '' => 'Без VPN',
            'use' => 'Использовать',
            'rotation' => 'Ротация',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'split_by'              => 'Разбить по (кол-во урлов)',
                'domain'                => 'Домен',
                'competitor_id'         => 'Конкурент',
                'competitor'            => 'Конкурент',
                'urls'                  => 'Урлы',
                'source_id'             => 'Источник',
                'source'                => 'Источник',
                'last_parsing_id'       => 'Последний запуск',
                'lastParsing'           => 'Последний запуск',
                'regions.region_id'     => 'Регион',
                'raw'                   => 'Исходный код файла проекта',
                'parsing_type'          => 'Тип проекта',
                'prepare_pages'         => 'Готовить страницы',
                'cookies'               => 'Cookies',
                'cookies_domain'        => 'Домен для cookies',
                'comment'               => 'Комментарии',
                'restart_browser'       => 'Перезапускать браузер через (кол-во запросов)',
                'max_connections'            => 'Одновременно потоков',
                'rate_limit'                 => 'Промежуток между запросами (мс.)',
                'retry_timeout'              => 'Промежуток между попытками (мс.)',
                'timeout'                    => 'Таймаут запроса (мс.)',
                'retries'                    => 'Доп. попыток',
                'proxies'                    => 'Прокси',
                'proxy_bantime'              => 'Длительность бана прокси',
                'user_agents'                => 'Юзер агенты',
                'masks'                      => 'Маски',
                'is_phantom'                 => 'deprecated',
                'browser'                    => 'Браузер',
                'is_our_regions'             => 'Наши регионы',
                'ping_url'                   => 'Урл для проверки проксей',
                'parallel_droids'            => 'Параллельно дроидов',
                'droid_type'                 => 'Тип дроида',
                'vpn_type'                   => 'тип VPN',
                'vpns'                       => 'VPN-сервера',
                'used_by_calc'               => 'Сбор для ПР',
                'matching_api_enabled'       => 'Автоидентификация по API',
                'url_replace_from'           => 'Что заменить в URL',
                'url_replace_to'             => 'На что заменить в URL',
                'tor_enabled'                => 'Использовать Tor',
                'save_browser_cookies'       => 'Сохранять куки после каждой страницы',
                'disable_images'             => 'Отключить картинки',
                'signals_enabled'            => 'Сигнализация включена',
                'items_per_hour_available'   => 'Обработанных страниц в час',
                'errors_per_hour_available'  => 'Ошибок в час',
                'check_unique_name' => 'Только уникальные товары'
            ]
        );
    }

    /**
     * @return bool
     */
    public static function crudCreateEnabled()
    {
        return true;
    }

    /**
     * @return bool
     */
    public static function crudDeleteEnabled(){
        return true;
    }

    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations()
    {
        return array_merge(
            parent::crudIndexSearchRelations(), 
            [
                'regions',
                'masks',
                'source',
                'competitor',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns()
    {
        $pp                 = Yii::$app->request->get('ParsingProject', []);
        $regionsRegionId    = isset($pp['regions.region_id']) ? $pp['regions.region_id'] : null;

        return array_merge(
            parent::crudIndexColumns(),
            [
                'name',
                'competitor',
                'masks' => [
                    'label'     => Masks::getPluralNominativeName(),
                    'format' => 'raw',
                    'value'     => function($pricingProject) {
                        /** @var \app\models\reference\ParsingProject $pricingProject */
                        $masks = $pricingProject->masks;
                        if(!$masks) {
                            return null;
                        }
                        return implode(',', array_reduce($masks, function ($carry, $item) use ($pricingProject) {
                            /** @var Masks $item */
                            $carry[] = yii\helpers\Html::a($item->name, ['/masquerade/index.html#masks&'.$pricingProject->id.'&'.$item->id]);
                            return $carry;
                        } , []));
                    }
                ],
                'used_by_calc',
                'matching_api_enabled',
                'regions_region_id' => [
                    'label'     => \app\models\enum\Region::getPluralNominativeName(),
                    'format'    => 'raw',
                    'filter'    => FormBuilder::renderSelect2(null, \app\models\enum\Region::className(), 'ParsingProject[regions.region_id]', $regionsRegionId, true, 0),
                    'value'     => function($model) {
                        /** @var \app\models\reference\ParsingProject $model */
                        $names = ArrayHelper::getColumn($model->regions,'name');
//                        $str = [];
//                        foreach ($ids as $id) {
//                            $str[] = Region::getNameById($id);
//                        }
                        return join(", ", $names);
                    }
                ]
            ]
        );
    }

    /**
     * @return array
     */
    public static function relations() {
        return array_merge(parent::relations(),[
            'source',
            'regions',
            'lastParsing',
            'competitor',
            'regions',
        ]);
    }

    /**
     * @param array $params
     * @return string
     */
    public function getScheduleDuration($params = []) {
        return '00:15:00';
    }

    /**
     * @param array $params
     * @return string
     */
    public function getScheduleTitle($params = []) {
        if (isset($params['regions'])) {
            $region = $this->getPPRegions();
            return (string)$region[$params['region_id']]['name']. ' - ' . (string)$this;
        } else {
            return (string)$this;
        }
    }

    /**
     * @param array $params
     * @return string
     */
    public function getScheduleDescription($params = []) {
        $str = (string)$this;
        return $str;
    }

    /**
     * Функция котрую запускает "Расписание"
     * @param array $params
     */
    public function schedule($params = []) {
        if (getenv('DISABLE_SCHEDULE_PARSING')==='true') {
            return;
        }
        $this->execute($params);
    }

    /**
     * Выполнить выгрузку цен
     * @param Task $task
     * @return null;
     */
    public function taskStartParsing(Task $task = null)
    {
        $task->task_status_id   = TaskStatus::STATUS_RUNNING;
        $task->started_at      = new DateTime();
        $task->save();

        $params = $task->getParams();
        ob_start();
        try {
            $this->execute($params,false, [], isset($params['test'])  ? $params['test'] : false);

            $task->task_status_id   = TaskStatus::STATUS_FINISHED;
            $task->finished_at      = new DateTime();
            $task->status_id        = Status::STATUS_DISABLED;
            $task->result_text      = ob_get_flush();
            $task->save();
        } catch (\Exception $e) {
            print_r($e->getMessage());
            print_r($e->getFile());
            print_r($e->getLine());

            $task->task_status_id   = TaskStatus::STATUS_CANCELED;
            $task->finished_at      = new DateTime();
            $task->status_id        = Status::STATUS_DISABLED;
            $task->result_text      = ob_get_flush();
            $task->save();

            Error::logError($e, ErrorType::TYPE_TASK, Entity::Task, $task->id);
        }
    }

    public function getMasksDomains() {
        return $this->getMasks()->indexBy('id')->select('domain')->column();
    }

    /**
     * @param $url
     * @param $regionId
     * @return mixed|null|string|string[]
     */
    public function prepareUrl($url, $regionId) {
        $url = trim($url);
        if ($this->url_replace_from && $this->url_replace_to) {
            $url = preg_replace('/' . str_replace('/', '\/', $this->url_replace_from) . '/', $this->url_replace_to, $url);
        }
        if ($this->urlReplacements === false) {
            $this->urlReplacements = $this->getParsingProjectRegions()
                ->andWhere(['is not', 'url_replace_from', null])
                ->select(['region_id','url_replace_from', 'url_replace_to'])
                ->indexBy('region_id')
                ->asArray()
                ->all();
        }
        if ($regionId && $this->urlReplacements && isset($this->urlReplacements[$regionId]) && $this->urlReplacements[$regionId]['url_replace_from']) {
            $url = preg_replace('/' . str_replace('/', '\/', $this->urlReplacements[$regionId]['url_replace_from']) . '/', $this->urlReplacements[$regionId]['url_replace_to'], $url);
        }
        return $url;
    }

    /**
     * Запустить проект
     * @param array $scope - срез для подбора урлов и выбора определенных регионов
     * @param bool $returnOnlyUrls
     * @param array $predefinedItems
     * @throws \Exception
     * @return mixed
     */
    public function execute($scope = [], $returnOnlyUrls = false, $predefinedItems = [], $test = false) {

        $parsingAll = Parsing::createFromProject($this);
        $parsingAll->scope_info  = yii\helpers\Json::encode($scope);

        $scope = array_merge([
            'name'          => null,
            'regions'       => null,
            'projects'      => null,
            'competitors'   => null,
            'brands'        => null,
            'attempt'       => 1,
            'from_errors'   => null,
            'limit'         => 0,
            'priority'      => 0,
        ], $scope);

        $parsingAll->attempt = $scope['attempt'];

        foreach ($scope as $k => $v) {
            if ($v && !is_array($v) && in_array($k, ['projects','regions','competitors','brands'])) {
                $scope[$k] = explode(',', $v);
            }
        }

        //  Если регионы не указаны и они в наличии, то взять все
        if (!$scope['regions']) {
            $scope['regions'] =
                $this->getParsingProjectRegions()->andWhere(['not',
                    ['cookies' => null],
                ])->andWhere(['not',
                    ['cookies' => '']
                ])->orderBy(['sort' => SORT_ASC])
                ->select('region_id')
                ->column();
            $scope['regions'][] = null;
        }
        if (!$scope['regions']) {
            $scope['regions'] = [null];
        }

        if ($test) {
            $parsingAll->is_test = true;
        }

        $regions        = $scope['regions'];

        //$parsings= [];
        foreach ($regions as $regionId) {

            $items          = [];

            $masksDomains = $this->getMasksDomains();

            // Предустановленные урлы с параметраи [PARAM]
            if ($predefinedItems){
                $items = $predefinedItems;
            }
            else if (!empty(trim($this->urls)) || !empty($scope['from_errors'])) {

                if (!empty($scope['from_errors']) && $scope['from_errors']) {
                    $urls =  ParsingError::find()->andWhere(['parsing_id' => $scope['from_errors']])->select('url')->column();
                    // для озона (http://pricing.vseinstrumenti.ru/parsing-project/update?id=fc6388d2-36f0-423e-bfc4-8c49eb09e68b)
                    // брать на перепарсинг урлы из цен со статусом "Ошибка обработки"
                    if ($this->id === 'fc6388d2-36f0-423e-bfc4-8c49eb09e68b' || $this->id === 'd839aec8-4f38-4ea0-a4d7-543727ff13e9'){
                        $urls = array_merge($urls, \app\models\pool\PriceParsed::find()
                            ->andWhere([
                                'parsing_project_id' => $this->id,
                                'parsing_id' => $scope['from_errors'],
                                'price_parsed_status_id' => PriceParsedStatus::STATUS_ERROR,
                            ])
                            ->select('url')
                            ->column()
                        );
                    }
                } else {
                    $urls = explode("\n", $this->urls);
                }


                foreach ($urls as $i => $urlString) {
                    $item = [];
                    $urlParts = explode('[PARAM]:',$urlString);

                    $item['url'] = $this->prepareUrl($urlParts[0], $regionId);


                    $item['attributes'] = [];

                    if ($this->competitor_id) {
                        $item['attributes']['competitor_id'] = $this->competitor_id;
                    }

                    if ($this->prepare_pages) {
                        $item['preparePages'] = true;
                    }

                    if ( $this->competitor_id) {
                        $item['attributes']['competitor_id'] = $this->competitor_id;
                    }

                    if (isset($urlParts[1])) {
                        $params = explode('$', $urlParts[1]);
                        //array_pop($params);
                        foreach ($params as $param) {
                            $paramParts = explode('=', $param);
                            if (count($paramParts) > 1) {
                                if ($paramParts[0] === 'ID') {
                                    $paramParts[0] = 'item_id';
                                }
                                if ($paramParts[0] === 'must') {
                                    $item['must']= $paramParts[1];
                                    continue;
                                }
                                if ($paramParts[0] === 'dont') {
                                    $item['dont']= $paramParts[1];
                                    continue;
                                }
                                $item['attributes'][$paramParts[0]] = $paramParts[1];
                            }
                        }
                    }

                    foreach ($masksDomains as $masksId => $domain) {
                        //if (preg_match("/".preg_quote($domain)."/i", $item['url'])) {
                            $item['masks_id'] = $masksId;
                           // break;
                    }

                    $items[] = $item;
                }

            } else if ($this->source_id) {
                $urlScope = array_merge([], $scope);
                // Если не заданы определенные проекты, берем все проекты региона
                if (!$urlScope['projects']) {
                    $findProject = Project::find()
                        ->alias('p')
                        ->innerJoin([
                            'ps' => ProjectSource::tableName()
                        ], 'ps.project_id = p.id AND ps.source_id = ' . $this->source_id);
                    // Если регион не указан то вообще все проекты
                    if ($regionId) {
                        $findProject->innerJoin(['pr'=>ProjectRegion::tableName()],'pr.project_id = p.id')->andWhere(['pr.region_id' => $regionId]);
                    }
                    $urlScope['projects'] = $findProject->groupBy('p.id')->select(['p.id'])->column();
                }
                // Если не указаны конкуренты брать коркурента
                if (!$urlScope['competitors'] && $this->competitor_id) {
                    $urlScope['competitors'] = [$this->competitor_id];
                }
                $urlScope['masks'] = $masksDomains;
                $items = $this->generateParsingItems($this->source_id, $urlScope, $regionId);
            }

            if ($returnOnlyUrls) {
                return $items;
            }

            if (count($items) == 0) {
                continue;
            }

            $parallel = $this->parallel_droids;
            if (!$parallel) {
                $parallel = 1;
            }

            // Распараллеливание дроидов
            $chunks = array_chunk($items, ceil(count($items) / $parallel), true);

            $mainChunkId = static::getDb()->createCommand("select uuid_generate_v4()")->queryScalar();

            for($p = 0; $p < count($chunks); $p++){

                $itemsChunk = $chunks[$p];

                /** @var Parsing $parsing */
                $parsing = clone $parsingAll;
                $parsing->parsing_status_id = ParsingStatus::STATUS_QUEUED;
                $parsing->total_count       = count($itemsChunk);
                $parsing->global_count      = count($itemsChunk);
                $parsing->region_id         = $regionId;
                $parsing->is_phantom        = $this->is_phantom;
                $parsing->browser           = $this->browser;
                $parsing->priority          = $scope['priority'];

                if ($p == 0) {
                    $parsing->id = $mainChunkId;
                    $parsing->parallel_is_main = true;
                } else {
                    $parsing->parallel_is_main = false;
                }
                $parsing->parallel_main_id  = $mainChunkId;
                $parsing->parallel_droids   = $this->parallel_droids;
                $parsing->parallelIndex     = $p;

                if ($test) {
                    $parsingAll->is_test = true;
                }
                if ($regionId === null) {
                    $parsing->regions = $this->getBatchRegions();
                    if (empty($parsing->regions)) {
                        continue;
                    }
                } else {
                    $parsing->regions = $regionId;
                }

                $parsing->name              = $this->name;

                if ($scope['name']) {
                    if (is_array($scope['name'])) {
                        if (count($scope['name']) > 0) {
                            $scope['name'] = $scope['name'][0];
                            $parsing->name = $scope['name'];
                        }
                    } else {
                        $parsing->name = $scope['name'];
                    }
                }

                $parsing->name = preg_replace('/\s\[.*?\]/','', $parsing->name);

                if ($scope['competitors']) {
                    $parsing->name .= ' - '. (string)Competitor::findOne($scope['competitors'][0]);
                }
                if ($scope['brands']) {
                    $parsing->name .= ' - '. (string)Brand::findOne($scope['brands'][0]);
                }
                if ($scope['projects']) {
                    $parsing->name = $parsing->name . ' из ' . (string)Project::findOne($scope['projects'][0]);
                } else {
                    if ($regionId) {
                        $parsing->name = $parsing->name . ' - ' . (string)Region::getNameById($regionId);
                    }
                }
                if (count($chunks) > 1) {
                    $parsing->name .= " [часть $p]";
                }
                if (count($itemsChunk) > 0) {

                    $parsing->assignRobot();
                    $parsing->save();

                    if ($this->vpn_type) {
                        $parsing->assignNewVpn();
                    }
                    Robot::sendTaskTo($parsing->getQueueName(), $itemsChunk);

                    echo $parsing->name . ' сгенерирован с количеством урлов ' . count($itemsChunk);
                }

            }

        }

        return isset($parsing) ? $parsing->id : null;

    }


    public function generateParsingItems($sourceId, $scope, $regionId = null) {
        $scope = array_merge([
            'regions'       => null,
            'projects'      => null,
            'competitors'   => null,
            'brands'        => null,
            'domain'        => null,
            'from_errors'   => null,
            'limit'         => 0,
        ], $scope);

        if (empty($scope['projects'])) {
            return [];
        }
        $isYm = $sourceId == Source::SOURCE_YANDEX_MARKET;

        $find = ProjectItem::find()
            ->alias('pi')
            ->innerJoin(['i' => Item::tableName()],'i.id = pi.item_id')
            ->innerJoin(['p' => Project::tableName()], 'pi.project_id = p.id')
            ->select([
                'url'           => 'i.ym_url',
                'item_id'       => 'pi.item_id',
                'must'          => 'i.pricing_must_be',
                'dont'          => 'i.pricing_dont_be',
                'rank'          => 'i.sales_rank'
            ])
            ->indexBy('item_id')
            ->asArray()
            ->distinct()
            ->andWhere(['or',
                ['p.supply_price_threshold' => null],
                ['>', 'i.price_supply', new yii\db\Expression('p.supply_price_threshold')],
            ])
            ->orderBy(['rank' => SORT_DESC])
        ;
        $findUnion = NomenclatureDocumentItem::find()
            ->alias('ndi')
            ->innerJoin(['i' => Item::tableName()],'i.id = ndi.item_id')
            ->innerJoin(['p' => Project::tableName()], 'p.nomenclature_document_id = ndi.nomenclature_document_id')
            ->select([
                'url'           => 'i.ym_url',
                'item_id'       => 'ndi.item_id',
                'must'          => 'i.pricing_must_be',
                'dont'          => 'i.pricing_dont_be',
                'rank'          => 'i.sales_rank'
            ])
            ->indexBy('item_id')
            ->asArray()
            ->distinct()
            ->andWhere(['or',
                ['p.supply_price_threshold' => null],
                ['>', 'i.price_supply', new yii\db\Expression('p.supply_price_threshold')],
            ])
            ->orderBy(['rank' => SORT_DESC])
        ;

        if (!empty($scope['projects']) && $scope['projects']) {
            $find->andWhere([
                'pi.project_id' => $scope['projects']
            ]);
            $findUnion->andWhere([
                'p.id' => $scope['projects']
            ]);
        }

        if (!empty($scope['brands']) && $scope['brands']) {
            $find->andWhere([
                'i.brand_id' => $scope['brands']
            ]);
            $findUnion->andWhere([
                'i.brand_id' => $scope['brands']
            ]);
        }

        if (!empty($scope['limit']) && $scope['limit'] > 0) {
            $find->limit($scope['limit']);
        }


        if ($isYm) {
            $find->andWhere(['not',
                    ['i.ym_url' => null]
            ]);
            if (!empty($scope['from_errors']) && $scope['from_errors']) {
                $find->andWhere([
                    'i.ym_url' => ParsingError::find()
                        ->andWhere(['parsing_id' => $scope['from_errors']])
                        ->select('url')
                ]);
            }
        }

        $find->union($findUnion);

        $items = [];

        foreach ($find->batch(300) as $foundItems) {
            if (!$isYm) {
                $competitorItemsQuery = CompetitorItem::find()
                    ->alias('t')
                    ->andWhere(['not',
                        ['t.url' => null]
                    ])
                    ->andWhere([
                        't.item_id' => ArrayHelper::getColumn($foundItems, 'item_id'),
                        't.status_id' => Status::STATUS_ACTIVE,
                    ])
                    ->asArray()
                    ->select([
                        't.url',
                        't.item_id',
                        't.competitor_id',
                    ]);

                if (!empty($scope['competitors']) && $scope['competitors']) {
                    $competitorItemsQuery->andWhere([
                        't.competitor_id' => $scope['competitors']
                    ]);
                }
                if (!empty($scope['from_errors']) && $scope['from_errors']) {
                    $competitorItemsQuery->andWhere([
                        't.url' => ParsingError::find()
                            ->andWhere(['parsing_project_id' => $this->id, 'parsing_id' => $scope['from_errors']])
                            ->select('url')
                    ]);
                }
                $competitorItems = $competitorItemsQuery->all();
                foreach ($competitorItems as $competitorItem) {
                    if (isset($foundItems[$competitorItem['item_id']])) {
                        $record = $foundItems[$competitorItem['item_id']];
                        $item = [
                            'url' => $this->prepareUrl($competitorItem['url'], $regionId),
                            'attributes' => [
                                'item_id'           => $competitorItem['item_id'],
                                'competitor_id'     => $competitorItem['competitor_id'],
                            ],
                            'must' => $record['must'],
                            'dont' => $record['dont'],
                        ];
                        $items[] = $item;
                    }
                }
            } else {
                foreach ($foundItems as $record) {
                    $items[] = [
                        'url' => $this->prepareUrl($record['url'], $regionId),
                        'attributes' => [
                            'item_id' => $record['item_id']
                        ],
                        'must' => $record['must'],
                        'dont' => $record['dont'],
                    ];
                }
            }
        }
        return $items;
    }


    public function getBatchRegions() {
        $column = $this->getParsingProjectRegions()->andWhere(['or',['cookies' => null], ['cookies' => '']])->select('region_id')->column();
        if (is_array($column)) {
            return join(',',$column);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (is_array($this->vpns)) {
                $this->vpns = json_encode($this->vpns);
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert && isset($changedAttributes['vpn_type']) && $this->vpn_type) {
            $parsingsQuery = Parsing::find()
                ->alias('p')
                ->andWhere([
                    'p.parsing_status_id' => [ParsingStatus::STATUS_QUEUED, ParsingStatus::STATUS_PROCESSING],
                    '(' . VpnCompetitor::find()
                        ->alias('vp')
                        ->select('count(*)')
                        ->andWhere('vp.parsing_id = p.id')
                        ->createCommand()
                        ->getRawSql()
                    . ')' => 0
                ]);
            foreach ($parsingsQuery->each() as $parsing) {
                $parsing->assignNewVpn();
            }
        }
        if (is_string($this->vpns)) {
            $this->vpns = json_decode($this->vpns);
        }
        if (isset($changedAttributes['proxies'])) {
            $old = array_map('trim', explode("\n", trim(isset($changedAttributes['proxies']) ? $changedAttributes['proxies'] : '')));
            $new = array_map('trim', explode("\n", trim($this->proxies)));
            ParsingProjectProxy::deleteAll(['parsing_project_id' => $this->id, 'proxy_id' => $old]);
            $proxiesToDelete = [];
            foreach ($old as $proxy) {
                if (!ParsingProjectProxy::find()
                    ->andWhere(['proxy_id' => $proxy])
                    ->andWhere(['!=', 'parsing_project_id', $this->id])
                    ->exists()) {
                    $proxiesToDelete[] = $proxy;
                }
            }
            ParsingProjectProxy::deleteAll(['proxy_id' => $proxiesToDelete]);
            ParsingProjectProxyBan::deleteAll(['proxy_id' => $proxiesToDelete]);
            ProxyParsingProject::deleteAll(['proxy_id' => $proxiesToDelete]);
            Proxy::deleteAll(['id' => $proxiesToDelete]);

            $proxiesToInsert = [];
            foreach ($new as $proxy) {
                if (strlen(trim($proxy)) > 0 && !Proxy::find()->andWhere(['id' => $proxy])->exists()) {
                    $proxiesToInsert[] = ['id' => $proxy, 'is_public' => false];
                }
            }
            if (count($proxiesToInsert) > 0) {
                Proxy::getDb()
                    ->createCommand()
                    ->batchInsert(
                        Proxy::tableName(),
                        array_keys($proxiesToInsert[0]),
                        $proxiesToInsert
                    )->execute();
            }

            $parsingProjectProxy = [];
            foreach ($new as $proxy) {
                $parsingProjectProxy[] = [
                    'id' => self::getDb()->createCommand("select uuid_generate_v4()")->queryScalar(),
                    'parsing_project_id' => $this->id,
                    'proxy_id' => $proxy,
                ];
            }
            if (count($parsingProjectProxy) > 0 && is_array($parsingProjectProxy[0])) {
                ParsingProjectProxy::getDb()
                    ->createCommand()
                    ->batchInsert(ParsingProjectProxy::tableName(), array_keys($parsingProjectProxy[0]), $parsingProjectProxy)
                    ->execute();
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritDoc
     */
    public function afterFind()
    {
        $this->proxies = implode("\n", ParsingProjectProxy::find()
            ->select('proxy_id')
            ->andWhere([
                'parsing_project_id' => $this->id,
            ])
            ->groupBy('proxy_id')
            ->column()
        );
        $this->vpns = explode(',', $this->vpns);
    }

    /**
     * Клонировать
     * @throws yii\db\Exception
     * @return ParsingProject
     */
//    public function cloneParsingProject() {
//
//        static::getDb()->createCommand("INSERT INTO ".ParsingProjectRegion::tableName()." (parsing_project_id, region_id, cookies, proxies, sort)
//    SELECT '{$clone->id}', region_id, cookies, proxies, sort FROM ".ParsingProjectRegion::tableName()." WHERE parsing_project_id = '{$this->id}'")->execute();
//
//        return $clone;
//    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchedule() {
        return $this->hasMany(Schedule::className(), ['requester_id' => 'id'])->andWhere(['requester_entity_id' => 48]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegions() {
        return $this->hasMany(Region::className(), ['id' => 'region_id'])->via('parsingProjectRegions');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsingProjectRegions() {
        return $this->hasMany(ParsingProjectRegion::className(), ['parsing_project_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects() {
        return $this->hasMany(ParsingProjectProject::className(), ['parsing_project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastParsing() {
        return $this->hasOne(Parsing::className(), ['id' => 'last_parsing_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource() {
        return $this->hasOne(Source::className(), ['id' => 'source_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsingProjectMasks() {
        return $this->hasMany(ParsingProjectMasks::className(), ['parsing_project_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasks() {
        return $this->hasMany(Masks::className(), ['id' => 'masks_id'])->via('parsingProjectMasks');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitor() {
        return $this->hasOne(Competitor::className(), ['id' => 'competitor_id']);
    }



}