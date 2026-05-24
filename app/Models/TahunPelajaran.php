<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunPelajaran extends Model
{
    protected $table = 'tahun_pelajaran';

    protected $fillable = ['tahun', 'semester', 'mulai', 'selesai', 'is_aktif'];

    protected function casts(): array
    {
        return [
            'mulai' => 'date',
            'selesai' => 'date',
            'is_aktif' => 'boolean',
        ];
    }

    public static function aktif(): ?self
    {
        return static::where('is_aktif', true)->latest('mulai')->first();
    }

    public function label(): string
    {
        return $this->tahun.' - '.$this->semester;
    }
}
