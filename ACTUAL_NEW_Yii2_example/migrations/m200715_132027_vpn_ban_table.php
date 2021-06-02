<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200715_132027_vpn_ban_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%parsing_project_vpn_ban}}', [
            'id'       => $this->uuidpk()->notNull(),
            'parsing_project_id' => $this->uuid()->notNull(),
            'vpn_id' => $this->uuid()->notNull(),
            'banned_at' => $this->timestamp(),
        ]);
        $this->addFK('{{%parsing_project_vpn_ban}}', 'parsing_project_id', '{{%parsing_project}}', 'id');
        $this->addFK('{{%parsing_project_vpn_ban}}', 'vpn_id', '{{%vpn}}', 'id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%parsing_project_vpn_ban}}');
    }
}
