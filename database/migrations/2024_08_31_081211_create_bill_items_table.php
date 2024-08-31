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
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade')
                  ->comment('[BillId] Foreign key referencing the parent bill');
            $table->string('ref_bill_id', 50)
                  ->comment('[RefBillId] Reference Bill ID');
            $table->string('sub_sp_code', 10)
                  ->comment('[SubSpCode] Service Provider Sub Code');
            $table->string('gfs_code', 20)
                  ->comment('[GfsCode] Government Finance Statistics Code');
            $table->string('bill_item_ref', 50)
                  ->comment('[BillItemRef] Item reference as deemed relevant by institution e.g., student registration number, customer account');
            $table->string('use_item_ref_on_pay', 1)
                  ->comment('[UseItemRefOnPay] The value should be “N”');
            $table->decimal('bill_item_amt', 32, 2)
                  ->comment('[BillItemAmt] Bill item amount can be decimal with precision of two. Always with billed currency.');
            $table->decimal('bill_item_eqv_amt', 32, 2)
                  ->comment('[BillItemEqvAmt] Bill item equivalent amount should be equal to TZS equivalent of the bill item amount. Can be decimal with precision of two.');
            $table->decimal('bill_item_misc_amt', 32, 2)
                  ->comment('[BillItemMiscAmt] Bill item miscellaneous Amount can be decimal with precision of two. Always with bill item currency. Default: 0.00');

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
