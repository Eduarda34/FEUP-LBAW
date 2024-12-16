<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoteNotification extends Model
{
    use HasFactory;

    protected $table = 'vote_notification';

    public $timestamps = false;

    protected $primaryKey = 'notification_id';

    /**
     * Notification relationship.
     */
    public function notification(): BelongsTo {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    /**
     * VOted post relationship.
     */
    public function post(): BelongsTo {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Voted comment relationship.
     */
    public function comment(): BelongsTo {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}
