<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        \App\Company::factory(1000)->create()->each(function ($company) {
            $company->users()->saveMany(
                \App\User::factory(50)->make()
            );
        });
    }
}
