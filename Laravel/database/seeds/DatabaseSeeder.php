<?php

use Illuminate\Database\Seeder;
use App\UsersRoles;
use Faker\Generator as Faker;


date_default_timezone_set('UTC');

class DatabaseSeeder extends Seeder
{
    protected $startUnixTSP;
    protected $startDate;
    protected $endUnixTSP;
    protected $endDate;

    public function getStartDate($var)
    {
        $temp_date = $var->dateTimeBetween('now', '+1 month');
        $this->startUnixTSP = $temp_date->getTimestamp();

        // while($this->startUnixTSP < strtotime('now'))
        // {
        //     $this->startUnixTSP = $var->unixTime($min = 'now', $max = '+1 month');
        // }
        $date =  date("Y-m-d\TH:i:s.u\Z", $this->startUnixTSP);
        $date_array = explode('.',$date);
        $datetime = $date_array[0].'.000Z';

        $this->startDate = $datetime;
        return $datetime;
    }

    public function getEndDate()
    {
        $this->endUnixTSP = $this->startUnixTSP + 2*60*60;
        $date =  date("Y-m-d\TH:i:s.u\Z", $this->endUnixTSP);
        $date_array = explode('.',$date);
        $datetime = $date_array[0].'.000Z';

        $this->endDate = $datetime;
        return $datetime;
    }
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        //$this->call(UsersTableSeeder::class);
        DB::table('roles')->insert(["title"=>"Πελάτης"]);
        DB::table('roles')->insert(["title"=>"Διαχειριστής"]);
        DB::table('roles')->insert(["title"=>"Τεχνικός"]);
        DB::table('roles')->insert(["title"=>"Admin"]);
        DB::table('roles')->insert(["title"=>"Super Admin"]);
        DB::table('users')->insert(["lastname"=>"Admin", "firstname"=>"Progressnet", "email" => "admin@progressnet.gr",	"telephone"=>"21 1182 8724", "password" => bcrypt("admin"),	"active" => true ]);
        DB::table('role_user')->insert(["user_id" => 1, "role_id" => 5]);
        DB::table('users')->insert(["lastname"=>"Lastech1", "firstname"=>"Tech1", "email" => "tech1@progressnet.gr",	"telephone"=>"21 2342 8767", "password" => bcrypt("tech"),	"active" => true ]);
        DB::table('role_user')->insert(["user_id" => 2, "role_id" => 3]);
        DB::table('users')->insert(["lastname"=>"Lastech2", "firstname"=>"Tech2", "email" => "tech2@progressnet.gr",	"telephone"=>"21 1223 8789", "password" => bcrypt("tech"),	"active" => true ]);
        DB::table('role_user')->insert(["user_id" => 3, "role_id" => 3]);
        DB::table('users')->insert(["lastname"=>"Lastech3", "firstname"=>"Tech3", "email" => "tech3@progressnet.gr",	"telephone"=>"21 1444 8555", "password" => bcrypt("tech"),	"active" => true ]);
        DB::table('role_user')->insert(["user_id" => 4, "role_id" => 3]);


        factory(App\Client::class,20)->create();
        for($i = 1; $i < 21; $i++)
        {
            DB::table('clients')->where('id',$i)->update(['foldername' => $i]);
        }
        factory(App\Manufacturer::class,2)->create();
        factory(App\Mark::class,3)->create();
        factory(App\Device::class,3)->create();
        factory(App\DamageType::class,5)->create();
        factory(App\Damage::class,5)->create();
        //end generating fake data
        for($i=1; $i < 6 ; $i++)
        {
            $startdate = $this->getStartDate($faker);
            $endate = $this->getEndDate($faker);
            DB::table('damages')->where('id',$i)->update(['appointment_start'=>$startdate,'appointment_end'=>$endate]);
        }

        factory(App\Note::class,3)->create();
        for($i=1; $i < 4 ; $i++)
        {
            $startdate = $this->getStartDate($faker);
            $endate = $this->getEndDate();
            DB::table('notes')->where('id',$i)->update(['dateTime_start'=>$startdate,'dateTime_end'=>$endate]);
        }

        for($i = 1; $i < 6; $i++)
        {
            DB::table('calendar')->insert(['name' => 'βλάβη', 'type' => 'damages', 'damage_id' => $i]);
        }

        for($i = 1; $i <4; $i++)
        {
            DB::table('calendar')->insert(['name' => 'Σημείωση', 'type' => 'notes', 'note_id' => $i]);
        }


    }

}
