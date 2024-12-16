<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReport extends Model
{
    use HasFactory;

    protected $table = 'user_report';

    public $timestamps = false;

    protected $primaryKey = 'report_id';

    /**
     * Report relationship.
     */
    public function report(): BelongsTo {
        return $this->belongsTo(Report::class, 'report_id');
    }

    /**
     * Reported user relationship.
     */
    public function reported(): BelongsTo {
        return $this->belongsTo(User::class, 'reported_id');
    }
}
