<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_data', function (Blueprint $table) {
            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('shops')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

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
        Schema::table('shop_data', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropForeign(['entity_id']);
        });
    }
};
