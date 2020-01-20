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
        DB::table('roles')->insert(["title" => "Πελάτης"]);
        DB::table('roles')->insert(["title" => "Διαχειριστής"]);
        DB::table('roles')->insert(["title" => "Τεχνικός"]);
        DB::table('roles')->insert(["title" => "Admin"]);
        DB::table('roles')->insert(["title" => "Super Admin"]);
        factory(App\Client::class, 30)->create();
        for ($i = 1; $i < 31; $i++) {
            if (!file_exists(storage_path("/Clients/" . "my_client_" . $i)) && !is_dir(storage_path("/Clients/" . "my_client_" . $i))) {
                $entry = "my_client_" . $i;
                mkdir(storage_path("/Clients/" . $entry));
                DB::table('clients')->where('id', $i)->update(["foldername" => $entry]);
            }
        }
        factory(App\Manufacturer::class, 5)->create();
        factory(App\Mark::class, 5)->create();
        factory(App\Device::class, 10)->create();
        factory(App\DamageType::class, 5)->create();
        factory(App\Damage::class, 10)->create();
        //Super Admin
        DB::table('users')->insert(["firstname" => "Demo", "lastname" => "Progressnet", "email" => "demo@progressnet.gr", "password" => bcrypt("demo"), "telephone" => "2111828724", "active" => true]);
        //$admin_id = DB::table('users')->where('email', 'testadmin@progressnet.gr')->value('id');
        $admin_id = DB::table('users')->where('email', 'demo@progressnet.gr')->value('id');
        DB::table('role_user')->insert(["role_id" => 5, "user_id" => $admin_id]);
        //create tech
        DB::table('users')->insert(["firstname" => "technician", "lastname" => "Progressnet", "email" => "tech@progressnet.gr", "password" => bcrypt("demo"), "telephone" => "2111828724", "active" => true]);
        $admin_id = DB::table('users')->where('email', 'tech@progressnet.gr')->value('id');
        DB::table('role_user')->insert(["role_id" => 3, "user_id" => $admin_id]);

        for ($i = 1; $i < 11; $i++) {
            DB::table('calendar')->insert(["name" => "βλάβη", "type" => "damages", "damage_id" => $i]);
        }
    }
}
