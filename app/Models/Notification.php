<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Notification extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'notification_id';

    /**
     * User relationship.
     */
    public function owner(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Follow notification relationship.
     */
    public function follow(): HasOne {
        return $this->hasOne(FollowNotification::class, 'notification_id');
    }

    /**
     * Vote notification relationship.
     */
    public function vote(): HasOne {
        return $this->hasOne(VoteNotification::class, 'notification_id');
    }

    /**
     * Comment notification relationship.
     */
    public function comment(): HasOne {
        return $this->hasOne(CommentNotification::class, 'notification_id');
    }

    /**
     * Post notification relationship.
     */
    public function post(): HasOne {
        return $this->hasOne(PostNotification::class, 'notification_id');
    }
}
