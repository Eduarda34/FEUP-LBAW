<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowNotification extends Model
{
    use HasFactory;

    protected $table = 'follow_notification';

    public $timestamps = false;

    protected $primaryKey = 'notification_id';

    /**
     * Notification relationship.
     */
    public function notification(): BelongsTo {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    /**
     * Notified follower relationship.
     */
    public function follower(): BelongsTo {
        return $this->belongsTo(User::class, 'follower_id');
    }
}
