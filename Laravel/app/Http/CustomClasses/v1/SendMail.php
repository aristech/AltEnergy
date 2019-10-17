<?php

namespace App\Http\CustomClasses\v1;
use App\Eventt;
use App\Damage;

//#TODO : Insert herefor services as well
class SendMail
{
    private $reminderDmg=array();
    private $reminderEvt=array();
    private $time;

    public $message;


    public function checktime($diff)
    {
        $years = floor($diff / (365*60*60*24));


        // To get the month, subtract it with years and
        // divide the resultant date into
        // total seconds in a month (30*60*60*24)
        $months = floor(($diff - $years * 365*60*60*24)
                                    / (30*60*60*24));


        // To get the day, subtract it with years and
        // months and divide the resultant date into
        // total seconds in a days (60*60*24)
        $days = floor(($diff - $years * 365*60*60*24 -
                    $months*30*60*60*24)/ (60*60*24));


        // To get the hour, subtract it with years,
        // months & seconds and divide the resultant
        // date into total seconds in a hours (60*60)
        $hours = floor(($diff - $years * 365*60*60*24
            - $months*30*60*60*24 - $days*60*60*24)
                                        / (60*60));


        // To get the minutes, subtract it with years,
        // months, seconds and hours and divide the
        // resultant date into total seconds i.e. 60
        $minutes = floor(($diff - $years * 365*60*60*24
                - $months*30*60*60*24 - $days*60*60*24
                                - $hours*60*60)/ 60);

        return $minutes;
    }

    public function sendMail()
    {
        $to = 'manentis.gerasimos@outlook.com';

        $subject = 'Υπενθύμιση Ραντεβού εντός του διαστήματος της Μισης Ωρας';

        $headers = "From: " . "reminder@atlenergy.gr" . "\r\n";
        $headers .= "CC:aris@progressnet.gr\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $message = $this->message;


        mail($to, $subject, $message, $headers);
    }

    public function getDamages()
    {
        $damages = Damage::where("status","Μη Ολοκληρωμένη")->where('appointment_start','!=',null)->get();

        if(count($damages) > 0)
        {
            foreach($damages as $damage)
            {
                $diff = strtotime($damage["appointment_start"]) - strtotime('now');

                if($this->checktime($diff) <=30 && $this->checktime($diff) > 0)
                {
                    $obj = new \stdClass();
                    $obj->type = $damage["type"]["name"];
                    $obj->client = $damage["client"]["firstname"]." ".$damage["client"]["lastname"];
                    $obj->address = "<a href='http://maps.google.com/?q=".$damage["client"]["address"].",".$damage["client"]["location"].",".$damage["client"]["zipcode"]."'>".$damage["client"]["address"].",".$damage["client"]["location"].",".$damage["client"]["zipcode"]."</a>";

                    if($damage['client']['telephone'] != null)
                    {
                        $obj->tel = "<a href='tel:".$damage['client']['telephone']."'>".$damage['client']['telephone']."</a>";
                    }
                    elseif($damage['client']['telephone2'] != null)
                    {
                        $obj->tel = "<a href='tel:".$damage['client']['telephone2']."'>".$damage['client']['telephone2']."</a>";
                    }
                    elseif($damage['client']['mobile'] != null)
                    {
                        $obj->tel = "<a href='tel:".$damage['client']['mobile']."'>".$damage['client']['mobile']."</a>";
                    }
                    else
                    {
                        $obj->tel = "N/A";
                    }

                    $appointment = explode('T',$damage['appointment_start']);
                    $appointment = explode('.',$appointment[1]);

                    $obj->date = $appointment[0];
                    array_push($this->reminderDmg,$obj);
                }
            }
        }
    }

        public function getEvents()
        {
            $events = Eventt::where('status','Μη Ολοκληρωμένο')->where('event_start','!=',null)->get();
            if(count($events) > 0)
            {
                foreach($events as $event)
                {
                   $diff = strtotime($event["event_start"]) - strtotime('now');
                    if( $this->checktime($diff) <= 30 && $this->checktime($diff) > 0)
                    {
                        $obj = new \stdClass();
                        $obj->type = $event["title"];

                        $appointment = explode('T',$event["event_start"]);
                        $appointment = explode('.',$appointment[1]);
                        $obj->date = $appointment[0];

                        array_push($this->reminderEvt,$obj);
                    }
                }
            }
        }

        public function createMessage()
        {
            if($this->reminderEvt||$this->reminderDmg)
            {
                $this->message = "<html><head></head><body><div><center>";
                $this->message .= "<h3>Ραντεβού κανονισμένα εντός των 30 λεπτών</h3><br><br>";

                if(count($this->reminderDmg) != 0)
                {
                    $this->message .= "<h4>Ραντεβού για βλάβες</h4>";
                    $this->message .= "<table border='2'>";
                    $this->message .= "<tr><th>Τυπος Βλάβης</th><th>Όνομα Πελάτη</th><th>Διεύθυνση</th><th>Τηλέφωνο Επικοινωνίας</th><th>Ωρα Ραντεβού</th></tr>";

                    foreach($this->reminderDmg as $dmg)
                    {
                        $this->message .= "<tr><td>".$dmg->type."</td><td>".$dmg->client."</td><td>".$dmg->address."</td><td>".$dmg->tel."</td><td>".$dmg->date."</td></tr>";
                    }

                    $this->message .= "</table><br><br>";
                }

                if(count($this->reminderEvt) != 0)
                {
                    $this->message .= "<h4>Λοιπές Δραστηριότητες</h4>";
                    $this->message .= "<table border='2' >";
                    $this->message .= "<tr><th>Δραστηριότητα</th><th>Ωρα Ραντεβού</th></tr>";

                    foreach($this->reminderEvt as $evt)
                    {
                        $this->message .= "<tr><td>".$evt->type."</td><td>".$evt->date."</td></tr>";
                    }

                    $this->message .= "</table><br><br>";
                }

                $this->message .= "</center></div></body></html>";

            }
        }
    }







