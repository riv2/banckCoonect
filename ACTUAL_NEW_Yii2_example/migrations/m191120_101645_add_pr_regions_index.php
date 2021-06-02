<?php

use app\components\Migration;

class m191120_101645_add_pr_regions_index extends Migration
{
    public function up()
    {
        // Old
        $this->db
            ->createCommand("drop index if exists ci_price_refined1;")
            ->execute();
        $this->db
            ->createCommand("drop index if exists ci_price_refined2;")
            ->execute();
        $this->db
            ->createCommand("drop index if exists ci_prc_price_refined_extracted_at;")
            ->execute();

        // New
        $this->db
            ->createCommand("create index ci_prc_price_refined_regions on prc_price_refined using gin (regions);")
            ->execute();
        $this->db
            ->createCommand("create index ci_prc_price_refined_extracted_at on prc_price_refined (extracted_at DESC);")
            ->execute();
        $this->db
            ->createCommand("create index ci_price_refined_calculate on prc_price_refined (item_id asc, source_id asc, competitor_id asc, extracted_at desc) WHERE out_of_stock = false;")
            ->execute();


        $this->addColumn('{{%log_price_calculation}}','regions', $this->string());

    }

    public function down()
    {
        // New
        $this->db
            ->createCommand("drop index ci_prc_price_refined_regions;")
            ->execute();
        $this->db
            ->createCommand("drop index ci_prc_price_refined_extracted_at;")
            ->execute();
        $this->db
            ->createCommand("drop index ci_price_refined_calculate;")
            ->execute();

        // Old
        $this->db
            ->createCommand("create index ci_price_refined1 on prc_price_refined (source_id asc, item_id asc, region_id asc, competitor_id asc, extracted_at desc);")
            ->execute();
        $this->db
            ->createCommand("create INDEX ci_price_refined2  ON prc_price_refined (id, out_of_stock asc, source_id asc, item_id ASC, region_id, extracted_at asc) include (price) WHERE out_of_stock = false;")
            ->execute();
        $this->db
            ->createCommand("create index ci_prc_price_refined_extracted_at on prc_price_refined (extracted_at DESC);")
            ->execute();


        $this->dropColumn('{{%log_price_calculation}}','regions');
    }
}
