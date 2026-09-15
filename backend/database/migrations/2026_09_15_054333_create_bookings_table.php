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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('slot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('help_type_id')->constrained();
            $table->foreignId('package_purchase_id')->nullable()->constrained();
            $table->string('status')->default('pending'); // pending | confirmed | rejected | rescheduled
            $table->decimal('price', 8, 2);
            $table->text('public_note')->nullable(); // от потребителя, видима в профила
            $table->text('internal_note')->nullable(); // само за админ/едитор
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
