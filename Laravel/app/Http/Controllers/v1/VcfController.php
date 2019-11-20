<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;
use App\Manager;
use Validator;
use JeroenDesloovere\VCard\VCard;
use App\Http\Resources\ManagerResource;
use App\Http\Resources\ClientResource;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\User;
use App\Http\Resources\UserResource;


class VcfController extends Controller
{
    public function export(Request $request)
    {
        if ($request->user()->role()->first()->id < 3) {
            return response()->json(["message" => "Ο συγκεκριμένος χρήστης δεν έχει πρόσβαση στο πεδία αυτό"], 401);
        }

        $validator = Validator::make($request->all(), [
            "task" => "required|string",
            "email" => "required|string|email"
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 422);
        }

        $variable = "";

        if ($request->task == "managers") {
            $managers = ManagerResource::collection(Manager::all());
            foreach ($managers as $manager) {
                $vcard = new VCard();

                $vcard->addName($manager->lastname, $manager->firstname);
                $vcard->addEmail($manager->email);
                $vcard->addRole('Διαχειριστής');
                $vcard->addPhoneNumber($manager->telephone, 'TELEPHONE');
                $vcard->addPhoneNumber($manager->telephone2, 'TELEPHONE2');
                $vcard->addPhoneNumber($manager->mobile, 'MOBILE');
                $variable .= $vcard->getOutput();
            }

            $myfile = fopen(storage_path() . "/VCF/managers.vcf", "w") or die("Unable to open file!");
            fwrite($myfile, $variable);
            fclose($myfile);

            $email = new PHPMailer();
            $email->CharSet = "UTF-8";
            $email->SetFrom('atlenergy@mail.gr', 'ATLEnergy'); //Name is optional
            $email->Subject   = 'Λίστα Διαχειριστών';
            $email->Body      = 'Αποστολή Λίστας Διαχειριστών';
            $email->AddAddress($request->email);

            $file_to_attach = storage_path() . "/VCF/managers.vcf";

            $email->AddAttachment($file_to_attach, 'managers.vcf');

            $email->Send();

            unlink(storage_path() . "/VCF/managers.vcf");

            return response()->json(["message" => "Η λίστα καταχωρημένων διαχειριστών εστάλη επιτυχώς στη διεύθυνση " . $request->email], 200);
        }
        if ($request->task == "clients") {
            $clients = ClientResource::collection(Client::all());
            foreach ($clients as $client) {
                $vcard = new VCard();

                $vcard->addName($client->lastname, $client->firstname);
                $vcard->addEmail($client->email);
                $vcard->addPhoneNumber($client->telephone, 'Τηλέφωνο');
                $vcard->addPhoneNumber($client->telephone2, 'Τηλέφωνο2');
                $vcard->addPhoneNumber($client->mobile, 'Κινητό');
                $vcard->addRole('Πελάτης');
                // $vcard->addPhoneNumber(1234121212, 'PREF;WORK');
                // $vcard->addPhoneNumber(123456789, 'WORK');
                $vcard->addAddress(null, null, $client->address, $client->location, null, $client->zipcode, 'Ελλάδα');
                $variable .= $vcard->getOutput();
            }

            $myfile = fopen(storage_path() . "/VCF/clients.vcf", "w") or die("Unable to open file!");
            fwrite($myfile, $variable);
            fclose($myfile);

            $email = new PHPMailer();
            $email->CharSet = "UTF-8";
            $email->SetFrom('atlenergy@mail.gr', 'ATLEnergy'); //Name is optional
            $email->Subject   = 'Λίστα Πελατών';
            $email->Body      = 'Αποστολή Λίστα Πελατών';
            $email->AddAddress($request->email);

            $file_to_attach = storage_path() . "/VCF/clients.vcf";

            $email->AddAttachment($file_to_attach, 'clients.vcf');

            $email->Send();

            unlink(storage_path() . "/VCF/clients.vcf");

            return response()->json(["message" => "Η λίστα πελατών εστάλη επιτυχώς στη διεύθυνση " . $request->email], 200);
        }

        if ($request->task == "users") {
            $users = User::all();
            foreach ($users as $user) {
                $vcard = new VCard();

                $vcard->addName($user->lastname, $user->firstname);
                $vcard->addEmail($user->email);
                $vcard->addRole('ATL/' . $user->role()->first()->title);
                $vcard->addPhoneNumber($user->telephone, 'TELEPHONE');
                $vcard->addPhoneNumber($user->telephone2, 'TELEPHONE2');
                $vcard->addPhoneNumber($user->mobile, 'MOBILE');
                $variable .= $vcard->getOutput();
            }

            $myfile = fopen(storage_path() . "/VCF/users.vcf", "w") or die("Unable to open file!");
            fwrite($myfile, $variable);
            fclose($myfile);

            $email = new PHPMailer();
            $email->CharSet = "UTF-8";
            $email->SetFrom('atlenergy@mail.gr', 'ATLEnergy'); //Name is optional
            $email->Subject   = 'Λίστα χρηστών του συστήματος';
            $email->Body      = 'Αποστολή Λίστας Χρηστών';
            $email->AddAddress($request->email);

            $file_to_attach = storage_path() . "/VCF/users.vcf";

            $email->AddAttachment($file_to_attach, 'users.vcf');

            $email->Send();

            unlink(storage_path() . "/VCF/users.vcf");

            return response()->json(["message" => "Η λίστα χρηστών του συστήματος εστάλη επιτυχώς στη διεύθυνση " . $request->email], 200);
        }
    }
}
