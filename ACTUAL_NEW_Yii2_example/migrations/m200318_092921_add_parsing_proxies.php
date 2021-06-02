<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200318_092921_add_parsing_proxies extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%parsing}}', 'proxies', $this->json()->defaultValue(new \yii\db\Expression("'[]'::jsonb")));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%parsing}}', 'proxies');
    }
}
