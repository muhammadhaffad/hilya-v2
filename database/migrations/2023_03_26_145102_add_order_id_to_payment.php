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
        Schema::table('payments', function (Blueprint $table) {
            $table->after('id', function (Blueprint $table) {
                $table->uuid('order_code');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'code')) {
                $table->dropColumn('code');
            }
            if (Schema::hasColumn('payments', 'code_order')) {
                $table->dropColumn('code_order');
            }
            if (Schema::hasColumn('payments', 'order_code')) {
                $table->dropColumn('order_code');
            }
        });
    }
};
