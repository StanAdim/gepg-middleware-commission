<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();

            $table->string('description', 512);
            $table->integer('user_id');
            $table->string('phone_number', 15);
            $table->string('customer_name', 256);
            $table->string('customer_email', 256);
            $table->string('approved_by', 256);
            $table->decimal('amount', 15, 2);
            $table->string('ccy');
            $table->integer('payment_option')->default(1);
            $table->dateTime('expires_at');

            $table->string('status_code');
            $table->timestamp('paid_date')->nullable();
            $table->string('sp_code')->nullable();
            $table->string('customer_cntr_num')->nullable();
            $table->string('GrpBillId')->nullable();
            $table->string('SpGrpCode')->nullable();
            $table->string('psp_code')->nullable();
            $table->string('psp_name')->nullable();
            $table->string('trx_id')->nullable();
            $table->string('pay_ref_id')->nullable();
            $table->decimal('bill_amt', 15, 2)->nullable();
            $table->decimal('paid_amt', 15, 2)->nullable();
            $table->string('coll_acc_num')->nullable();
            $table->timestamp('trx_dt_tm')->nullable();
            $table->string('usd_pay_chnl')->nullable();
            $table->string('trd_pty_trx_id')->nullable();
            $table->string('pyr_cell_num')->nullable();
            $table->string('pyr_name')->nullable();
            $table->string('pyr_email')->nullable();
            $table->string('rsv1')->nullable();
            $table->string('rsv2')->nullable();
            $table->string('rsv3')->nullable();
            $table->boolean('status')->default(1);

            $table->integer('payment_order_id');

            $table->longText('request_content')->nullable();
            $table->string('callback_url', 1024)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
