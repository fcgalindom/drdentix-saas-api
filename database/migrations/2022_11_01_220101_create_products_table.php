<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('active_principle');
            $table->string('concentration');
            $table->integer('amount');
            $table->string('pharmaceutical_form');
            $table->string('commercial_presentation');
            $table->string('medication_unit');
            $table->string('batch');
            $table->string('health_register_invima');
            $table->date('expiration_date');
            $table->string('semaphore');
            $table->date('date_of_admission');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
