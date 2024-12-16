<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostNotification extends Model
{
    use HasFactory;

    protected $table = 'post_notification';

    public $timestamps = false;

    protected $primaryKey = 'notification_id';

    /**
     * Notification relationship.
     */
    public function notification(): BelongsTo {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    /**
     * Followed user relationship.
     */
    public function author(): BelongsTo {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Post relationship.
     */
    public function post(): BelongsTo {
        return $this->belongsTo(Post::class, 'post_id');
    }

}
