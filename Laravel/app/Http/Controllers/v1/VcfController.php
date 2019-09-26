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

class VcfController extends Controller
{
    public function export(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "task" => "required|string",
            "email" => "required|string|email"
        ]);

        if($validator->fails())
        {
            return response()->json(["message" => $validator->errors()->first()],422);
        }


        $variable = "";

        if($request->task == "managers")
        {
            $managers = ManagerResource::collection(Manager::all());
            foreach($managers as $manager)
            {
                $vcard = new VCard();

                $vcard->addName($manager->lastname, $manager->firstname);
                $vcard->addEmail($manager->email);
                $vcard->addRole('Διαχειριστής');
                $vcard->addPhoneNumber($manager->telephone, 'TELEPHONE');
                $vcard->addPhoneNumber($manager->telephone2, 'TELEPHONE2');
                $vcard->addPhoneNumber($manager->mobile, 'MOBILE');
                $variable .= $vcard->getOutput();

                $myfile = fopen(public_path()."\VCF\managers.vcf", "w") or die("Unable to open file!");
                fwrite($myfile, $variable);
                fclose($myfile);

                $email = new PHPMailer();
                $email->SetFrom('atlenergy@mail.gr', 'ATLEnergy'); //Name is optional
                $email->Subject   = 'Λίστα Διαχειριστών';
                $email->Body      = 'Αποστολή Λίστας Διαχειριστών';
                $email->AddAddress($request->email);

                $file_to_attach = public_path()."/VCF";

                $email->AddAttachment( $file_to_attach , 'managers.vcf' );

                $email->Send();

                unlink(public_path()."/VCF/managers.vcf");

                return response()->json(["message" => "Η λίστα πελατών εστάλη επιτυχώς στη διεύθυνση ".$request->email],200);
            }
        }


        if($request->task == "clients")
        {
            $clients = ClientResource::collection(Client::all());
            foreach($clients as $client)
            {
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

                $myfile = fopen(public_path()."\VCF\clients.vcf", "w") or die("Unable to open file!");
                fwrite($myfile, $variable);
                fclose($myfile);

                $email = new PHPMailer();
                $email->SetFrom('atlenergy@mail.gr', 'ATLEnergy'); //Name is optional
                $email->Subject   = 'Λίστα Πελατών';
                $email->Body      = 'Αποστολή Λίστα Πελατών';
                $email->AddAddress($request->email);

                $file_to_attach = public_path()."/VCF";

                $email->AddAttachment( $file_to_attach , 'clients.vcf' );

                $email->Send();

                unlink(public_path()."/VCF/clients.vcf");

                return response()->json(["message" => "Η λίστα πελατών εστάλη επιτυχώς στη διεύθυνση ".$request->email],200);


            }



        }
    }
}
