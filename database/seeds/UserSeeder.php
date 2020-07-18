<?php


use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'      => 'User Test Development',
            'email'     => 'test@test.com',
            'password'  => bcrypt('123123')
        ]);
    }
}
