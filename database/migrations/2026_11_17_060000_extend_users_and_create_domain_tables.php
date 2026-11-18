<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('channel');
            $table->string('code');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('availability_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('availability_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_closed')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'date']);
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->unsignedSmallInteger('age')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'phone']);
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('status')->default('scheduled');
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'starts_at']);
        });

        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name');
            $table->string('generic_name')->nullable();
            $table->string('strength')->nullable();
            $table->string('form')->nullable();
            $table->string('company')->nullable();
            $table->string('external_id')->nullable()->index();
            $table->string('source')->default('seed');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->index('brand_name');
            $table->index('generic_name');
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->date('prescribed_on');
            $table->unsignedSmallInteger('patient_age')->nullable();
            $table->text('chief_complaints')->nullable();
            $table->text('on_examination')->nullable();
            $table->text('advice')->nullable();
            $table->unsignedSmallInteger('follow_up_days')->nullable();
            $table->string('status')->default('final');
            $table->string('share_token')->unique();
            $table->timestamps();
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->nullable()->constrained()->nullOnDelete();
            $table->string('brand_name');
            $table->string('strength')->nullable();
            $table->string('form')->nullable();
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->string('duration')->nullable();
            $table->string('instructions')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('availability_exceptions');
        Schema::dropIfExists('availability_rules');
        Schema::dropIfExists('verification_codes');
    }
};
