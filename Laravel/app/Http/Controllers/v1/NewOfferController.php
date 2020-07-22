<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Offer;
use App\Bullet;
use App\BulletOffer;
use App\OfferText;
use App\Client;
use App\Http\Resources\OfferResource;
use Validator;
use Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use ConvertApi\ConvertApi;
use App\Http\CustomClasses\v1\Greeklish;
use App\Project;
use App\DamageType;

use Elibyy\TCPDF\Facades\TCPDF;
use Barryvdh\DomPDF\Facade as PDF;

class NewOfferController extends Controller
{
    public function index(Request $request)
    {
        return OfferResource::collection(Offer::all());
    }

    public function store(Request $request)
    {
        $descriptions = array();
        $amount = 0;
        //Checks
        if (!$request->client_id) {
            return response()->json(["message" => "Πρέπει να επιλέξετε πελάτη για να στείλετε την προσφορά"], 422);
        }

        $client = Client::where('id', $request->client_id)->first();
        if (!$client) {
            return response()->json(["message" => "Δεν υπάρχει ο πελατης καταχωρημένος στο σύστημα"], 404);
        }

        if (!$client['firstname'] || !$client['lastname'] || !$client['address'] || !$client['location']) {
            return response()->json(["message" => "Για να σταλεί προσφορά πρέπει να υπάρχουν το ον/μο πελάτη, διεύθυνση και περιοχή"], 422);
        }

        if (!$client['telephone1'] && !$client['telephone2'] && !$client['mobile']) {
            return response()->json(["message" => "Θα πρέπει να υπάρχει τουλάχιστον ένα νουμερο για τον πελάτη"], 422);
        }

        if ($client['telephone1']) {
            $phone = $client['telephone1'];
        } elseif ($client['telephone2']) {
            $phone = $client['telephone2'];
        } else {
            $phone = $client['mobile'];
        }

        // if (!$client['email']) {
        //     return response()->json(["message" => "Δεν μπορεί να σταλεί προσφορά σε πελάτη που δεν εχει καταχωρημένη διεύθυνση email"], 422);
        // }

        if (count($request->bullets) == 0) {
            return response()->json(["message" => "Η προσφορά δεν μπορεί να είναι κενή"], 422);
        }

        foreach ($request->bullets as $bullet) {
            $bullett = Bullet::where('id', $bullet['bullet_id'])->first();
            if (!$bullett) {
                return response()->json(["message" => "Παρακαλώ ελέγξτε πάλι τις εγγραφές σας για την προσφορά"], 422);
            }
            array_push($descriptions, $bullett['description']);
            $amount += $bullett['price'] * $bullet['quantity'];
        }

        if (!$request->title) {
            return response()->json(["message" => "Ο τίτλος προσφοράς είναι υποχρεωτικός"], 422);
        }

        $text = OfferText::where('id', $request->offer_text_id)->first();
        if (!$text) {
            return response()->json(["message" => "Παρακαλώ επιλέξτε κείμενο προσφοράς"], 422);
        }


        $bullet_offers = '';
        $i = 0;
        foreach ($request->bullets as $bullet) {
            if ($bullet['quantity'] <= 1) {
                $bullet_offers .= '<li>' . $descriptions[$i] . '</li>';
            } else {
                $bullet_offers .= '<li>' . $descriptions[$i] . ' x ' . $bullet['quantity'] . '</li>';
            }
            $i++;
        }
        $offer = '<br><b><u>ΟΙΚΟΝΟΜΙΚΗ ΠΡΟΣΦΟΡΑ</u></b>
            <ul>' . $bullet_offers . '</ul>
            <br/>
            Σύνολο Τιμής: &euro;' . $amount . '<br/>';

        $offer_count = Offer::where('created_at', 'like', '%' . date('Y') . '%')->count();

        $html = '
        <html>
            <head>
                <style type="text/css">
                body {
                    font-family: "dejavu sans";
                    font-size: 11px;
                 }
                @page {
                    margin-top: 140px;
                    margin-bottom: 140px;
                    margin-left: 1.5cm;
                    margin-right: 1.5cm;
                  }
                header { position: fixed; top: -140px; left: 0px; right: 0px; height: 50px;}
                footer { position: fixed; bottom: -90px; left: 0px; right: 0px; height: 1.5cm; }
                </style>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            </head>
            <body>
            <header>
                <img src="' . public_path() . '/offer_header.jpg" width="100%" />
            </header>
            <footer>
                <p style="text-align: center;margin-top:50px;font-size:8px;">Πλατεία Αγίου Ευσταθίου 9, 14233 Νέα Ιωνία, Αθήνα, Τηλ./fax:211 411 40 30<br/>
                web site: www.atlenergy.gr e-mail:sales@atlenergy.gr</p>
            </footer>
            <main>
                <div style="text-align: right;"> Α.Φ.Μ.: 106764905, Δ.Ο.Υ.: Νέας Ιωνίας</div>
                <hr>
                <div style="text-align: right;">
                <span><b>' . $client['lastname'] . ' ' . $client['firstname'] . '</b></span>
                <br>
                <span><b>' . $client['address'] . ', ' . $client['location'] . '.</b></span>
                <br>
                <span> <b>' . $phone . '</b></span>
                </div>
                <br>
                <table>
                <tr>
                <td width="120">ΘΕΜΑ:</td>
                <td width="100%" style="padding: 0;margin: 0;"><b>' . $request->title . '</b></td>
                </tr>
                <tr>
                <td width="120">Αριθμός Προσφοράς:</td>
                <td style="padding: 0;margin: 0;">' . ($offer_count + 1) . '</td>
                </tr>
                <tr><td width="120">Υπεύθυνος έργου:</td>
                <td style="padding: 0;margin: 0;">ΑΘΑΝΑΣΟΠΟΥΛΟΣ ΠΕΡΙΚΛΗΣ</td>
                </tr>
                <tr><td width="120">Ημερομηνία:</td>
                <td style="padding: 0;margin: 0;">' . date('d/m/Y') . '</td>
                </tr>
                </table>
            ' . $text['upper_text'] . $offer . $text['lower_text'] .
            '<main>' .
            '</body>
        </html>';
        // ---------------------------------------------------------
        $offer_filename = Greeklish::remove_accent($client['lastname']) . '_' . Greeklish::remove_accent($client['firstname']) . '-' . 'prosfora_' . ($offer_count + 1) . '-' . date('Y-m-d') . '.pdf';
        //Close and output PDF document

        $offer_file = storage_path() . '/Clients/' . $client['foldername'] . '/' . $offer_filename;

        PDF::loadHTML($html)->setPaper('a4', 'portrait')->setWarnings(false)->save($offer_file);

        $db_type = DamageType::where('name', $request->title)->first();
        if ($db_type) {
            $title_id = $db_type['id'];
        } else {
            $new_title = DamageType::create(["name" => $request->title]);
            $title_id = $new_title->id;
        }

        $offer = Offer::create(['title_id' => $title_id, 'client_id' => $request->client_id, "offer_number" => $offer_count + 1, "offer_filename" => $offer_filename, "amount" => $amount, "offer_text_id" => $request->offer_text_id]);

        foreach ($request->bullets as $bul) {
            BulletOffer::create(['bullet_id' => $bul['bullet_id'], 'offer_id' => $offer->id, 'quantity' => $bul['quantity']]);
        }
        //pending mail

        if ($client['email'] != null) {
            $email = new PHPMailer();
            $email->CharSet = "UTF-8";
            $email->SetFrom('support@atlenergy.gr', 'ATLEnergy'); //Name is optional
            $email->Subject   = 'ATL energy - Προσφορά ' . ($offer_count + 1);
            $email->AddEmbeddedImage(public_path("imagesatlenergy_maillogo.jpg"), 'logoimg', 'imagesatlenergy_maillogo.jpg');
            $email->Body      = '<p>Συνημμένη θα βρείτε την προσφορά μας.
            </p><br><hr><br>
            Με εκτίμηση<br>
            Για την A.T.L. Energy<br>
            Περικλής Π. Αθανασόπουλος<br>
            Πτ. Μηχανολόγος Μηχανικός Τ.Ε.<br>' . '<img src="cid:logoimg">' . '<br>'
                . "<p>Kεντρικό: Κατάστημα Ν. Ελλάδος:<br>
            Πλατεία Αγ. Ευσταθίου 9 Ν. Ιωνία Τ.Κ. 14233<br>
            Τηλ.-Φαξ: +30 211 411 4030 ,Κιν.:+30 6938340219<br>
            e-mail: pathanasopoulos@atlenergy.gr<br>
            <a href='www.atlenergy.gr'>www.atlenergy.gr</a></p>";
            $email->isHTML(true);
            $email->AddAddress($client['email']);

            $email->AddAttachment($offer_file, $offer_filename);

            $email->Send();

            return response()->json(['message' => 'Η προσφορά δημιουργήθηκε και εστάλη επιτυχώς'], 200);
        } else {
            return response()->json(['message' => 'Η προσφορά δημιουργήθηκε επιτυχώς'], 200);
        }
    }

