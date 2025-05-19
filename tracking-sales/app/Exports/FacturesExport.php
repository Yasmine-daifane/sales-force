<?php

namespace App\Exports;

use App\Models\Facture;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class FacturesExport implements FromCollection , WithHeadings

{
    /**
    * @return \Illuminate\Support\Collection
    */




    public function collection()
    {
        return Facture::select('prix', 'departement', 'date', 'type', 'societe')->get();
    }

    public function headings(): array
    {
        return ['Prix', 'Département', 'Date', 'Type', 'Société'];
    }
}
