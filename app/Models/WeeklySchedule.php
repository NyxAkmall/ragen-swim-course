<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklySchedule extends Model
{
    protected $fillable = [
        "program_id", "coach_id", "location_id", "title",
        "swimming_level", "day_of_week", "start_date", "start_time",
        "end_time", "capacity", "is_active", "notes",
    ];

    protected function casts(): array
    {
        return [
            "day_of_week" => "integer",
            "capacity" => "integer",
            "is_active" => "boolean",
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }
}
