<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentMonitoringExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $students;
    protected $availableSubjects;
    protected $schoolName;
    protected $className;
    protected $quarter;

    public function __construct($students, $availableSubjects, $schoolName, $className, $quarter)
    {
        $this->students = $students;
        $this->availableSubjects = $availableSubjects;
        $this->schoolName = $schoolName;
        $this->className = $className;
        $this->quarter = $quarter;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->students as $index => $student) {
            $row = [
                $index + 1,
                $student->first_name . ' ' . $student->last_name,
            ];

            // Fanlar
            foreach ($this->availableSubjects as $subject) {
                $subjectData = $student->subjectsData->get($subject->id);
                if ($subjectData && $subjectData['score'] > 0) {
                    $row[] = $subjectData['grade'];
                    $row[] = $subjectData['score'];
                } else {
                    $row[] = '-';
                    $row[] = '-';
                }
            }

            // Xulqiqo'l
            $row[] = $student->conduct_grade ?? '-';
            $row[] = $student->conduct_score > 0 ? $student->conduct_score : '-';

            // Kitobxonlik
            $row[] = $student->reading_score > 0 ? $student->reading_score : '-';

            // Natijalar
            $row[] = $student->total_score > 0 ? $student->total_score : '-';
            $row[] = $student->average_score > 0 ? $student->average_score : '-';

            // Jami
            $row[] = $student->total_score > 0 ? $student->total_score : '-';
            $row[] = $student->average_score > 0 ? $student->average_score : '-';

            // O'rni
            $row[] = $student->total_score > 0 ? $student->rank : '-';

            $data[] = $row;
        }

        return collect($data);
    }

    public function headings(): array
    {
        $headings = [
            [
                $this->schoolName . ' maktabi',
            ],
            [
                $this->className . ' sinfi o\'quvchilarining ' . $this->quarter . ' monitoring',
            ],
            [], // Bo'sh qator
            [
                '№',
                'O\'quvchining familiyasi va ismi',
            ],
        ];

        // Fanlar
        foreach ($this->availableSubjects as $subject) {
            $headings[3][] = strtoupper($subject->name);
            $headings[3][] = '';
        }

        $headings[3][] = 'XULQI';
        $headings[3][] = '';
        $headings[3][] = 'KITOBXONLIK';
        $headings[3][] = 'UMUMIY';
        $headings[3][] = 'O\'RTACHA';
        $headings[3][] = 'UMUMIY';
        $headings[3][] = 'O\'RTACHA';
        $headings[3][] = 'O\'RNI';

        // Ikkinchi qator (D | B)
        $secondRow = ['', ''];
        foreach ($this->availableSubjects as $subject) {
            $secondRow[] = 'D';
            $secondRow[] = 'B';
        }
        $secondRow[] = 'D';
        $secondRow[] = 'B';
        $secondRow[] = '';
        $secondRow[] = '';
        $secondRow[] = '';
        $secondRow[] = '';
        $secondRow[] = '';
        $secondRow[] = '';

        $headings[] = $secondRow;

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        // Sarlavha
        $sheet->mergeCells('A1:' . $this->getColumnLetter(count($this->headings()[3])) . '1');
        $sheet->mergeCells('A2:' . $this->getColumnLetter(count($this->headings()[3])) . '2');

        $sheet->getStyle('A1:A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Header
        $headerRow = 4;
        $lastColumn = $this->getColumnLetter(count($this->headings()[3]));
        
        $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . ($headerRow + 1))->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'B8CCE4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Ma'lumotlar
        $dataStartRow = $headerRow + 2;
        $dataEndRow = $dataStartRow + count($this->students) - 1;

        $sheet->getStyle('A' . $dataStartRow . ':' . $lastColumn . $dataEndRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // O'quvchi ismlari chapga
        $sheet->getStyle('B' . $dataStartRow . ':B' . $dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Qatorlar balandligi
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);
        
        for ($row = $dataStartRow; $row <= $dataEndRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(30);
        }

        // Merge cells for subjects
        $colIndex = 3; // C ustuni
        foreach ($this->availableSubjects as $subject) {
            $col1 = $this->getColumnLetter($colIndex);
            $col2 = $this->getColumnLetter($colIndex + 1);
            $sheet->mergeCells($col1 . $headerRow . ':' . $col2 . $headerRow);
            $colIndex += 2;
        }

        // Xulqiqo'l merge
        $col1 = $this->getColumnLetter($colIndex);
        $col2 = $this->getColumnLetter($colIndex + 1);
        $sheet->mergeCells($col1 . $headerRow . ':' . $col2 . $headerRow);
        $colIndex += 2;

        // Kitobxonlik, Umumiy, O'rtacha merge
        for ($i = 0; $i < 5; $i++) {
            $col = $this->getColumnLetter($colIndex);
            $sheet->mergeCells($col . $headerRow . ':' . $col . ($headerRow + 1));
            $colIndex++;
        }

        return [];
    }

    public function title(): string
    {
        return substr($this->className . ' - ' . $this->quarter, 0, 31);
    }

    private function getColumnLetter($columnIndex)
    {
        $letter = '';
        while ($columnIndex > 0) {
            $columnIndex--;
            $letter = chr(65 + ($columnIndex % 26)) . $letter;
            $columnIndex = intdiv($columnIndex, 26);
        }
        return $letter;
    }
}