    public function file(Request $request, $offer)
    {
        $selected_offer = Offer::where('id', $offer)->first();
        if (!$selected_offer) {
            return response()->json(["message" => "Δεν βρέθηκε η προσφορά"], 404);
        }

        $client = Client::where('id', $selected_offer['client_id'])->first();

        $file = storage_path() . "/Clients/" . $client['foldername'] . "/" . $selected_offer['offer_filename'];

        if (!file_exists($file)) {
            return response()->json(["message" => "Δεν υπάρχει το αρχείο που αναζητείτε!"], 404);
        }

        $fileExtension = explode('.', $selected_offer['offer_filename']);
        $n = count($fileExtension);
        $extension = $fileExtension[$n - 1];

        if ($extension == "pdf") {
            $headers = array(
                'Content-Type: application/pdf',
            );
        }

        $filename = $selected_offer['offer_filename'];
        // if ($extension == "jpeg") {
        //     $headers = array(
        //         'Content-Type: image/jpeg',
        //     );
        // }

        // if ($extension == "jpg") {
        //     $headers = array(
        //         'Content-Type: image/jpg',
        //     );
        // }

        // if ($extension == "png") {
        //     $headers = array(
        //         'Content-Type: image/png',
        //     );
        // }

        return Response::download($file, $filename, $headers);
    }

