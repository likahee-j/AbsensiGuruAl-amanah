<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $rows) {}

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['No', 'Nama Guru', 'Tanggal', 'Jam Masuk', 'Status', 'Latitude', 'Longitude'];
    }

    /**
     * @param  \App\Models\Attendance  $row
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->user->name ?? '-',
            $row->date instanceof \Carbon\Carbon ? $row->date->format('Y-m-d') : (string) $row->date,
            $row->check_in_time ?: '-',
            ucfirst(str_replace('_', ' ', $row->status)),
            $row->latitude,
            $row->longitude,
        ];
    }
}
