<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('ec_customer_used_coupons')) {
            Schema::create('ec_customer_used_coupons', function (Blueprint $table) {
                $table->integer('discount_id')->unsigned();
                $table->integer('customer_id')->unsigned();
                $table->primary(['discount_id', 'customer_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ec_customer_used_coupons');
    }
};
