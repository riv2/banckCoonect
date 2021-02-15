<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixFieldsOnPayDocuments extends Migration
{

    public function __construct() {
        // Register ENUM type
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::table('pay_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->comment('Order number')->change();
            $table->unsignedInteger('user_id')->change();
        });

        Schema::table('pay_documents_student_disciplines', function (Blueprint $table) {
            $table->unsignedInteger('pay_document_id')->change();
            $table->unsignedInteger('student_discipline_id')->change();
        });

        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->unsignedInteger('id')->change();
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_documents', function (Blueprint $table) {
            $table->bigInteger('order_id')->comment('Order number')->change();
            $table->integer('user_id')->change();
        });

        Schema::table('pay_documents_student_disciplines', function (Blueprint $table) {
            $table->integer('pay_document_id')->change();
            $table->integer('student_discipline_id')->change();
        });

        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->integer('id')->change();
        });
    }
}
