<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDimensionsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer("length")->after("variation")->default(0);
            $table->integer("breadth")->after("length")->default(0);
            $table->integer("height")->after("breadth")->default(0);
            $table->decimal("weight", 10, 2)->after("height")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn("length");
            $table->dropColumn("breadth");
            $table->dropColumn("height");
            $table->dropColumn("weight");
        });
    }
}
