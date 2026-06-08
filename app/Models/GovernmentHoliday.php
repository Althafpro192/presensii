<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * GovernmentHoliday model – synced from external API.
 */
class GovernmentHoliday extends Model
{
    protected $fillable = ['holiday_date', 'name', 'is_national', 'year'];

    protected function casts(): array
    {
        return [
            'holiday_date' => 'date',
            'is_national' => 'boolean',
            'year' => 'integer',
        ];
    }
}
