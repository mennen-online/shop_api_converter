<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entity_fields', function (Blueprint $table) {
            $table
                ->foreign('entity_id')
                ->references('id')
                ->on('entities')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(!app()->environment('testing')) {
            Schema::table('entity_fields', function (Blueprint $table) {
                $table->dropForeign(['entity_id']);
            });
        }
    }
};
