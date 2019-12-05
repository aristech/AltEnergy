<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\UsersRoles;

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
        // factory(App\Manufacturer::class,5)->create();
        // factory(App\Mark::class,5)->create();
        // factory(App\Device::class,10)->create();
        // factory(App\DamageType::class,5)->create();
        // factory(App\Damage::class,25)->create();
        DB::table('users')->insert(["firstname" => "TestAdmin", "lastname" => "Progressnet", "email" => "testadmin@progressnet.gr", "password" => bcrypt("progressnet"), "telephone" => "2111828724", "active" => true]);
        $admin_id = DB::table('users')->where('email', 'testadmin@progressnet.gr')->value('id');
        DB::table('role_user')->insert(["role_id" => 5, "user_id" => $admin_id]);
    }
}
