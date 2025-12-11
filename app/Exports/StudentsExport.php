<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    protected $students;

    public function __construct(Collection $students)
    {
        $this->students = $students;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // O'quvchi ma'lumotlari va hardcode qilingan parolni qo'shish
        return $this->students->map(function ($student) {
            return [
                'ID' => $student->id,
                'Ism' => $student->first_name,
                'Familya' => $student->last_name,
                'Username' => $student->name,
                'Email' => $student->email,
                'Sinfi' => $student->classRelation->name ?? 'N/A',
                // Yangi o'quvchilar uchun default parol
                'Parol' => '12345678', 
            ];
        });
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'ID',
            'Ism',
            'Familya',
            'Username',
            'Email',
            'Sinfi',
            'Parol',
        ];
    }
}