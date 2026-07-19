<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->date('day');
            $table->string('hour');
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('patient_id')->constrained('patients');
            $table->unsignedBigInteger('dentist_procedure_id');
            $table->string('state')->default('Activo');
            $table->double('pay')->default(0);
            $table->tinyInteger('type_state')->default(0);
            $table->timestamps();

            $table->foreign('dentist_procedure_id')->references('id')->on('dentist_procedures');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
