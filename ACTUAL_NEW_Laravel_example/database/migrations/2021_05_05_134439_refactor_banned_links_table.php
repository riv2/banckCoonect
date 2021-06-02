<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefactorBannedLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('banned_links');

        Schema::create('banned_links', function (Blueprint $table) {
            $table->id();
            $table->string('link_id')->index()->references('id')->on('links');
            $table->string('web_id')->nullable()->index();
            $table->string('pp_id');
            $table->date('date_start');
            $table->date('date_end')->nullable();
            $table->text('comment')->nullable();
            $table->text('evidence')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['link_id', 'web_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // не требуется
    }
}
