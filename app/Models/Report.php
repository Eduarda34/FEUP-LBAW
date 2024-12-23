<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Report extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'report_id';

    /**
     * Reporter relationship.
     */
    public function reporter(): BelongsTo {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * User report relationship.
     */
    public function user(): HasOne {
        return $this->hasOne(UserReport::class, 'report_id');
    }

    /**
     * Post report relationship.
     */
    public function post(): HasOne {
        return $this->hasOne(PostReport::class, 'report_id');
    }

    /**
     * Comment report relationship.
     */
    public function comment(): HasOne {
        return $this->hasOne(CommentReport::class, 'report_id');
    }

    /**
     * Define a relationship to the BlockedUser model.
     */
    public function blocked_user(): HasOne
    {
        return $this->hasOne(BlockedUser::class, 'report_id');
    }
}
