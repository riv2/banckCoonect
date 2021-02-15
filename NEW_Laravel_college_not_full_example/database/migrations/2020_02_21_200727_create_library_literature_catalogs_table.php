<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLibraryLiteratureCatalogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('library_literature_catalogs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('media');
            $table->string('literature_type');
            $table->string('publication_type');
            $table->year('publication_year');
            $table->string('isbn')->nullable();
            $table->string('ydk')->nullable();
            $table->string('bbk')->nullable();
            $table->string('author');
            $table->string('more_authors')->nullable();
            $table->string('language');
            $table->integer('number_pages');
            $table->string('key_words')->nullable();
            $table->decimal('cost', 10, 2);
            $table->date('receipt_date');
            $table->string('source_income');
            $table->string('e_books_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('library_literature_catalogs');
    }
}
