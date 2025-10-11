<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title', 500);
            $table->enum('type', ['assignment', 'exam', 'reading', 'project']);
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->integer('duration')->default(60); // minutos
            $table->dateTime('deadline');
            $table->enum('status', ['todo', 'in-progress', 'completed'])->default('todo');
            $table->enum('energy', ['low', 'medium', 'high'])->default('medium');
            $table->text('description')->nullable();
            $table->timestamps(); // created_at y updated_at

            // Índices para mejor performance
            $table->index(['user_id', 'status']);
            $table->index('deadline');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
