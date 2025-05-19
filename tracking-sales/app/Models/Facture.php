<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;


    protected $fillable = [
        'prix', 'departement', 'date', 'type', 'societe', 'file_path'
    ];
}
