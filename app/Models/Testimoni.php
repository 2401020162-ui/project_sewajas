<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimoni extends Model
{
    protected $table = 'testimoni';

    protected $fillable = [
        'user_id',
        'jas_id',
        'rating',
        'komentar',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jas(): BelongsTo
    {
        return $this->belongsTo(Jas::class, 'jas_id');
    }
}
