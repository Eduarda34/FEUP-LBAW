<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentNotification extends Model
{
    use HasFactory;

    protected $table = 'comment_notification';

    public $timestamps = false;

    protected $primaryKey = 'notification_id';

    /**
     * Notification relationship.
     */
    public function notification(): BelongsTo {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    /**
     * Notified comment relationship.
     */
    public function comment(): BelongsTo {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    /**
     * Post relationship.
     */
    public function post(): BelongsTo {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Parent comment relationship.
     */
    public function parent_comment(): BelongsTo {
        return $this->belongsTo(Comment::class, 'parent_comment_id');
    }
}
