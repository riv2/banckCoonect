<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200902_120016_nomenclature_document_table extends Migration
{
    const NOMENCLATURE_DOCUMENT_ENTITY_ID = 88;
    const NOMENCLATURE_DOCUMENT_ITEM_ENTITY_ID = 89;
    private $_permissionsToAdd = [
        'app\models\reference\NomenclatureDocument.read' => 'Просмотр документа номенклатур',
        'app\models\reference\NomenclatureDocumentItem.read' => 'Просмотр номенклатуры документа',
    ];

    public function safeUp()
    {
        $this->createTable('{{%nomenclature_document}}', [
            'id' => $this->uuid()->notNull(),
            'name' => $this->string(),
            'status_id' => $this->integer()->defaultValue(0),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);
        $this->addPk('{{%nomenclature_document}}', ['id']);
        $this->addFK('{{%nomenclature_document}}', 'status_id', '{{%status}}', 'id');
        $this->addFK('{{%nomenclature_document}}', 'created_user_id', '{{%user}}', 'id');
        $this->addFK('{{%nomenclature_document}}', 'updated_user_id', '{{%user}}', 'id');

        $this->insert('{{%entity}}', [
            'id' => self::NOMENCLATURE_DOCUMENT_ENTITY_ID,
            'name' => 'Документ номенклатуры',
            'alias' => 'NomenclatureDocument',
            'class_name' => 'app\models\reference\NomenclatureDocument',
            'action' => 'nomenclature-document',
            'entity_type' => 'reference',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);
        $this->insert('{{%entity}}', [
            'id' => self::NOMENCLATURE_DOCUMENT_ITEM_ENTITY_ID,
            'name' => 'Номенклатура документа',
            'alias' => 'NomenclatureDocumentItem',
            'class_name' => 'app\models\pool\NomenclatureDocumentItem',
            'action' => 'nomenclature-document-item',
            'entity_type' => 'pool',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);
        $this->db->createCommand()->resetSequence('{{%entity}}')->execute();
        Yii::$app->cache->delete('#prc_entity#');

        $this->createTable('{{%nomenclature_document_item}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp(),
            'nomenclature_document_id' => $this->uuid()->notNull(),
            'item_id' => $this->uuid()->notNull(),
            'min_margin' => $this->double(),
            'rrp_regulations' => $this->boolean()->notNull()->defaultValue(false),
            'price_variation_modifier' => $this->float(),
        ]);
        $this->addPk('{{%nomenclature_document_item}}', ['id']);
        $this->addFK('{{%nomenclature_document_item}}', 'nomenclature_document_id',
            '{{%nomenclature_document}}', 'id');
        $this->addFK('{{%nomenclature_document_item}}', 'item_id',
            '{{%item}}', 'id');

        $this->addColumn('{{%project}}', 'nomenclature_document_id', $this->uuid());
        $this->addFK('{{%project}}', 'nomenclature_document_id',
            '{{%nomenclature_document}}', 'id');

        $auth = Yii::$app->authManager;
        foreach ($this->_permissionsToAdd as $name => $description) {
            $permission = new \yii\rbac\Permission([
                'name' => $name,
                'description' => $description
            ]);
            $auth->add($permission);
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        foreach ($this->_permissionsToAdd as $name => $description) {
            $permission = $auth->getPermission($name);
            if ($permission) {
                $auth->remove($permission);
            }
        }

        $this->delete('{{%entity}}', ['id' => self::NOMENCLATURE_DOCUMENT_ITEM_ENTITY_ID]);
        $this->delete('{{%entity}}', ['id' => self::NOMENCLATURE_DOCUMENT_ENTITY_ID]);
        Yii::$app->cache->delete('#prc_entity#');

        $this->dropColumn('{{%project}}', 'nomenclature_document_id');

        $this->dropTable('{{%nomenclature_document_item}}');
        $this->dropTable('{{%nomenclature_document}}');
    }
}
