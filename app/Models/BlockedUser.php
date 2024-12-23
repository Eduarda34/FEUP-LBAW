<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedUser extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'blocked_id';

    /**
     * Define a relationship to the User model.
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'blocked_id');
    }

    /**
     * Define a relationship to the Report model.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_id');
    }
}
