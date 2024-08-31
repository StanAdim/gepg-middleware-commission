<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $bill_id
 * @property string $sub_sp_code [SubSpCode] Service Provider Sub Code
 * @property string $gfs_code [GfsCode] Government Finance Statistics Code
 * @property string $bill_item_ref [BillItemRef] Item reference as deemed relevant by institution e.g., student registration number, customer account
 * @property string $bill_item_amt [BillItemAmt] Bill item amount can be decimal with precision of two. Always with billed currency.
 * @property string $bill_item_eqv_amt [BillItemEqvAmt] Bill item equivalent amount should be equal to TZS equivalent of the bill item amount. Can be decimal with precision of two.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bill $bill
 * @method static \Illuminate\Database\Eloquent\Builder|BillItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillItem whereBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillItem whereBillItemAmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillItem whereBillItemEqvAmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillItem whereBillItemRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillItem whereGfsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillItem whereSubSpCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'sub_sp_code',
        'gfs_code',
        'bill_item_ref',
        'bill_item_amt',
        'bill_item_eqv_amt',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }
}
