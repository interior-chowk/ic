<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Model\Order;

class CreateShiprocketCouriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shiprocket_couriers', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(Order::class, 'order_id');
            $table->string('shiprocket_order_id')->nullable();
            $table->string('shipment_id')->nullable();
            $table->string('status')->nullable();
            $table->string('awb_code')->nullable();
            $table->string('courier_company_id')->nullable();
            $table->string('courier_name')->nullable();
            $table->boolean('onboarding_completed_now')->default(0);
            $table->json('scans')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shiprocket_couriers');
    }
}
