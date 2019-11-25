<?php

namespace App\Http\CustomClasses\v1;

use App\Note;
use App\Damage;
use App\Http\Resources\DamageResource;
use App\Http\Resources\NoteResource;
use App\Service;
use App\Http\Resources\ServiceResource;
use DB;

//#TODO : Insert herefor services as well
class SendMail
{


    private $reminderDmg = array();
    private $reminderService = array();
    private $reminderNt = array();
    public $test;

    public $notifications = array();

    public $message;

    public $dmgCount = 0;
    public $serviceCount = 0;
    public $noteCount = 0;


    public function checktime($diff)
    {
        //$temp = explode(".", $diff);
        //$diffe = str_replace("T", " ", $temp[0]);
        $time = strtotime($diff); //prod server time
        $this->test = date("F j, Y, g:i a", $time);
        $now = time();
        $minutes = ($time - $now) / 60;

        return $minutes;
    }

    public function sendMail()
    {
        if (count($this->notifications) > 0) {

            $timestamp_current = time();
            $last_mail_timestamp = DB::table('last_reminder_mail')->where('last_timestamp', '!=', null)->first();

            if (!$last_mail_timestamp) {
                //$to = 'sales@atlenergy.gr';
                $to = 'manentis.gerasimos@outlook.com';

                $subject = 'Υπενθύμιση Ραντεβού εντός του διαστήματος της Μισης Ωρας(Αυτόματο μήνυμα)';

                $headers = "From: " . "reminder@atlenergy.gr" . "\r\n";
                //$headers .= "CC:aris@progressnet.gr\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                $message = $this->message;

                mail($to, $subject, $message, $headers);
                DB::table('last_reminder_email')->insert(['last_timestamp' => time()]);
            } else {
                $difference = ($timestamp_current - $last_mail_timestamp) / 60;
                if ($difference <= 30) {
                    //$to = 'sales@atlenergy.gr';
                    $to = 'manentis.gerasimos@outlook.com';

                    $subject = 'Υπενθύμιση Ραντεβού εντός του διαστήματος της Μισης Ωρας(Αυτόματο μήνυμα)';

                    $headers = "From: " . "reminder@atlenergy.gr" . "\r\n";
                    //$headers .= "CC:aris@progressnet.gr\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                    $message = $this->message;
                    mail($to, $subject, $message, $headers);
                    DB::table('last_reminder_email')->where("id", 1)->update(['last_timestamp' => time()]);
                }
            }
        }
    }

    public function getDamages()
    {
        $damages = Damage::where("status", "Μη Ολοκληρωμένη")->where('appointment_start', '!=', null)->get();

        if (count($damages) > 0) {
            foreach ($damages as $damage) {
                $diff = $damage["appointment_start"];

                if ($this->checktime($diff) <= 30 && $this->checktime($diff) > 0) {
                    array_push($this->notifications, DamageResource::make($damage));
                    $this->dmgCount = $this->dmgCount + 1;
                    $obj = new \stdClass();
                    $obj->type = $damage["type"]["name"];
                    $obj->client = $damage["client"]["firstname"] . " " . $damage["client"]["lastname"];
                    $obj->address = "<a href='http://maps.google.com/?q=" . $damage["client"]["address"] . "," . $damage["client"]["location"] . "," . $damage["client"]["zipcode"] . "'>" . $damage["client"]["address"] . "," . $damage["client"]["location"] . "," . $damage["client"]["zipcode"] . "</a>";

                    if ($damage['client']['telephone'] != null) {
                        $obj->tel = "<a href='tel:" . $damage['client']['telephone'] . "'>" . $damage['client']['telephone'] . "</a>";
                    } elseif ($damage['client']['telephone2'] != null) {
                        $obj->tel = "<a href='tel:" . $damage['client']['telephone2'] . "'>" . $damage['client']['telephone2'] . "</a>";
                    } elseif ($damage['client']['mobile'] != null) {
                        $obj->tel = "<a href='tel:" . $damage['client']['mobile'] . "'>" . $damage['client']['mobile'] . "</a>";
                    } else {
                        $obj->tel = "N/A";
                    }

                    $appointment = explode('T', $damage['appointment_start']);
                    $appointment = explode('.', $appointment[1]);

                    $obj->date = $appointment[0];
                    array_push($this->reminderDmg, $obj);
                }
            }
        }
    }

