<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * School model – the core tenant entity.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $logo
 * @property string|null $address
 * @property string $primary_color
 * @property string $secondary_color
 * @property string|null $api_key
 * @property array|null $settings
 * @property \Carbon\Carbon|null $subscription_ends_at
 * @property bool $is_active
 */
class School extends Model
{
    protected $fillable = [
        'name', 'slug', 'logo', 'address', 'primary_color', 'secondary_color',
        'api_key', 'settings', 'subscription_ends_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'subscription_ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Auto-generate API key before creating if not set.
     */
    protected static function booted(): void
    {
        static::creating(function (School $school) {
            if (empty($school->api_key)) {
                $school->api_key = Str::random(32);
            }
            if (empty($school->slug)) {
                $school->slug = Str::slug($school->name);
            }
        });
    }

    // ── Relationships ──────────────────────────────────────────────

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function rfidDevices(): HasMany
    {
        return $this->hasMany(RfidDevice::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(SchoolFeature::class);
    }

    public function holidays(): HasMany
    {
        return $this->hasMany(SchoolHoliday::class);
    }

    public function n8nWebhooks(): HasMany
    {
        return $this->hasMany(N8nWebhook::class);
    }

    // ── Accessors & Helpers ────────────────────────────────────────

    /**
     * Get a specific setting value with fallback.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Get the late threshold for this school.
     */
    public function getLateThreshold(): string
    {
        return $this->getSetting('late_threshold', '07:00:00');
    }

    /**
     * Get the absent threshold for this school.
     */
    public function getAbsentThreshold(): string
    {
        return $this->getSetting('absent_threshold', '15:00:00');
    }

    /**
     * Check if a feature is enabled for this school.
     */
    public function isFeatureEnabled(string $featureName): bool
    {
        return $this->features()
            ->where('feature_name', $featureName)
            ->where('is_enabled', true)
            ->exists();
    }

    /**
     * Check if the subscription is still active.
     */
    public function isSubscriptionActive(): bool
    {
        if (!$this->subscription_ends_at) {
            return true; // No expiry set = unlimited
        }
        return $this->subscription_ends_at->isFuture();
    }
}
