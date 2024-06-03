<?php

use App\Models\Task;
use App\Models\User;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('title');
            $table->text('description');
            $table->boolean('urgent')->default(false);
            $table->string('project')->nullable();
            $table->dateTime('due_date');
            $table->tinyInteger('progress')->default(0);
            $table->string('status')->default('todo');
            $table->string('is_done')->default('pending');
            $table->integer('order_column')->default(0);
            $table->string('text_color')->default('text-white');
            $table->string('bg_color')->default('bg-sky-400');
            $table->timestamps();
        });

        Schema::create('task_user', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Task::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('task_user');
    }
};
