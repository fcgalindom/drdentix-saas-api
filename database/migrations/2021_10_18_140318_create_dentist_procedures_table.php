<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dentist_procedures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('procedure_id');
            $table->unsignedBigInteger('dentist_id');
            $table->timestamps();

            $table->foreign('procedure_id')->references('id')->on('procedures');
            $table->foreign('dentist_id')->references('id')->on('dentists');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dentist_procedures');
    }
};
