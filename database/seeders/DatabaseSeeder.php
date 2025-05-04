<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Product::factory(10)->create();
        
        Role::create(['name' => 'Account Admin']);
        Role::create(['name' => 'Account Api User']);
        Role::create(['name' => 'Portal User']);
        Role::create(['name' => 'Portal Admin']);

        $accounts = Account::factory(5)
            ->has(
                User::factory()
            )->create()
            ->each(function ($account) {
                $account->users->each(function ($user) {
                    $user->assignRole('Account Admin');
                });
            }); 
 
        User::factory(3)
            ->recycle($accounts)
            ->create()
            ->each(function ($user) {
                $user->assignRole('Account Api User');
            });


        User::factory()
            ->recycle($accounts)
            ->create([
                'name' => 'Test User',
                'email' => 'apiuser@example.com',
            ])
            ->assignRole('Account Api User');
 

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])->assignRole('Portal Admin');
    }
}
