<?php

namespace App\Http\Controllers\v1;

require '../vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Damage;
use App\Http\CustomClasses\v1\TechMail;
use PDF;
use ConvertApi\ConvertApi;
use Elibyy\TCPDF\Facades\TCPDF;
use App\OfferText;
use App\Client;
use App\Bullet;
use App\Offer;
use App\BulletOffer;
use App\Http\CustomClasses\v1\Greeklish;


class TestController extends Controller
{

    public function test(Request $request)
    {
        $descriptions = array();
        $amount = 0;
        //Checks
        if (!$request->client_id) {
            return response()->json(["message" => "Πρέπει να επιλέξετε πελάτη για να στείλετε την προσφορά"], 422);
        }
        //mandatory -> client_id & bullets[] array
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

        if (!$client['email']) {
            return response()->json(["message" => "Δεν μπορεί να σταλεί προσφορά σε πελάτη που δεν εχει καταχωρημένη διεύθυνση email"], 422);
        }

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
        TCPDF::SetFont('freeserif', '', 9.5);


        // // add a page
        TCPDF::AddPage();
        $bullet_offers = '';

        $i = 0;
        foreach ($request->bullets as $bullet) {
            if ($bullet['quantity'] <= 1) {
                $bullet_offers .= '<li>' . $description[$i] . '</li>';
            } else {
                $bullet_offers .= '<li>' . $descriptions[$i] . ' x ' . $bullet['quantity'] . '</li>';
            }
            $i++;
        }
        $offer = '<b><u>ΟΙΚΟΝΟΜΙΚΗ ΠΡΟΣΦΟΡΑ</u></b>
            <ul>' . $bullet_offers . '</ul>
            <br/>
            Σύνολο Τιμής: &euro;' . $amount . '<br/>';


        $offer_count = Offer::where('created_at', 'like', '%' . date('Y') . '%')->count();
        // // set some text to print
        //my html text
        //$text = OfferText::where('id', 3)->first();
        $html = '
            <span style="text-align: right;"> Α.Φ.Μ.: 106764905, Δ.Ο.Υ.: Νέας Ιωνίας</span>
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
            <td style="padding: 0;margin: 0;">' . $offer_count . '</td>
            </tr>
            <tr><td width="120">Υπεύθυνος έργου:</td>
            <td style="padding: 0;margin: 0;">ΑΘΑΝΑΣΟΠΟΥΛΟΣ ΠΕΡΙΚΛΗΣ</td>
            </tr>
            <tr><td width="120">Ημερομηνία:</td>
            <td style="padding: 0;margin: 0;">' . date('d/m/Y') . '</td>
            </tr>
            </table>
            ' . $text['upper_text'] . $offer . $text['lower_text'];
        //$html .= $request->test;
        //end
        // print a block of text using Write()
        TCPDF::writeHTML($html, true, false, true, false, '');

        // ---------------------------------------------------------
        $offer_filename = Greeklish::remove_accent($client['lastname']) . '_' . Greeklish::remove_accent($client['firstname']) . '-' . 'prosfora_' . ($offer_count + 1) . '-' . date('Y-m-d') . '.pdf';
        //Close and output PDF document
        //TCPDF::Output(public_path($offer_filename), 'F');
        $offer_file = storage_path() . '/Clients/' . $client['foldername'] . '/' . $offer_filename;
        TCPDF::Output($offer_file, 'F');

        $offer = Offer::create(['client_id' => $request->client_id, "offer_number" => $offer_count + 1, "offer_filename" => $offer_filename, "amount" => $amount]);

        foreach ($request->bullets as $bul) {
            BulletOffer::create(['bullet_id' => $bul['bullet_id'], 'offer_id' => $offer_count + 1, 'quantity' => $bul['quantity']]);
        }
        //pending mail

        if ($client['email']) {
            $email = new PHPMailer();
            $email->CharSet = "UTF-8";
            $email->SetFrom('support@atlenergy.gr', 'ATLEnergy'); //Name is optional
            $email->Subject   = 'ATL energy - Προσφορά ' . ($offer_count + 1);
            $email->Body      = '<p>Συνημμένη θα βρείτε την προσφορά μας.
            </p><br><hr><br>
            Με εκτίμηση<br>
            Για την A.T.L. Energy<br>
            Περικλής Π. Αθανασόπουλος<br>
            Πτ. Μηχανολόγος Μηχανικός Τ.Ε.<br>' . '<img src="' . public_path("imagesatlenergy_maillogo.jpg") . '">' . '<br>'
                . "<p>Kεντρικό: Κατάστημα Ν. Ελλάδος:<br>
            Πλατεία Αγ. Ευσταθίου 9 Ν. Ιωνία Τ.Κ. 14233<br>
            Τηλ.-Φαξ: +30 211 411 4030 ,Κιν.:+30 6938340219<br>
            e-mail: pathanasopoulos@atlenergy.gr<br>
            <a href='www.atlenergy,gr'>www.atlenergy.gr</a></p>";
            $email->isHTML(true);
            $email->AddAddress($client['email']);

            $email->AddAttachment($offer_file, $offer_filename);

            $email->Send();
        }


        //
        return response()->json(['message' => 'Η προσφορά δημιουργήθηκε και εστάλη επιτυχώς'], 200);
        return 'Ok';
    }
}
