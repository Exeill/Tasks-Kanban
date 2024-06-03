<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()
            ->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'ADMIN',
        ]);

        // $users = User::factory(50)->create();

        // $tasks = Task::factory(30)
        //     ->recycle($users)
        //     ->create();

        //     $tasks->each(function (Task $task) use ($users)
        //     { 
        //         $task->team()->attach(
        //             $users->shuffle()
        //             ->take(fake()->numberBetween(1,4))
        //             ->pluck('id')
        //         );
        //     });
    }
}
