<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->decimal('prix', 10, 2);
            $table->string('departement');
            $table->date('date');
            $table->enum('type', ['espece', 'cheque', 'virement']);
            $table->string('societe'); // fixed or custom
            $table->string('file_path')->nullable(); // scanned doc
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
