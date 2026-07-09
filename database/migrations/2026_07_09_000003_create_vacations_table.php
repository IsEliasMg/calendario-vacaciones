<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('vacation_date');
            $table->timestamps();

            $table->unique(['employee_id', 'vacation_date']);
            $table->index('vacation_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacations');
    }
};
