<?php
use app\components\base\Entity;
use app\models\enum\Status;
use app\models\reference\Robot;
use app\models\register\Task;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/** @var $this \yii\web\View */
/** @var $content string */
/** @var \app\models\reference\User $user */
$user = Yii::$app->user->identity;

$exchangeDashboard = \app\components\exchange\Exchange::stats();

$refineErrors = Yii::$app->cache->get("Refine::errors");

/** @var Task[] $tasks */
$tasks = \app\models\register\Task::find()
    ->orderBy([
        'task_type_id'      => SORT_DESC,
        'task_status_id'    => SORT_DESC,
        'created_at'        => SORT_ASC,
    ])
    ->where([
        'status_id'         => Status::STATUS_ACTIVE
    ])
    ->indexBy('id')
    ->asArray()
    ->all();

$taskTypes = \app\models\enum\TaskType::getEnumArray();

if (!$tasks) { $tasks = []; }

// Для вывода апдейта по задачам которые обновляются в кеше а не в базе
foreach ($tasks as $i => $task) {
    if ($task['task_type_id'] == \app\models\enum\TaskType::TYPE_PARSING) {
        $redisKey = 'Task#Quick#' . $task['requester_id'];
        if (Yii::$app->redis->executeCommand('EXISTS', [$redisKey])) {
            $tasks[$i] = Json::decode(Yii::$app->redis->executeCommand('GET', [$redisKey]), true);
        }
    }
}

$this->registerJs("window.pricingTasks = " . Json::encode($tasks) . ";", \yii\web\View::POS_HEAD);
$this->registerJs("window.pricingTaskTypes = " . Json::encode($taskTypes) . ";", \yii\web\View::POS_HEAD);

$paths = "if(typeof window.indexUrls=='undefined'){window.indexUrls=[];}";
foreach (Yii::$app->crudModelsMap as $class => $url) {
    $name = explode('\\', $class);
    $shortName = end($name);
    $paths.="window.indexUrls['".$shortName."']='" . $url . "';";
}
$this->registerJs($paths, View::POS_HEAD);


?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">₽</span><span class="logo-lg">₽ricing</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Открыть меню</span>
        </a>
        <?php
            echo Breadcrumbs::widget(
            [
                'homeLink' => false,
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]);
            //echo '<h1 class="pull-left" style="color: white; line-height: 51px; margin: 0;">' . Html::encode($this->title) . '</h1>';
        ?>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
