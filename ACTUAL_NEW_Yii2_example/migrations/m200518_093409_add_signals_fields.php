<?php

use app\components\Migration;

class m200518_093409_add_signals_fields extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%parsing_project}}', 'signals_enabled', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('{{%parsing_project}}', 'items_per_hour_available', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('{{%parsing_project}}', 'errors_per_hour_available', $this->integer()->notNull()->defaultValue(0));

        Yii::$app->cache->delete('#prc_parsing_project#');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%parsing_project}}', 'signals_enabled');
        $this->dropColumn('{{%parsing_project}}', 'items_per_hour_available');
        $this->dropColumn('{{%parsing_project}}', 'errors_per_hour_available');

        Yii::$app->cache->delete('#prc_parsing_project#');
    }
}
