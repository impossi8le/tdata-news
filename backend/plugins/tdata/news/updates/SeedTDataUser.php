<?php namespace TData\News\Updates;

use Schema;
use October\Rain\Database\Updates\Seeder;
use TData\News\Models\User;

class SeedTDataUser extends Seeder
{
    public function run()
    {
        if (!User::where('login', 'root')->exists()) {
            User::create([
                'login'    => 'root',
                'email'    => 'root@root.ru',
                'password' => 'root', 
            ]);
        }
    }
}
