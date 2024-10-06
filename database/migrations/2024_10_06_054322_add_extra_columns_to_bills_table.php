<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->string('status_description')->nullable()->after('status_code');
            $table->string('ack_id')->nullable()->after('status');
            $table->string('ReqId')->nullable()->after('status');
            $table->string('bill_pay_opt')->nullable()->after('paid_amt');
            $table->integer('entry_cnt')->nullable()->after('sp_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            //
        });
    }
};
