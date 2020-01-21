<?php

namespace App\Http\CustomClasses\v1;

use App\User;
//use App\DamageType;
//use App\Client;
use App\Mark;

class TechMail
{

    private static function checkNumeric($num)
    {
        if (is_numeric($num)) {
            return $num;
        } else {
            return 0.00;
        }
    }
    private static function boolToText($bool)
    {
        if ($bool) {
            return "ΝΑΙ";
        } else {
            return "ΟΧΙ";
        }
    }

    private static function createAddrLink($address)
    {
        $temp_address = str_replace(",", "", $address);
        if ($temp_address) {
            return $address;
        } else {
            return "#";
        }
    }

    private static function removeNull($text)
    {
        if ($text === null) {
            return "";
        } else {
            return $text;
        }
    }


    public static function sendToTechs($object, $type, $state)
    {
        //set receivers
        $tech_ids = explode(",", $object->techs);
        $techmails = array();
        foreach ($tech_ids as $id) {
            $mail = User::where("id", $id)->first()["email"];
            array_push($techmails, $mail);
        }

        $to = implode(",", $techmails);
        //end set receivers

        if ($state == "new") {
            $subject = "[ΝΕΑ ΚΑΤΑΧΩΡΗΣΗ - " . $type . "]";
        } else {
            $subject = "[ΕΝΗΜΕΡΩΣΗ ΥΠΑΡΧΟΥΣΑΣ -" . $type . "]";
        }


        $from = "no-reply@atlenergy.com";
        $headers = "From:" . $from . "\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        if ($state == "new") {
            $message = "<b>Σας έχει ανατεθεί νέα εργασία - " . $type . " μέσω της πλατφόρμας ALT Energy</b> <br>";
        } else {
            $message = "<b>Υπήρξε ενημέρωση για  εργασία - " . $type . " που σας έχει ανατεθεί</b><br>";
        }



        $message .= "<br><b>Πληροφορίες:</b><br><br>";

        $message .= "<b>Κατηγορία:</b> " . $type . "<br>";
        $message .= "<b>Ειδος:</b> " . $object->type->name . "<br>";

        $devices_array = explode(',', $object->marks);
        $devices_second_array = array();
        foreach ($devices_array as $dev_id) {
            $dev = Mark::where('id', $dev_id)->first();
            array_push($devices, $dev["manufacturer"]['name'] . "/" . $dev["name"]);
            $devices = implode(" , ", $devices_second_array);
        }
        //$message .= "<b>Συσκευες:</b> " . $object->mark->manufacturer->name . "," . $object->mark->name . "<br>";
        $message .= "<b>Συσκευες:</b> " . $devices . "<br>";

        $message .= "<br><br><b>Στοιχεία Πελάτη:</b> <br><br>";

        $message .= "<b>Ον/μο Πελάτη:</b> " . self::removeNull($object->client->firstname . " " . $object->client->lastname) . "<br>";
        $message .= "<b>Διεύθυνση Πελάτη:</b> " . "<a href='http://maps.google.com/?q=" . self::createAddrLink($object->client->address . "," . $object->client->location) . "'>" . self::removeNull($object->client->address . " " . $object->client->location) . "</a>" . "<br>";
        $message .= "<b>Όροφος:</b> " . self::removeNull($object->client->level) . "<br>";
        $message .= "<b>Τηλέφωνο:</b> " . "<a href='tel:" . self::removeNull($object->client->telephone) . "'>" . self::removeNull($object->client->telephone) . "</a><br>";
        $message .= "<b>Τηλέφωνο 2:</b> " . "<a href='tel:" . self::removeNull($object->client->telephone2) . "'>" . self::removeNull($object->client->telephone2) . "</a><br>";
        $message .= "<b>Κινητό:</b> " . "<a href='tel:" . self::removeNull($object->client->mobile) . "'>" . self::removeNull($object->client->mobile) . "</a><br><br>";

        $message .= "<br><b>Περισσότερες Πληροφορίες σχετικά με εργασία:</b><br><br>";

        if (!$object->appointment_start) {
            $message .= "<b>Αναμονή ραντεβού:</b> ΝΑΙ<br>";
        } else {
            $time = strtotime($object->appointment_start);
            $message .= "<b>Ημερομηνία:</b> " . date('l jS  F Y h:i:s A', $time) . "<br>";
            //l jS \of F Y h:i:s A'

        }

        if ($state == "update") {
            $message .= "<b>Αποχώρηση Τεχνικού:</b> " . self::boolToText($object->technician_left) . "<br>";
            $message .= "<b>Αφηξη Τεχνικού:</b> " . self::boolToText($object->technician_arrived) . "<br>";
            $message .= "<b>Ολοκλήρωση Ραντεβού:</b> " . self::boolToText($object->appointment_completed) . "<br>";
            $message .= "<b>Ανάγκη για νέο Ραντεβού:</b> " . self::boolToText($object->appointment_needed) . "<br>";
            $message .= "<b>Αναμονή ανταλλακτικού:</b> " . self::boolToText($object->supplement_pending) . "<br>";
            if ($type == "βλάβη") {
                $message .= "<b>Εργασία Ολοκληρώθηκε:</b> " . self::boolToText($object->damage_fixed) . "<br>";
            } else {
                $message .= "<b>Εργασία Ολοκληρώθηκε:</b> " . self::boolToText($object->service_done) . "<br>";
            }
        }

        if ($type == "βλάβη") {
            $message .= "<b>Σχόλια σχετικά με εργασία:</b> " . self::removeNull($object->damage_comments) . "<br>";
        } else {
            $message .= "<b>Σχόλια σχετικά με εργασία:</b> " . self::removeNull($object->service_comments) . "<br>";
        }

        $message .= "<b>Γενικά Σχόλια:</b> " . self::removeNull($object->comments) . "<br>";

        //client info
        $total =  $object->cost + self::checkNumeric($object->manager_payment);
        $message .= "<b>Συνολικό Κόστος:</b> " . $total;

        // return $message;
        mail($to, $subject, $message, $headers);
    }
}
