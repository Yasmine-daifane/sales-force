<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommercialVisitController;
use App\Http\Controllers\FactureController;

use App\Http\Controllers\CommercialDashboardController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('visits', CommercialVisitController::class);
Route::resource('users', UserController::class);

Route::resource('factures', FactureController::class);
Route::get('factures-export', [FactureController::class, 'export'])->name('factures.export');
Route::get('factures-export-pdf', [FactureController::class, 'exportPDF'])->name('factures.exportPDF');

// Route::post('/factures/scan', [FactureController::class, 'scan'])->name('factures.scan');
Route::post('/factures/scan', [FactureController::class, 'scan'])->name('factures.scan');


Route::get('/test-factures', function () {
    return \App\Models\Facture::select('prix', 'departement', 'date', 'type', 'societe')->get();
});



Route::middleware(['auth'])->group(function () {
    Route::get('/visits', [CommercialVisitController::class, 'index'])->name('visits.index');
    Route::get('/visits/create', [CommercialVisitController::class, 'create'])->name('visits.create');
    Route::post('/visits', [CommercialVisitController::class, 'store'])->name('visits.store');
});


