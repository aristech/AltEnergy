<?php

use Illuminate\Database\Seeder;
use App\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(UsersTableSeeder::class);
        // DB::table('roles')->insert(["title"=>"Πελάτης"]);
        // DB::table('roles')->insert(["title"=>"Διαχειριστής"]);
        // DB::table('roles')->insert(["title"=>"Τεχνικός"]);
        // DB::table('roles')->insert(["title"=>"Admin"]);
        // DB::table('roles')->insert(["title"=>"Super Admin"]);
        factory(App\Manufacturer::class,5)->create();
        factory(App\Mark::class,5)->create();
        factory(App\Device::class,10)->create();
        factory(App\DamageType::class,5)->create();
        factory(App\Damage::class,25)->create();
    }
}
