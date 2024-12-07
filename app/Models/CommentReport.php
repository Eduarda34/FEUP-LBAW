<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentReport extends Model
{
    use HasFactory;

    protected $table = 'comment_report';

    public $timestamps = false;

    protected $primaryKey = 'report_id';

    /**
     * Reporter relationship.
     */
    public function reporter(): BelongsTo {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Reported comment relationship.
     */
    public function comment(): BelongsTo {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}
