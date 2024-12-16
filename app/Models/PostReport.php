<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostReport extends Model
{
    use HasFactory;

    protected $table = 'post_report';

    public $timestamps = false;

    protected $primaryKey = 'report_id';

    /**
     * Report relationship.
     */
    public function report(): BelongsTo {
        return $this->belongsTo(Report::class, 'report_id');
    }

    /**
     * Reported post relationship.
     */
    public function post(): BelongsTo {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
