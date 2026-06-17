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
        // 1. Entities table
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Add columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('entity_id')->nullable()->after('id')->constrained('entities')->nullOnDelete();
            $table->boolean('is_entity_manager')->default(false)->after('is_instructor');
        });

        // 3. Add columns to courses table
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('entity_id')->nullable()->after('id')->constrained('entities')->nullOnDelete();
            $table->boolean('is_approved_for_sharing')->default(false)->after('requires_quiz');
        });

        // 4. Groups table
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->timestamps();
        });

        // 5. group_user pivot table
        Schema::create('group_user', function (Blueprint $table) {
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['group_id', 'user_id']);
        });

        // 6. Trails table
        Schema::create('trails', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->timestamps();
        });

        // 7. course_trail pivot table
        Schema::create('course_trail', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('trail_id')->constrained('trails')->cascadeOnDelete();
            $table->integer('position')->default(1);
            $table->primary(['course_id', 'trail_id']);
        });

        // 8. trail_user pivot table
        Schema::create('trail_user', function (Blueprint $table) {
            $table->foreignId('trail_id')->constrained('trails')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['trail_id', 'user_id']);
        });

        // 9. group_trail pivot table
        Schema::create('group_trail', function (Blueprint $table) {
            $table->foreignId('trail_id')->constrained('trails')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['trail_id', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_trail');
        Schema::dropIfExists('trail_user');
        Schema::dropIfExists('course_trail');
        Schema::dropIfExists('trails');
        Schema::dropIfExists('group_user');
        Schema::dropIfExists('groups');

        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['entity_id']);
            $table->dropColumn(['entity_id', 'is_approved_for_sharing']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['entity_id']);
            $table->dropColumn(['entity_id', 'is_entity_manager']);
        });

        Schema::dropIfExists('entities');
    }
};
