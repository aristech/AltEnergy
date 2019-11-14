<?php

namespace App\Http\CustomClasses\v1;

use App\Note;
use App\Damage;
use App\Http\Resources\DamageResource;
use App\Http\Resources\NoteResource;

//#TODO : Insert herefor services as well
class SendMail
{


    private $reminderDmg = array();
    private $reminderEvt = array();
    public $test;

    public $notifications = array();

    public $message;


    public function checktime($diff)
    {
        $time = strtotime($diff) - 10800; //prod server time
        $this->test = date("F j, Y, g:i a", strtotime($diff));
        $now = time();
        $minutes = ($time - $now) / 60;

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
        $damages = Damage::where("status", "Μη Ολοκληρωμένη")->where('appointment_start', '!=', null)->get();

        if (count($damages) > 0) {
            foreach ($damages as $damage) {
                $diff = $damage["appointment_start"];

                if ($this->checktime($diff) <= 30 && $this->checktime($diff) > 0) {
                    array_push($this->notifications, DamageResource::make($damage));

                    // $obj = new \stdClass();
                    // $obj->type = $damage["type"]["name"];
                    // $obj->client = $damage["client"]["firstname"]." ".$damage["client"]["lastname"];
                    // $obj->address = "<a href='http://maps.google.com/?q=".$damage["client"]["address"].",".$damage["client"]["location"].",".$damage["client"]["zipcode"]."'>".$damage["client"]["address"].",".$damage["client"]["location"].",".$damage["client"]["zipcode"]."</a>";

                    // if($damage['client']['telephone'] != null)
                    // {
                    //     $obj->tel = "<a href='tel:".$damage['client']['telephone']."'>".$damage['client']['telephone']."</a>";
                    // }
                    // elseif($damage['client']['telephone2'] != null)
                    // {
                    //     $obj->tel = "<a href='tel:".$damage['client']['telephone2']."'>".$damage['client']['telephone2']."</a>";
                    // }
                    // elseif($damage['client']['mobile'] != null)
                    // {
                    //     $obj->tel = "<a href='tel:".$damage['client']['mobile']."'>".$damage['client']['mobile']."</a>";
                    // }
                    // else
                    // {
                    //     $obj->tel = "N/A";
                    // }

                    // $appointment = explode('T',$damage['appointment_start']);
                    // $appointment = explode('.',$appointment[1]);

                    // $obj->date = $appointment[0];
                    // array_push($this->reminderDmg,$obj);
                }
            }
        }
    }

    public function getNotes()
    {
        $notes = Note::where('dateTime_start', '!=', null)->get();
        if (count($notes) > 0) {
            foreach ($notes as $note) {
                $diff = $note["dateTime_start"];
                if ($this->checktime($diff) <= 30 && $this->checktime($diff) > 0) {
                    // $obj = new \stdClass();
                    // $obj->type = $event["title"];

                    // $appointment = explode('T', $event["event_start"]);
                    // $appointment = explode('.', $appointment[1]);
                    // $obj->date = $appointment[0];

                    array_push($this->notifications, NoteResource::make($note));
                }
            }
        }
    }

    public function createMessage()
    {
        if ($this->reminderEvt || $this->reminderDmg) {
            $this->message = "<html><head></head><body><div><center>";
            $this->message .= "<h3>Ραντεβού κανονισμένα εντός των 30 λεπτών</h3><br><br>";

            if (count($this->reminderDmg) != 0) {
                $this->message .= "<h4>Ραντεβού για βλάβες</h4>";
                $this->message .= "<table border='2'>";
                $this->message .= "<tr><th>Τυπος Βλάβης</th><th>Όνομα Πελάτη</th><th>Διεύθυνση</th><th>Τηλέφωνο Επικοινωνίας</th><th>Ωρα Ραντεβού</th></tr>";

                foreach ($this->reminderDmg as $dmg) {
                    $this->message .= "<tr><td>" . $dmg->type . "</td><td>" . $dmg->client . "</td><td>" . $dmg->address . "</td><td>" . $dmg->tel . "</td><td>" . $dmg->date . "</td></tr>";
                }

                $this->message .= "</table><br><br>";
            }

            if (count($this->reminderEvt) != 0) {
                $this->message .= "<h4>Λοιπές Δραστηριότητες</h4>";
                $this->message .= "<table border='2' >";
                $this->message .= "<tr><th>Δραστηριότητα</th><th>Ωρα Ραντεβού</th></tr>";

                foreach ($this->reminderEvt as $evt) {
                    $this->message .= "<tr><td>" . $evt->type . "</td><td>" . $evt->date . "</td></tr>";
                }

                $this->message .= "</table><br><br>";
            }

            $this->message .= "</center></div></body></html>";
        }
    }
}
