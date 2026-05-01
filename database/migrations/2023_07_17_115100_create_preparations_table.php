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
        Schema::create('preparations', function (Blueprint $table) {
            $table->id();
            $table->integer('ppm');
            $table->decimal('quantity', 8, 2);
            $table->integer('cont_hours');
            $table->dateTime('actual_time');
            $table->integer('slices_ton');
            $table->string('shift');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('typePreparation_id')->nullable();
            $table->unsignedBigInteger('station_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('typePreparation_id')->references('id')->on('type_preparations');
            $table->foreign('station_id')->references('id')->on('stations');
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preparations');
    }
};