    public function getServices()
    {
        $services = Service::where("status", "Μη Ολοκληρωμένο")->where('appointment_start', '!=', null)->get();

        if (count($services) > 0) {
            foreach ($services as $service) {
                $diff = $service["appointment_start"];

                if ($this->checktime($diff) <= 30 && $this->checktime($diff) > 0) {
                    array_push($this->notifications, ServiceResource::make($service));
                    $this->serviceCount = $this->serviceCount + 1;
                    $obj = new \stdClass();
                    $obj->type = $service["type"]["name"];
                    $obj->client = $service["client"]["firstname"] . " " . $service["client"]["lastname"];
                    $obj->address = "<a href='http://maps.google.com/?q=" . $service["client"]["address"] . "," . $service["client"]["location"] . "," . $service["client"]["zipcode"] . "'>" . $service["client"]["address"] . "," . $service["client"]["location"] . "," . $service["client"]["zipcode"] . "</a>";

                    if ($service['client']['telephone'] != null) {
                        $obj->tel = "<a href='tel:" . $service['client']['telephone'] . "'>" . $service['client']['telephone'] . "</a>";
                    } elseif ($service['client']['telephone2'] != null) {
                        $obj->tel = "<a href='tel:" . $service['client']['telephone2'] . "'>" . $service['client']['telephone2'] . "</a>";
                    } elseif ($service['client']['mobile'] != null) {
                        $obj->tel = "<a href='tel:" . $service['client']['mobile'] . "'>" . $service['client']['mobile'] . "</a>";
                    } else {
                        $obj->tel = "N/A";
                    }

                    $appointment = explode('T', $service['appointment_start']);
                    $appointment = explode('.', $appointment[1]);

                    $obj->date = $appointment[0];
                    array_push($this->reminderService, $obj);
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

                    array_push($this->notifications, NoteResource::make($note));

                    $obj = new \stdClass();
                    $obj->type = $note["title"];
                    $obj->importance = $note["importance"];
                    $appointment = explode('T', $note["dateTime_start"]);
                    $appointment = explode('.', $appointment[1]);
                    $obj->date = $appointment[0];

                    array_push($this->reminderNt, $obj);
                }
            }
        }
    }

    public function createMessage()
    {
        if (count($this->notifications) > 0) {
            $this->message = "<html><head></head><body><div><center>";
            $this->message .= "<h3>Ραντεβού κανονισμένα εντός των 30 λεπτών</h3><br><br>";

            if (count($this->reminderDmg) != 0) {
                $this->message .= "<h4>Ραντεβού για βλάβες</h4>";
                $this->message .= "<table border='2'>";
                $this->message .= "<tr><th>Τυπος Βλάβης</th><th>Όνομα Πελάτη</th><th>Διεύθυνση</th><th>Τηλέφωνο Επικοινωνίας</th><th>Ωρα Ραντεβού</th></tr>";

                foreach ($this->reminderDmg as $dmg) {

                    $timestamp = strtotime($dmg->date) + 2 * 60 * 60;

                    $time = date('H:i', $timestamp);

                    $this->message .= "<tr><td>" . $dmg->type . "</td><td>" . $dmg->client . "</td><td>" . $dmg->address . "</td><td>" . $dmg->tel . "</td><td>" . $time . "</td></tr>";
                }

                $this->message .= "</table><br><br>";
            }

            if (count($this->reminderService) != 0) {
                $this->message .= "<h4>Ραντεβού για Σερβις</h4>";
                $this->message .= "<table border='2'>";
                $this->message .= "<tr><th>Τυπος Σέρβις</th><th>Όνομα Πελάτη</th><th>Διεύθυνση</th><th>Τηλέφωνο Επικοινωνίας</th><th>Ωρα Ραντεβού</th></tr>";

                foreach ($this->reminderService as $serv) {

                    $timestamp = strtotime($serv->date) + 2 * 60 * 60;

                    $time = date('H:i', $timestamp);

                    $this->message .= "<tr><td>" . $serv->type . "</td><td>" . $serv->client . "</td><td>" . $serv->address . "</td><td>" . $serv->tel . "</td><td>" . $time . "</td></tr>";
                }

                $this->message .= "</table><br><br>";
            }

            if (count($this->reminderNt) != 0) {
                $this->message .= "<h4>Λοιπές Δραστηριότητες</h4>";
                $this->message .= "<table border='2' >";
                $this->message .= "<tr><th>Δραστηριότητα</th><th>Σημαντικότητα</th><th>Ωρα Ραντεβού</th></tr>";

                foreach ($this->reminderNt as $evt) {

                    $timestamp = strtotime($evt->date) + 2 * 60 * 60;

                    $time = date('H:i', $timestamp);

                    switch ($evt->importance) {
                        case 0:
                            $importance = "Υψηλή";
                            break;
                        case 1:
                            $importance = "Μέτρια";
                            break;
                        default:
                            $importance = "Χαμηλή";
                    }

                    $this->message .= "<tr><td>" . $evt->type . "</td><td>" . $importance . "</td><td>" . $time . "</td></tr>";
                }

                $this->message .= "</table><br><br>";
            }

            $this->message .= "</center></div></body></html>";
        }
    }
}
