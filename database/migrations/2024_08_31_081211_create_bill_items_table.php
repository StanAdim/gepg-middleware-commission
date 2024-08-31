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
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
            $table->string('sub_sp_code', 10)
                ->comment('[SubSpCode] Service Provider Sub Code');
            $table->string('gfs_code', 20)
                ->comment('[GfsCode] Government Finance Statistics Code');
            $table->string('bill_item_ref', 50)
                ->comment('[BillItemRef] Item reference as deemed relevant by institution e.g., student registration number, customer account');
            $table->decimal('bill_item_amt', 32, 2)
                ->comment('[BillItemAmt] Bill item amount can be decimal with precision of two. Always with billed currency.');
            $table->decimal('bill_item_eqv_amt', 32, 2)
                ->comment('[BillItemEqvAmt] Bill item equivalent amount should be equal to TZS equivalent of the bill item amount. Can be decimal with precision of two.');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
