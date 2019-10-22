<?php

namespace App\Http\CustomClasses\v1;

class GreekDate
{

    private $dateArray;
    public function __construct($date)
    {
        $this->dateArray = explode(" ",$date);
    }
    public function dayToGreek()
    {
        switch($this->dateArray[0])
        {
            case "Mon":
            $this->dateArray[0] = "Δευτερα";
            break;
            case "Tue":
            $this->dateArray[0] = "Τρίτη";
            break;
            case "Wed":
            $this->dateArray[0] = "Τετάρτη";
            break;
            case "Thu":
            $this->dateArray[0] = "Πέμπτη";
            break;
            case "Frid":
            $this->dateArray[0] = "Παρασκευή";
            break;
            case "Sat":
            $this->dateArray[0] = "Σάββατο";
            break;
            case "Sun":
            $this->dateArray[0] = "Κυριακή";
            break;
            default:
            break;


        }
    }










}
