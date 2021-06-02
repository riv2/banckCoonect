<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m201125_062040_add_console_task_table extends Migration
{
    const CONSOLE_TASK_ENTITY_ID = 90;
    const CONSOLE_TASK_TYPE_ENTITY_ID = 91;
    const CONSOLE_TASK_STATUS_ENTITY_ID = 92;
    private $_consoleTaskStatus = [
        1 => ['Запланирована', 'fa fa-clock-o'],
        2 => ['Выполняется', 'fa fa-spin fa-spinner'],
        3 => ['Выполнена', 'fa fa-check-square-o'],
        4 => ['Отменена', 'fa fa-trash-o'],
        5 => ['Прервана', 'fa-exclamation-triangle'],
    ];

    private $_consoleTaskType = [
        1 => 'Запуск проектов парсинга',
        2 => 'Загрузка пользователей из LDAP',
        3 => 'Загрузка типов из PriceFormer',
        4 => 'Загрузка брендов',
        5 => 'Загрузка категорий',
        6 => 'Загрузка номенклатуры',
        7 => 'Загрузка номенклатуры',
        8 => 'Обновление цен товаров',
        9 => 'Обновление рангов товаров',
        10 => 'Обновление цен товаров конкурентов',
        11 => 'Обработка ошибок для товаров конкурентов',
        12 => 'Обработка цен',
        13 => 'Дедубликация товаров из PDM',
        14 => 'Перепарсинг ошибок сбора',
        15 => 'Очистка старых данных',
        16 => 'Очистка очередей RabbitMQ',
        17 => 'Обработка буфера цен',
        18 => 'Обработка логов подготовки расчёта проекта',
        19 => 'Обработка логов расчёта проекта',
        20 => 'Поиск товаров ВИ',
        21 => 'Обработка спарсенных цен',
        22 => 'Обработка импорта/экспорта файлов',
        23 => 'Расчёт проектов',
        24 => 'Расчёт KPI',
        25 => 'Расчёт KPI сопоставления',
        26 => 'Очистка регионов',
        27 => 'Очистка ВПН',
        28 => 'Обновление данных активных сборов',
        29 => 'Проаналитика: загрузка цен',
        30 => 'Проаналитика: выгрузка урлов',
        31 => 'Смс уведомления о проблемах сбора',
        32 => 'Очистка проксей',
        33 => 'Обработка процессов',
    ];


    public function safeUp()
    {
        $this->createTable('{{%console_task_status}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'icon' => $this->string(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ]);
        foreach ($this->_consoleTaskStatus as $id => $data) {
            $this->insert('{{%console_task_status}}', [
                'id' => $id,
                'name' => $data[0],
                'icon' => $data[1],
            ]);
        }

        $this->createTable('{{%console_task_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ]);
        foreach ($this->_consoleTaskType as $id => $name) {
            $this->insert('{{%console_task_type}}', [
                'id' => $id,
                'name' => $name,
            ]);
        }

        $this->createTable('{{%console_task}}', [
            'id' => $this->uuid(),
            'name' => $this->string()->notNull(),
            'status_id' => $this->integer(1)->defaultValue(0),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),

            'console_task_type_id' => $this->integer()->notNull(),
            'console_task_status_id' => $this->integer()->defaultValue(1),
            'params' => $this->text(),
            'is_repeatable' => $this->boolean()->defaultValue(false),
            'repeat_interval' => $this->integer(),
            'result_text' => $this->text(),
            'result_data' => $this->text(),
            'start_date' => $this->timestamp(),
            'finish_date' => $this->timestamp(),
        ]);
        $this->addPk('{{%console_task}}', ['id']);

        $this->insert('{{%entity}}', [
            'id' => self::CONSOLE_TASK_ENTITY_ID,
            'name' => 'Консольная команда',
            'alias' => 'ConsoleTask',
            'class_name' => 'app\models\reference\ConsoleTask',
            'action' => 'console-task',
            'entity_type' => 'reference',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);
        $this->insert('{{%entity}}', [
            'id' => self::CONSOLE_TASK_TYPE_ENTITY_ID,
            'name' => 'Тип консольной команды',
            'alias' => 'ConsoleTaskType',
            'class_name' => 'app\models\enum\ConsoleTaskType',
            'action' => 'console-task-type',
            'entity_type' => 'enum',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);
        $this->insert('{{%entity}}', [
            'id' => self::CONSOLE_TASK_STATUS_ENTITY_ID,
            'name' => 'Статус консольной команды',
            'alias' => 'ConsoleTaskStatus',
            'class_name' => 'app\models\enum\ConsoleTaskStatus',
            'action' => 'console-task-status',
            'entity_type' => 'enum',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);
        $this->db->createCommand()->resetSequence('{{%entity}}')->execute();
        Yii::$app->cache->delete('#prc_entity#');

        $this->addColumn('{{%schedule}}', 'started', $this->boolean()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%schedule}}', 'started');
        $this->delete('{{%entity}}', ['id' => self::CONSOLE_TASK_ENTITY_ID]);
        $this->delete('{{%entity}}', ['id' => self::CONSOLE_TASK_TYPE_ENTITY_ID]);
        $this->delete('{{%entity}}', ['id' => self::CONSOLE_TASK_STATUS_ENTITY_ID]);
        Yii::$app->cache->delete('#prc_entity#');
        $this->dropTable('{{%console_task}}');
        $this->dropTable('{{%console_task_status}}');
        $this->dropTable('{{%console_task_type}}');
    }
}
