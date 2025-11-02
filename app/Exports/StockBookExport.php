<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Style\Color;
use Maatwebsite\Excel\Style\Alignment;
use Maatwebsite\Excel\Style\Border;
use Maatwebsite\Excel\Style\Fill;

class StockBookExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting, WithMapping, WithEvents, RegistersEventListeners, ShouldQueue
{
    /**
     * @var array
     */
    protected $summary = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Create a new export instance.
     *
     * @param array $summary
     * @return void
     */
    public function __construct($summary = [])
    {
        $this->summary = $summary;
    }

    /**
     * Set the data for export.
     *
     * @param array $data
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->data);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Date',
            'Product Code',
            'Product Name',
            'Location',
            'Transaction Type',
            'Reference',
            'Quantity In',
            'Quantity Out',
            'Balance',
            'Created By',
            'Notes',
        ];
    }

    /**
     * @param mixed $row
     * @param mixed $key
     * @return array
     */
    public function map($row): array
    {
        return [
            $row['Date'],
            $row['Product Code'],
            $row['Product Name'],
            $row['Location'],
            $row['Transaction Type'],
            $row['Reference'],
            $row['Quantity In'],
            $row['Quantity Out'],
            $row['Balance'],
            $row['Created By'],
            $row['Notes'],
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'A' => 'yyyy-mm-dd',
            'H' => '#,##0',
            'I' => '#,##0',
            'J' => '#,##0',
            'K' => '#,##0',
        ];
    }

    /**
     * @return array
     */
    public function styles(): array
    {
        return [
            // Style for header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FF4F81BD',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // Style for summary rows
            'A' => function ($row) {
                if (isset($row['Summary'])) {
                    return [
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => 'FFE6F7FF',
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ];
                }
                return [];
            },
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Add summary at the end of the sheet if summary data is available
                if (!empty($this->summary)) {
                    $sheet = $event->sheet->getDelegate();
                    $row = count($this->data) + 5; // Add some spacing

                    // Add summary title
                    $sheet->setCellValue('A' . $row, 'Summary');
                    $sheet->mergeCells('A' . $row . ':K' . $row);
                    $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 14,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => 'FFE6F7FF',
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    // Add summary data
                    $summaryRow = $row + 1;
                    foreach ($this->summary as $key => $value) {
                        $sheet->setCellValue('A' . $summaryRow, $key);
                        $sheet->setCellValue('B' . $summaryRow, $value);
                        $summaryRow++;
                    }
                }
            },
        ];
    }
}