<!--                <li class="">-->
<!--                    <a href="/site/manual" >-->
<!--                        <i class="fa fa-question-circle" aria-hidden="true"></i>-->
<!--                    </a>-->
<!--                </li>-->
                <li class="dropdown tasks-menu notifications-menu">
                    <?php
                    $data = \app\models\register\Proxy::find()
                        ->select([
                            'out_of_date' => 'COUNT(CASE WHEN until < NOW() THEN 1 ELSE NULL END)',
                            'out_of_7_days' => 'COUNT(CASE WHEN until > NOW() AND until < (NOW() + interval \'7 days\') THEN 1 ELSE NULL END)'
                        ])
                        ->andWhere('until IS NOT NULL')
                        ->andWhere(['status_id' => Status::STATUS_ACTIVE])
                        ->asArray()
                        ->one();
                    $outOfDateProxiesCount = $data['out_of_date'];
                    $outOf7DaysProxiesCount = $data['out_of_7_days'];
                    $warnVpns = \app\models\reference\Vpn::find()
                        ->select([
                            'id',
                            'name',
                            'is_out' => '(CASE WHEN until < NOW() THEN TRUE ELSE FALSE END)',
                            'until',
                        ])
                        ->asArray()
                        ->andWhere('until IS NOT NULL AND (until < NOW() OR until < (NOW() + interval \'7 days\'))')
                        ->orderBy('until ASC')
                        ->all();
                    ?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-share" aria-hidden="true"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header"><strong>VPN</strong></li>
                        <li>
                            <ul class="menu">
                                <?php foreach ($warnVpns as $vpn): ?>
                                    <li><a href="<?= Url::to(['/vpn/update', 'id' => $vpn['id']])?>" style="background-color:<?= $vpn['is_out'] ? '#ff00005c' : '#ffff005c' ?>;"><?= $vpn['name'] ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="header"><strong>Прокси</strong></li>
                        <li>
                            <ul class="menu">
                                <li>
                                    <a href="<?= Url::to(['/proxy', 'is_out' => 1]) ?>" style="<?= $outOfDateProxiesCount > 0 ? 'background-color:#ff00005c;' : ''?>color:#000;">
                                        Просроченных <span class="pull-right"><?= $outOfDateProxiesCount ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= Url::to(['/proxy', 'is_out_of_7' => 1]) ?>" style="<?= $outOf7DaysProxiesCount > 0 ? 'background-color:#ffff005c;' : ''?>color:#000;">
                                        Скоро просрочатся <span class="pull-right"><?= $outOf7DaysProxiesCount ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php if($exchangeDashboard['totals']['import'] + $exchangeDashboard['totals']['importErrors'] > 0) { ?>
                    <li class="dropdown tasks-menu notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-cloud-download" aria-hidden="true"></i>
                            <?php if($exchangeDashboard['totals']['import'] > 0) {
                                echo '<span class="label " style="bottom: 9px;left: 7px;top:auto;right: auto;">'.$exchangeDashboard['totals']['import'].'</span>';
                            } ?>
                            <?php if($exchangeDashboard['totals']['importErrors'] > 0) {
                                echo '<span class="label label-danger">'.$exchangeDashboard['totals']['importErrors'].'</span>';
                            } ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header"><strong>Импорт из внешних систем</strong></li>
                            <?php foreach (['importErrors' => ['Ошибки','text-red','fa-bug'], 'import' => ['В процессе','text-green','fa-cloud-download']] as $exchangeNoteId => $exchangeNoteTitle) { ?>
                                <?php foreach($exchangeDashboard[$exchangeNoteId] as $requesterType => $processes) { ?>
                                    <li class="header <?=$exchangeNoteTitle[1]?>">
                                        <i class="fa <?=$exchangeNoteTitle[2]?> "></i> <?=$exchangeNoteTitle[0]?> <?=$requesterType?>
                                    </li>
                                    <li>
                                        <ul class="menu">
                                            <?php foreach($processes as $i => $process) { ?>
                                                <li>
                                                    <?php
                                                    if ($exchangeNoteId == 'importErrors') {
                                                        ?>
                                                            <a href="<?=Url::to([
                                                                '/crud-exchange-import',
                                                                'search[requester_entity_id]'   => $process['requester_entity_id'],
                                                                'search[requester_id]'          => $process['requester_id'],
                                                                'search[remote_entity]'         => $process['remote_entity'],
                                                                'search[is_error]'              => 1,
                                                            ])?>" target="_blank">
                                                        <?php
                                                    } else {
                                                        ?>
                                                            <a href="<?=Url::to([
                                                                '/crud-exchange-import',
                                                                'search[requester_entity_id]'   => $process['requester_entity_id'],
                                                                'search[requester_id]'          => $process['requester_id'],
                                                                'search[remote_entity]'         => $process['remote_entity'],
                                                            ])?>" target="_blank">
                                                        <?php
                                                    }
                                                    ?>
                                                        <small class="pull-right"><?=$process['count']?></small>
                                                        <?=$process['name']?> из <span class="text-light-blue"><?=$process['system_name']?></span>
                                                        <?php if ($process['requester_name']) { ?>
                                                            <br/><small>По запросу от <?=$process['requester_name']?></small>
                                                        <?php } ?>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                <?php } ?>
                            <?php }?>
                        </ul>
                    </li>
                <?php } ?>


                <?php if($refineErrors && intval($refineErrors['errors'],10) > 0) { ?>
                    <li class="dropdown tasks-menu notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-rub" aria-hidden="true"></i>
                            <?php if($refineErrors['errors'] > 0) {
                                echo '<span class="label label-danger">'.$refineErrors['errors'].'</span>';
                            } ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">

                                <a href="<?=Url::to([
                                    '/crud-error',
                                    'search[entity_type_id]'   => Entity::PriceParsed,
                                    'search[error_type_id]'    =>\app\models\enum\ErrorType::TYPE_IMPORT,
                                ])?>" target="_blank">
                                    <strong>Ошибки обработки цен</strong>
                                    <small class="pull-right">
                                        <span class="text-red"><?=$refineErrors['errors']?></span>
                                    </small>
                                </a>
                            </li>
                            <li class="header">
                                Ошибки на этапе преобразования спарценных цен в цены конкурентов
                                <br/> <small>Последняя обработка <?=$refineErrors['date']?></small>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <!-- Задачи (Исполнения проектов и т.д.) -->
                <li class="dropdown tasks-menu notifications-menu" id="tasks-tray" style="display: none;">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-tasks" aria-hidden="true"></i>
                        <span class="label label-info tasks-count">0</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                            $taskTypes = array_reverse($taskTypes);
                            foreach ($taskTypes as $taskType) {
                                echo '<li class="header" data-task-task_type_id="'.$taskType['id'].'" style="display:none;"><i class="fa '.$taskType['icon'].'"></i> '.$taskType['name'].'</li><li data-task-task_type_id="'.$taskType['id'].'"><ul class="menu tasks-list"></ul></li>';
                            }
                        ?>
                    </ul>
                </li>

                <li class="dropdown tasks-menu notifications-menu" id="robots-tray">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-android" aria-hidden="true"></i>
                        <span class="label" id="robots-count">0</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                        /** @var Robot[] $robots */
                        $robots = Robot::find()->andWhere(['status_id'=>Status::STATUS_ACTIVE])->all();
                        foreach ($robots as $robot) { ?>
                            <li class="header robot" data-id="<?=$robot->id?>">
                                <div class="box-tools pull-right">
                                    <a class="reboot-droid" data-droid="<?=$robot->id?>"><i class="fa fa-refresh"></i></a>
                                    <a href="/robot/update?id=<?=$robot->id?>"><i class="fa fa-cog"></i></a>
                                </div>
                                <i class="fa fa-android"></i> <?=$robot->name?>
                            </li>
                        <?php } ?>
                    </ul>
                </li>

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php echo \cebe\gravatar\Gravatar::widget([
                            'email' => $user->email,
                            'options' => [
                                'alt' => $user->getShortName()
                            ],
                            'size'  => 18,
                            'class' => 'user-image'
                        ]); ?>
                        <span class="hidden-xs"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <?php echo \cebe\gravatar\Gravatar::widget([
                                'email' => $user->email,
                                'options' => [
                                    'alt' => $user->getShortName()
                                ],
                                'size' => 160,
                                'class' => 'img-circle'
                            ]); ?>

                            <p>
                                <?=$user->lastname?>
                                <?=$user->firstname?><br/>
                                <small><?=$user->username?></small><br/>
                                <small><?=$user->email?></small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <!--li class="user-body">
                            <div class="col-xs-4 text-center">
                                <a href="#">Followers</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#">Sales</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#">Friends</a>
                            </div>
                        </li-->
                        <!-- Menu Footer-->
                        <li class="user-footer">
<!--                            <div class="pull-left">-->
<!--                                <a href="#" class="btn btn-default btn-flat">Аккаунт</a>-->
<!--                            </div>-->
                            <div class="pull-right">
                                <?= Html::a(
                                    'Выйти',
                                    ['/usr/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <!--li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li-->
            </ul>
        </div>
    </nav>
</header>