    public function repo()
    {
        /*
        //TCPDF init
        TCPDF::SetHeaderData(public_path('offer_header.jpg'));
        TCPDF::SetAuthor('Nicola Asuni');
        TCPDF::SetTitle('TCPDF Example 003');
        TCPDF::SetSubject('TCPDF Tutorial');
        TCPDF::SetKeywords('TCPDF, PDF, example, test, guide');

        TCPDF::setHeaderCallback(function ($pdf) {
            $pdf->writeHTML('<img src="' . public_path('offer_header.jpg') . '"style="">');
            $pdf->SetTopMargin(40);
        });
        TCPDF::setFooterCallback(function ($pdf) {
            $pdf->SetFont('freeserif', '', 8);
            $pdf->writeHTML('<p style="text-align: center;">Πλατεία Αγίου Ευσταθίου 9, 14233 Νέα Ιωνία, Αθήνα, Τηλ./fax:211 411 40 30<br/>
            web site: www.atlenergy.gr e-mail:sales@atlenergy.gr</p>');
            $pdf->SetTopMargin(35);
        });

        $arial = TCPDF::addFont(public_path('fonts\Arialn.ttf'), 'TrueTypeUnicode', '', 32);

        // set default monospaced font
        TCPDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        TCPDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, PDF_MARGIN_BOTTOM);
        //TCPDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        TCPDF::SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        TCPDF::SetAutoPageBreak(true, 35);

        // set image scale factor
        TCPDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            TCPDF::setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        TCPDF::SetFont('Arialn', '', 10);

        // // add a page
        TCPDF::AddPage();
        */
        //TCPDF::writeHTML($html, true, false, true, false, '');
        //TCPDF::Output(public_path($offer_filename), 'F');
    }

    public function convertToProject(Request $request, $offerId)
    {
        //return response()->json(["message" => "It works"]);
        $offer = Offer::where('id', $offerId)->first();
        //fetching important properties for creating a project
        $client_id = $offer['client_id'];
        $amount = $offer['amount'];
        //end
        if (!$offer) {
            return response()->json(["message" => "Δεν βρέθηκε η προσφορά"]);
        }
        $offer_bullets = BulletOffer::where('offer_id', $offerId)->get();
        $marks_array = array();
        foreach ($offer_bullets as $offer_bullet) {
            $bullet = Bullet::where('id', $offer_bullet['bullet_id'])->where('mark_id', '!=', null)->first();
            if ($bullet) {
                if ($offer_bullet['quantity'] > 1) {
                    for ($i = 0; $i < $offer_bullet['quantity']; $i++) {
                        array_push($marks_array, $bullet['mark_id']);
                    }
                } else {
                    array_push($marks_array, $bullet['mark_id']);
                }
            }
        }
        Project::create([
            "client_id" => $client_id,
            "status" => "Μη Ολοκληρωμένo",
            "title_id" => $offer['title_id'],
            "cost" => $amount,
            "marks" => implode(',', $marks_array)
        ]);
        return response()->json(["message" => "Η προσφορά μετατράπηκε σε έργο επιτυχώς!"], 200);
    }
}
