<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SchoolFeature model – toggle features per school.
 */
class SchoolFeature extends Model
{
    protected $table = 'school_feature';

    use Auditable;

    protected $fillable = ['school_id', 'feature_name', 'is_enabled'];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    /**
     * List of all available feature names.
     */
    public const FEATURES = [
        'pelanggaran_siswa',
        'n8n_whatsapp',
        'dashboard_sakit',
        'ekspor_excel',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
