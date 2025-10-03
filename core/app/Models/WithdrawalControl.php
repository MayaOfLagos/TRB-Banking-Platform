<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalControl extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'reason',
        'set_by',
    ];

    // Status constants
    const STATUS_ALLOWED = 'allowed';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_RESTRICTED = 'restricted';

    public static function getStatuses()
    {
        return [
            self::STATUS_ALLOWED => 'Allowed',
            self::STATUS_PENDING_REVIEW => 'Pending Review',
            self::STATUS_ON_HOLD => 'On Hold',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_RESTRICTED => 'Restricted',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'set_by');
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_ALLOWED => '<span class="badge badge--success">Allowed</span>',
            self::STATUS_PENDING_REVIEW => '<span class="badge badge--warning">Pending Review</span>',
            self::STATUS_ON_HOLD => '<span class="badge badge--info">On Hold</span>',
            self::STATUS_SUSPENDED => '<span class="badge badge--danger">Suspended</span>',
            self::STATUS_RESTRICTED => '<span class="badge badge--dark">Restricted</span>',
        ];

        return $labels[$this->status] ?? '<span class="badge badge--secondary">Unknown</span>';
    }

    public function isBlocked()
    {
        return $this->status !== self::STATUS_ALLOWED;
    }
}
