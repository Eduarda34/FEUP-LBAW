<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemManager extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'sm_id';

    /**
     * Define a relationship to the User model.
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
