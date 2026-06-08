<?php

namespace App\Models;

use App\Models\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * N8nWebhook model – WhatsApp bot integration configuration.
 */
class N8nWebhook extends Model
{
    use Tenantable;

    protected $fillable = ['school_id', 'webhook_url', 'secret_token', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
