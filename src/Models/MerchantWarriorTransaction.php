<?php
namespace Sharnw\AvoRedMerchantWarrior\Models;

use AvoRed\Framework\Database\Models\Order;
use Illuminate\Database\Eloquent\Model;

class MerchantWarriorTransaction extends Model
{
    protected $fillable = ['transaction_id', 'transaction_ref', 'order_id'];

    /**
     * Transaction belongs to one order.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
