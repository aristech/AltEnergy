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
        // if ($request->user()->role()->first()->id < 3) {
        //     return response()->json(["message" => "Ο συγκεκριμένος χρήστης δεν έχει πρόσβαση στο πεδία αυτό"], 401);
        // }

        $validator = Validator::make($request->all(), [
            "task" => "required|string",
            "email" => "required|string|email"
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 422);
        }

        $variable = "";

        if ($request->task == "managers") {
            $managers = Manager::where('telephone', '!=', null)->orWhere('telephone2', '!=', null)->orWhere('mobile', '!=', null)->get();
            if (count($managers) > 0) {
                foreach ($managers as $manager) {

                    $vcard = new VCard();
                    if ($manager['firstname'] != null || $manager['lastname'] != null) {
                        $vcard->addName($manager['lastname'], $manager['firstname']);
                    } elseif ($manager['firstname'] != null || $manager['lastname'] == null) {
                        $vcard->addName("ΑΓΝΩΣΤΟ ΕΠΩΝΥΜΟ", $manager['firstname']);
                    } elseif ($manager['firstname'] == null || $manager['lastname'] != null) {
                        $vcard->addName($manager['lastname'], "ΑΓΝΩΣΤΟ ΟΝΟΜΑ");
                    } else {
                        $vcard->addName("ΑΓΝΩΣΤΟ ΕΠΩΝΥΜΟ", "ΑΓΝΩΣΤΟ ΟΝΟΜΑ");
                    }


                    if ($manager['email'] != null) {
                        $vcard->addEmail($manager['email']);
                    }

                    //$vcard->addRole('Διαχειριστής');
                    if ($manager['telephone'] != null) {
                        $vcard->addPhoneNumber($manager['telephone'], 'HOME');
                    }

                    if ($manager['telephone2'] != null) {
                        $vcard->addPhoneNumber($manager['telephone2'], 'WORK');
                    }

                    if ($manager['mobile'] != null) {
                        $vcard->addPhoneNumber($manager['mobile'], 'CELL');
                    }


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
            } else {
                return response()->json(["message" => "Δεν υπάρχουν τηλέφωνα για να δημιουργηθει VCF"], 422);
            }
        }
        if ($request->task == "clients") {
            $clients = Client::where('telephone', '!=', null)->orWhere('telephone2', '!=', null)->orWhere('mobile', '!=', null)->get();
            if (count($clients) > 0) {
                foreach ($clients as $client) {
                    $vcard = new VCard();
                    if ($client['firstname'] != null || $client['lastname'] != null) {
                        $vcard->addName($client['lastname'], $client['firstname']);
                    } elseif ($client['firstname'] != null || $client['lastname'] == null) {
                        $vcard->addName("ΑΓΝΩΣΤΟ ΕΠΩΝΥΜΟ", $client['firstname']);
                    } elseif ($client['firstname'] == null || $client['lastname'] != null) {
                        $vcard->addName($client['lastname'], "ΑΓΝΩΣΤΟ ΟΝΟΜΑ");
                    } else {
                        $vcard->addName("ΑΓΝΩΣΤΟ ΕΠΩΝΥΜΟ", "ΑΓΝΩΣΤΟ ΟΝΟΜΑ");
                    }


                    if ($client['email'] != null) {
                        $vcard->addEmail($client['email']);
                    }

                    //$vcard->addRole('Διαχειριστής');
                    if ($client['telephone'] != null) {
                        $vcard->addPhoneNumber($client['telephone'], 'HOME');
                    }

                    if ($client['telephone2'] != null) {
                        $vcard->addPhoneNumber($client['telephone2'], 'WORK');
                    }

                    if ($client['mobile'] != null) {
                        $vcard->addPhoneNumber($client['mobile'], 'CELL');
                    }
                    //$vcard->addRole('Πελάτης');
                    // $vcard->addPhoneNumber(1234121212, 'PREF;WORK');
                    // $vcard->addPhoneNumber(123456789, 'WORK');
                    //$vcard->addAddress(null, null, $client->address, $client->location, null, $client->zipcode, 'Ελλάδα');
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
            } else {
                return response()->json(["message" => "Δεν υπάρχουν τηλέφωνα για τη δημιουργία vcf αρχειου"], 422);
            }
        }

        if ($request->task == "users") {
            $users = User::where('telephone', '!=', null)->orWhere('telephone2', '!=', null)->orWhere('mobile', '!=', null)->get();
            if (count($users) > 0) {
                foreach ($users as $user) {
                    $vcard = new VCard();

                    if ($user['firstname'] != null || $user['lastname'] != null) {
                        $vcard->addName($user['lastname'], $user['firstname']);
                    } elseif ($user['firstname'] != null || $user['lastname'] == null) {
                        $vcard->addName("ΑΓΝΩΣΤΟ ΕΠΩΝΥΜΟ", $user['firstname']);
                    } elseif ($user['firstname'] == null || $user['lastname'] != null) {
                        $vcard->addName($user['lastname'], "ΑΓΝΩΣΤΟ ΟΝΟΜΑ");
                    } else {
                        $vcard->addName("ΑΓΝΩΣΤΟ ΕΠΩΝΥΜΟ", "ΑΓΝΩΣΤΟ ΟΝΟΜΑ");
                    }


                    if ($user['email'] != null) {
                        $vcard->addEmail($user['email']);
                    }

                    //$vcard->addRole('Διαχειριστής');
                    if ($user['telephone'] != null) {
                        $vcard->addPhoneNumber($user['telephone'], 'HOME');
                    }

                    if ($user['telephone2'] != null) {
                        $vcard->addPhoneNumber($user['telephone2'], 'WORK');
                    }

                    if ($user['mobile'] != null) {
                        $vcard->addPhoneNumber($user['mobile'], 'CELL');
                    }
                    // $vcard->addRole('ATL/' . $user->role()->first()->title);
                    // $vcard->addPhoneNumber($user->telephone, 'TELEPHONE');
                    // $vcard->addPhoneNumber($user->telephone2, 'TELEPHONE2');
                    // $vcard->addPhoneNumber($user->mobile, 'MOBILE');
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
            } else {
                return response()->json(["message" => "Υπάρχουν επαφές για τη δημιουργία vcf"], 422);
            }
        }
    }
}
