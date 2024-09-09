<?php

namespace App\Models;

use App\Enums\BillState;
use App\Events\BillCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property string $description
 * @property int $user_id
 * @property string $phone_number
 * @property string $customer_name
 * @property string $customer_email
 * @property string $approved_by
 * @property string $amount
 * @property string $ccy
 * @property int $payment_option
 * @property string $expires_at
 * @property string $status_code
 * @property string|null $paid_date
 * @property string|null $sp_code
 * @property string|null $customer_cntr_num
 * @property string|null $GrpBillId
 * @property string|null $SpGrpCode
 * @property string|null $psp_code
 * @property string|null $psp_name
 * @property string|null $trx_id
 * @property string|null $pay_ref_id
 * @property string|null $bill_amt
 * @property string|null $paid_amt
 * @property string|null $coll_acc_num
 * @property string|null $trx_dt_tm
 * @property string|null $usd_pay_chnl
 * @property string|null $trd_pty_trx_id
 * @property string|null $pyr_cell_num
 * @property string|null $pyr_name
 * @property string|null $pyr_email
 * @property string|null $rsv1
 * @property string|null $rsv2
 * @property string|null $rsv3
 * @property int $status
 * @property int $payment_order_id
 * @property string|null $request_content
 * @property string|null $callback_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property BillState $state
 * @method static \Illuminate\Database\Eloquent\Builder|Bill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereBillAmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCallbackUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCcy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCollAccNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCustomerCntrNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereGrpBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePaidAmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePaidDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePayRefId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePaymentOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePaymentOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePspCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePspName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePyrCellNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePyrEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePyrName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereRequestContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereRsv1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereRsv2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereRsv3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereSpCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereSpGrpCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereTrdPtyTrxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereTrxDtTm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereTrxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUsdPayChnl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUserId($value)
 * @mixin \Eloquent
 */
class Bill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'user_id',
        'phone_number',
        'customer_name',
        'customer_email',
        'approved_by',
        'amount',
        'ccy',
        'payment_option',
        'status_code',
        'paid_date',
        'sp_code',
        'cust_cntr_num',
        'GrpBillId',
        'SpGrpCode',
        'psp_code',
        'psp_name',
        'trx_id',
        'pay_ref_id',
        'bill_amt',
        'paid_amt',
        'bill_pay_opt',
        'coll_acc_num',
        'trx_dt_tm',
        'usd_pay_chnl',
        'trd_pty_trx_id',
        'pyr_cell_num',
        'pyr_name',
        'pyr_email',
        'rsv1',
        'rsv2',
        'rsv3',
        'status',
        'payment_order_id',
        'request_content',
        'expires_at',
        'callback_url',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'saved' => BillCreated::class,
    ];
}
