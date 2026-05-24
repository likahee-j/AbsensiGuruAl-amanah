<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'school_name',
        'phone',
        'email',
        'address',
        'logo',
        'latitude',
        'longitude',
        'radius_meters',
        'check_in_start',
        'check_in_end',
        'late_threshold',
        'check_out_start',
        'check_out_end',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'radius_meters' => 'integer',
            'updated_at' => 'datetime',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'school_name' => 'Sekolah Al-Amanah',
                'latitude' => -6.2,
                'longitude' => 106.816666,
                'radius_meters' => 200,
                'check_in_start' => '06:00:00',
                'check_in_end' => '09:00:00',
                'late_threshold' => '07:15:00',
                'check_out_start' => '14:00:00',
                'check_out_end' => '18:00:00',
            ]
        );
    }
}
