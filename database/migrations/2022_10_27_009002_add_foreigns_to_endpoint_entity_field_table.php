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
        Schema::table('endpoint_entity_field', function (Blueprint $table) {
            $table
                ->foreign('entity_field_id')
                ->references('id')
                ->on('entity_fields')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('endpoint_id')
                ->references('id')
                ->on('endpoints')
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
        if (! app()->environment('testing')) {
            Schema::table('endpoint_entity_field', function (Blueprint $table) {
                $table->dropForeign(['entity_field_id']);
                $table->dropForeign(['endpoint_id']);
            });
        }
    }
};
