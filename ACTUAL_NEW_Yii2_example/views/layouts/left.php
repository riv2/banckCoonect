<?php
/** @var \app\models\reference\User $user */
/** @var \yii\web\View $this */
use app\components\Menu;
use app\models\enum\HoradricCubeStatus;
use app\models\enum\PriceParsedStatus;
use app\models\enum\Status;
use yii\helpers\Json;

$user = Yii::$app->user->identity;
$fileExchanges = \app\models\register\FileExchange::find()
    ->andWhere([
        'created_user_id'   => Yii::$app->user->identity->getId(),
        'status_id'         => Status::STATUS_ACTIVE,
    ])
    ->indexBy('id')
    ->asArray()
    ->all();
if (!$fileExchanges) {
    $fileExchanges = [];
}
$this->registerJs("window.fileExchange = " . Json::encode($fileExchanges) . ";", \yii\web\View::POS_HEAD);

/** @var \yii\web\Controller $controller */
$controller = $this->context;
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?php echo \cebe\gravatar\Gravatar::widget([
                    'email' => $user->email,
                    'options' => [
                        'alt' => $user->getShortName()
                    ],
                    'size' => 45,
                    'class' => 'img-circle'
                ]); ?>
            </div>
            <div class="pull-left info">
                <p><?=$user->shortName?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> В сети</a>
            </div>
        </div>


        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Поиск..."/>
              <span class="input-group-btn">
                <button type='submit' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <?php 


        $nav = [
            [
                'label'  => 'KPI',
                'icon'   => 'fa fa-bullseye',
                'url'    => '/report-kpi',
                'visible' => Yii::$app->user->can(\app\models\pool\ReportKpi::className() . '.read'),
                'active' => $controller->id === 'report-kpi' && $controller->action->id === 'index',
            ],
            [
                'label'  => 'Сопоставление KPI',
                'icon'   => 'fa fa-bullseye',
                'url'    => '/report-matching',
                'visible' => Yii::$app->user->can(\app\models\pool\ReportMatching::className() . '.read'),
                'active' => $controller->id === 'report-matching' && $controller->action->id === 'index',
            ],
            [
                'label'  => 'Расчёт цен',
                'icon'   => 'fa fa-calculator',
                'url'    => '#',
                'items'  => [
                    [
                        'label' => 'Проекты рачсёта',
                        'icon' => 'fa fa-flag', 'url' => ['/project'],
                        'visible' => Yii::$app->user->can(\app\models\reference\Project::className() . '.read'),
                    ],
                    [
                        'label' => 'Журнал расчётов',
                        'icon' => 'fa fa-book',
                        'url' => ['/crud-project-execution'],
                        'visible' => Yii::$app->user->can(\app\models\document\ProjectExecution::className() . '.read'),
                    ],
                    [
                        'label' => 'Конкуренты проектов',
                        'icon' => 'fa fa-binoculars',
                        'url' => ['/project-competitor'],
                        'visible' => Yii::$app->user->can(\app\models\reference\ProjectCompetitor::className() . '.read'),
                    ],
                    [
                        'label' => 'Ассортимент проектов',
                        'icon' => 'fa fa-cart-arrow-down',
                        'url' => ['/crud-project-item'],
                        'visible' => Yii::$app->user->can(\app\models\reference\ProjectItem::className() . '.read'),
                    ],
                    [
                        'label'  => 'Справочники',
                        'icon'   => 'fa fa-database',
                        'url'    => '#',
                        'items'  => [
                            [
                                'label' => 'Тематики проектов',
                                'icon' => 'fa fa-folder-open',
                                'url' => ['/crud-project-theme'],
                                'visible' => Yii::$app->user->can(\app\models\reference\ProjectTheme::className() . '.read'),
                            ],
                            [
                                'label' => 'Конкуренты проектов',
                                'icon' => 'fa fa-binoculars',
                                'url' => ['/crud-project-competitor'],
                                'visible' => Yii::$app->user->can(\app\models\reference\ProjectCompetitor::className() . '.read'),
                            ],
                            [
                                'label' => 'Документы номенклатур',
                                'icon' => 'fa fa-list',
                                'url' => ['/nomenclature-document'],
                                'visible' => Yii::$app->user->can(\app\models\reference\NomenclatureDocument::className() . '.read'),
                            ],
                        ]
                    ],
                    [
                        'label'  => 'Журналы',
                        'icon'   => 'fa fa-book',
                        'url'    => '#',
                        'items'  => [
                            [
                                'label' => 'Расчитанные цены',
                                'icon' => 'fa fa-rub',
                                'url' => ['/crud-price-calculated'],
                                'visible' => Yii::$app->user->can(\app\models\pool\PriceCalculated::className() . '.read'),
                            ],
                            [
                                'label' => 'Журнал расчета цен',
                                'icon' => 'fa fa-bars',
                                'url' => ['/crud-log-project-execution'],
                                'visible' => Yii::$app->user->can(\app\models\pool\LogProjectExecution::className() . '.read'),
                            ],
                            [
                                'label' => 'Детализация расчет цен',
                                'icon' => 'fa fa-calculator',
                                'url' => ['/crud-log-price-calculation'],
                                'visible' => Yii::$app->user->can(\app\models\pool\LogPriceCalculation::className() . '.read'),
                            ],
                        ]
                    ],
                ]
            ],

            [
                'label'  => 'Парсинг цен',
                'icon'   => 'fa fa-rub',
                'url'    => '#',
                'items'  => [
                    [
                        'label' => 'Состояние парсинга',
                        'icon' => 'fa fa-android',
                        'url' => ['/'],
                        'visible' => Yii::$app->user->can(\app\controllers\SiteController::className() . '.index'),
                    ],
                    [
                        'label' => 'Проекты парсинга',
                        'icon' => 'fa fa-flag',
                        'url' => ['/parsing-project/index','ParsingProject' => ['parsing_type'=> 'normal']],
                        'visible' => Yii::$app->user->can(\app\models\reference\ParsingProject::className() . '.read'),
                    ],
                    [
                        'label' => 'Маски',
                        'icon' => 'fa fa-code',
                        'url' => '/masquerade/index.html',
                        'visible' => Yii::$app->user->can(\app\models\reference\Masks::className() . '.read'),
                    ],
                    [
                        'label' => 'Расписание парсинга',
                        'icon' => 'fa fa-calendar',
                        'url' => ['/schedule/parsing'],
                        'visible' => Yii::$app->user->can(\app\models\reference\Schedule::className() . '.read'),
                    ],
                    [
                        'label' => 'Журнал парсинга',
                        'icon' => 'fa fa-book',
                        'url' => ['/crud-parsing','sort'=>'-created_at','Parsing'=>['is_test'=>0]],
                        'visible' => Yii::$app->user->can(\app\models\register\Parsing::className() . '.read')
                    ],
                    [
                        'label' => 'Отчет по конкурентам',
                        'icon' => 'fa fa-book',
                        'url' => ['/report-parsing'],
                        'visible' => Yii::$app->user->can(\app\models\register\Parsing::className() . '.read')
                    ],
                    [
                        'label' => 'Спарсенные цены',
                        'icon' => 'fa fa-font',
                        'url' => ['/crud-price-parsed'],
                        'visible' => Yii::$app->user->can(\app\models\pool\PriceParsed::className() . '.read'),
                    ],
                    [
                        'label' => 'Обработанные цены',
                        'icon' => 'fa fa-rub',
                        'url' => ['/crud-price-refined'],
                        'visible' => Yii::$app->user->can(\app\models\pool\PriceRefined::className() . '.read'),
                    ],
                    ['label' => 'Другие журналы', 'icon' => 'fa fa-book', 'items'=>[
                        //['label' => 'Антикапча', 'icon' => 'fa fa-code', 'url' => ['/crud-anti-captcha-task','sort'=>'-created_at']],
                        [
                            'label' => 'VPN',
                            'icon' => 'fa fa-share',
                            'url' => ['/vpn'],
                            'visible' => Yii::$app->user->can(\app\models\reference\Vpn::className() . '.read'),
                        ],
                        [
                            'label' => 'Прокси',
                            'icon' => 'fa fa-share',
                            'url' => ['/proxy'],
                            'visible' => Yii::$app->user->can(\app\models\register\Proxy::className() . '.read'),
                        ],
                        [
                            'label' => 'Ошибки парсинга',
                            'icon' => 'fa fa-bug',
                            'url' => ['/crud-parsing-error'],
                            'visible' => Yii::$app->user->can(\app\models\pool\ParsingError::className() . 'read'),
                        ],
                        [
                            'label' => 'Буфер (для CD)',
                            'icon' => 'fa fa-cog',
                            'url' => ['/crud-parsing-buffer'],
                            'visible' => Yii::$app->user->can(\app\models\pool\ParsingBuffer::className() . '.read'),
                        ],
                    ]],
                ]
            ],
            ['label' =>
                'Сопоставление',
                'icon' => 'fa fa-book',
                'url'    => '#',
                'items'=>[
                    [
                        'label' => 'Ручной разбор',
                        'icon' => 'fa fa-american-sign-language-interpreting',
                        'url' => ['/horadric-cube/manual','HoradricCube' => ['horadric_cube_status_id'=> HoradricCubeStatus::STATUS_NEW]],
                        'visible' => Yii::$app->user->can(\app\models\register\HoradricCube::className() . '.read'),
                    ],
                    [
                        'label' => 'Проекты сопоставления',
                        'icon' => 'fa fa-flag',
                        'url' => ['/parsing-project/index','ParsingProject' => ['parsing_type'=> 'collecting']],
                        'visible' => Yii::$app->user->can(\app\models\reference\ParsingProject::className() . '.read'),
                    ],
                    [
                        'label' => 'Журнал разбора',
                        'icon' => 'fa fa-book',
                        'url' => ['/horadric-cube/index'],
                        'visible' => Yii::$app->user->can(\app\models\register\HoradricCube::className() . '.read'),
                    ],
                    [
                        'label' => 'Журнал несоответствия',
                        'icon' => 'fa fa-exclamation-triangle',
                        'url' => ['/horadric-cube','HoradricCube' => ['horadric_cube_status_id'=> HoradricCubeStatus::STATUS_WRONG]],
                        'visible' => Yii::$app->user->can(\app\models\register\HoradricCube::className() . '.read'),
                    ],
                    [
                        'label' => 'Автоматически отфильтрованное',
                        'icon' => 'fa fa-filter',
                        'url' => ['/price-parsed','PriceParsed' => ['price_parsed_status_id'=> PriceParsedStatus::COLLECTING_FILTERED_OUT.','.PriceParsedStatus::MATCHING_FILTERED_OUT]],
                        'visible' => Yii::$app->user->can(\app\models\pool\PriceParsed::className() . '.read'),
                    ],
                    [
                        'label' => 'Буффер',
                        'icon' => 'fa fa-cog',
                        'url' => ['/price-parsed','PriceParsed' => ['price_parsed_status_id'=> PriceParsedStatus::COLLECTING_NEW.','.PriceParsedStatus::MATCHING_NEW]],
                        'visible' => Yii::$app->user->can(\app\models\pool\PriceParsed::className() . '.read'),
                    ],
                    [
                        'label' => 'Бренды для фильтрации',
                        'icon' => 'fa fa-book',
                        'url' => ['/crud-brand-filter'],
                        'visible' => Yii::$app->user->can(\app\models\reference\BrandFilter::className() . '.read'),
                    ],
                    [
                        'label' => 'Маски для сопоставления',
                        'icon' => 'fa fa-code',
                        'url' => ['/masks/','Masks' => ['name'=>'Sys.Matching']],
                        'visible' => Yii::$app->user->can(\app\models\reference\Masks::className() . '.read'),
                    ],
                ]
            ],
            [
                'label'  => 'Конкуренты',
                'icon'   => 'fa fa-binoculars',
                'url'    => '#',
                'items'  => [
                    [
                        'label' => 'Конкуренты',
                        'icon' => 'fa fa-binoculars',
                        'url' => ['/competitor'],
                        'visible' => Yii::$app->user->can(\app\models\reference\Competitor::className() . '.read'),
                    ],
                    [
                        'label' => 'Товары конкурентов',
                        'icon' => 'fa fa-barcode',
                        'url' => ['/competitor-item'],
                        'visible' => Yii::$app->user->can(\app\models\reference\CompetitorItem::className() . '.read'),
                    ],
                    [
                        'label' => 'Названия конкурентов',
                        'icon' => 'fa fa-tags',
                        'url' => ['/crud-competitor-shop-name'],
                        'visible' => Yii::$app->user->can(\app\models\reference\CompetitorShopName::className() . '.read'),
                    ],
                    [
                        'label' => 'YMID конкурентов',
                        'icon' => 'fa fa-hashtag',
                        'url' => ['/crud-competitor-shop-index'],
                        'visible' => Yii::$app->user->can(\app\models\reference\CompetitorShopIndex::className() . '.read'),
                    ],
                    [
                        'label' => 'Домены конкурентов',
                        'icon' => 'fa fa-globe',
                        'url' => ['/crud-competitor-shop-domain'],
                        'visible' => Yii::$app->user->can(\app\models\reference\CompetitorShopDomain::className() . '.read'),
                    ],
                ]
            ],
            [
                'label'  => 'Справочники PHub',
                'icon'   => 'fa fa-book',
                'url'    => '#',
                'items'  => [
                    [
                        'label' => 'Товары',
                        'icon' => 'fa fa-shopping-cart',
                        'url' => ['/crud-item'],
                        'visible' => Yii::$app->user->can(\app\models\reference\Item::className() . '.read'),
                    ],
                    [
                        'label' => 'Бренды',
                        'icon' => 'fa fa-amazon',
                        'url' => ['/crud-brand'],
                        'visible' => Yii::$app->user->can(\app\models\reference\Brand::className() . '.read'),
                    ],
                    [
                        'label' => 'Категории',
                        'icon' => 'fa fa-plus-square-o',
                        'url' => ['/crud-category'],
                        'visible' => Yii::$app->user->can(\app\models\reference\Category::className() . '.read'),
                    ],
                    [
                        'label' => 'Регионы',
                        'icon' => 'fa fa-globe',
                        'url' => ['/region','sort' => 'id'],
                        'visible' => Yii::$app->user->can(\app\models\enum\Region::className() . '.read'),
                    ],
                ]
            ],
            [
                'label'  => 'Обработка документов',
                'icon'   => 'fa fa-file-o',
                'url'    => '#',
                'items'  => [
                    [
                        'label' => 'Виды документов',
                        'icon' => 'fa fa-file',
                        'url' => ['/file-settings'],
                        'visible' => Yii::$app->user->can(\app\models\reference\FileProcessingSettings::className() . '.read'),
                    ],
                    [
                        'label' => 'Журнал',
                        'icon' => 'fa fa-book',
                        'url' => ['/file', 'sort' => '-created_at'],
                        'visible' => Yii::$app->user->can(\app\models\register\FileProcessing::className() . '.read'),
                    ],
                    [
                        'label' => 'Загрузить',
                        'icon' => 'fa fa-upload',
                        'url' => ['/file/upload'],
                        'visible' => Yii::$app->user->can(\app\models\register\FileProcessing::className() . '.upload'),
                    ],
                ]
            ],
        ];

        $workingProjects = \app\models\reference\Project::getWorkingProjects();
        if (count($workingProjects) > 0) {
            $workingProjects = array_reverse($workingProjects);
            foreach ($workingProjects as $projectId => $projectName) {
                if (Yii::$app->controller->id == 'project' && $projectId == Yii::$app->request->get('id')) continue;
                array_unshift($nav, [
                    'label'  => 'Назад в '.$projectName,
                    'icon'   => 'fa fa-arrow-left',
                    'title'  => "Вернуться к проекту $projectName",
                    'url'    => ['/project/view', 'id' => $projectId],
                    'color'  => '#fff1c7'
                ]);
                break;
            }
        }

