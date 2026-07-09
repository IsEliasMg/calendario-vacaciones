<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedTinyInteger('max_people_per_day')->default(3);
            $table->unsignedTinyInteger('max_days_per_employee')->default(15);
            $table->boolean('allow_saturdays')->default(false);
            $table->boolean('allow_sundays')->default(false);
            $table->json('available_years');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
