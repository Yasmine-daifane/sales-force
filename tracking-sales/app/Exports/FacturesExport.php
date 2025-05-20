<?php

namespace App\Exports;

use App\Models\Facture;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Log;

class FacturesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $factures = Facture::all();
        Log::info('Exporting factures: ' . $factures->count());
        return $factures;
    }

    /**
     * @param Facture $facture
     * @return array
     */
    public function map($facture): array
    {
        // Format the data as needed before exporting
        return [
            $facture->prix,
            $facture->departement,
            $facture->date,
            $facture->type,
            $facture->societe,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Prix',
            'Département',
            'Date',
            'Type',
            'Société'
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EEEEEE'],
                ],
            ],
        ];
    }
}