//        # Все справочники
//        $entitiesNav = [];
//        $entityModels = \app\components\base\Entity::getEnumArray();
//
//        # Убираем уже использованные УРЛ из полного списка сущностей
//        foreach ($entityModels as $emid => $entityModel) {
//            $f = false;
//            foreach ($nav as $item) {
//                if (isset($item['items']) && !empty($item['items'])) {
//                    foreach ($item['items'] as $items) {
//                        if (isset($items['url'][0]) && $items['url'][0] == '/crud-' . $entityModel['action']) {
//                            unset($entityModels[$emid]);
//                            $f = true;
//                            break;
//                        }
//                    }
//                } else if ($item['url'][0] && $item['url'][0] == '/crud-' . $entityModel['action']) {
//                    unset($entityModels[$emid]);
//                    $f = true;
//                }
//                if ($f) break;
//            }
//        }
//
//        foreach ($entityModels as $entityModel) {
//            $entitiesNav[] = [
//                'label'     => $entityModel['name'],
//                'icon'      => 'fa fa-bookmark-o',
//                'url'       => ['/crud-'.$entityModel['action']],
//            ];
//        }
//
////        $nav [] = [
////            'label'  => 'Все справочники',
////            'icon'   => 'fa fa-book',
////            'url'    => '#',
////            'items'  => $entitiesNav
////        ];
//
        $nav [] = [
            'label' => 'Администрирование',
            'icon'  => 'fa fa-file-code-o',
            'url'   => '#',
            'items' => [
                [
                    'label' => 'Ошибки',
                    'icon' => 'fa fa-bug',
                    'url' => ['/error/?sort=-created_at'],
                    'visible' => Yii::$app->user->can(\app\models\register\Error::className() . '.read'),
                ],
                [
                    'label' => 'Внешние системы',
                    'icon' => 'fa fa-share-alt',
                    'url' => ['/exchange-system'],
                    'visible' => Yii::$app->user->can(\app\models\reference\ExchangeSystem::className() . '.read'),
                ],
                [
                    'label' => 'Регистр импорта',
                    'icon' => 'fa fa-cloud-download',
                    'url' => ['/crud-exchange-import'],
                    'visible' => Yii::$app->user->can(\app\models\register\ExchangeImport::className() . '.read'),
                ],
                [
                    'label' => 'Пользователи',
                    'icon' => 'fa fa-users',
                    'url' => ['/users'],
                    'visible' => Yii::$app->user->can(\app\models\reference\User::className() . '.read'),
                ],
                [
                    'label' => 'Настройки журналов',
                    'icon' => 'fa fa-gear',
                    'url' => ['/crud-journal-settings'],
                    'visible' => Yii::$app->user->can(\app\models\reference\JournalSettings::className() . '.read'),
                ],
                [
                    'label' => 'Консольные задачи',
                    'icon' => 'fa fa-gear',
                    'url' => ['/console-task'],
                    'visible' => Yii::$app->user->can(\app\models\reference\JournalSettings::className() . '.read'),
                ],
                [
                    'label' => 'Процессы',
                    'icon' => 'fa fa-gear',
                    'url' => ['/crud-task'],
                    'visible' => Yii::$app->user->can(\app\models\reference\JournalSettings::className() . '.read'),
                ],
                [
                    'label' => 'Настройки',
                    'icon' => 'fa fa-gear',
                    'url' => ['/crud-setting'],
                    'visible' => Yii::$app->user->can(\app\models\reference\JournalSettings::className() . '.read'),
                ],
            ]
        ];
        ?>
        <?= Menu::widget(
            [
                'options'   => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items'     => $nav,
            ]
        ) ?>

    </section>

    <div id="file_exchange-tray" class="file_exchange-tray">
    </div>

</aside>
