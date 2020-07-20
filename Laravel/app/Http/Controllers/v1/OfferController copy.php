<?php

namespace App\Http\Controllers\v1;

require '../vendor/autoload.php';

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

use Elibyy\TCPDF\Facades\TCPDF;
use Barryvdh\DomPDF\Facade as PDF;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return OfferResource::collection(Offer::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
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
        $offer = '<b><u>ΟΙΚΟΝΟΜΙΚΗ ΠΡΟΣΦΟΡΑ</u></b>
            <ul>' . $bullet_offers . '</ul>
            <br/>
            Σύνολο Τιμής: &euro;' . $amount . '<br/>';


        $offer_count = Offer::where('created_at', 'like', '%' . date('Y') . '%')->count();
        // // set some text to print
        //my html text
        //$text = OfferText::where('id', 3)->first();
        $html = '<html>
        <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>
        <body>
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
            ' . $text['upper_text'] . $offer . $text['lower_text'] . '</body></html>';
        //$html .= $request->test;
        //end
        // print a block of text using Write()
        $html = str_replace('<h1>', '<h1 style="text-align: center;">', $html);

        //TCPDF::writeHTML($html, true, false, true, false, '');

        // ---------------------------------------------------------
        $offer_filename = Greeklish::remove_accent($client['lastname']) . '_' . Greeklish::remove_accent($client['firstname']) . '-' . 'prosfora_' . ($offer_count + 1) . '-' . date('Y-m-d') . '.pdf';
        //Close and output PDF document
        //TCPDF::Output(public_path($offer_filename), 'F');
        $offer_file = storage_path() . '/Clients/' . $client['foldername'] . '/' . $offer_filename;
        //TCPDF::Output($offer_file, 'F');

        PDF::loadHTML($html)->setPaper('a4', 'portrait')->setWarnings(false)->save($offer_file);

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

        return response()->json(['message' => 'Η προσφορά δημιουργήθηκε και εστάλη επιτυχώς'], 200);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function oldStore(Request $request)
    // {
    //     if (!$request->client_id) {
    //         return response()->json(["message" => "Πρέπει να επιλέξετε πελάτη για να στείλετε την προσφορά"], 422);
    //     }
    //     //mandatory -> client_id & bullets[] array
    //     $client = Client::where('id', $request->client_id)->first();
    //     if (!$client) {
    //         return response()->json(["message" => "Δεν υπάρχει ο πελατης καταχωρημένος στο σύστημα"], 404);
    //     }

    //     if (!$client['firstname'] || !$client['lastname'] || !$client['address'] || !$client['location']) {
    //         return response()->json(["message" => "Για να σταλεί προσφορά πρέπει να υπάρχουν το ον/μο πελάτη, διεύθυνση και περιοχή"], 422);
    //     }

    //     if (!$client['telephone1'] && !$client['telephone2'] && !$client['mobile']) {
    //         return response()->json(["message" => "Θα πρέπει να υπάρχει τουλάχιστον ένα νουμερο για τον πελάτη"], 422);
    //     }

    //     if ($client['telephone1']) {
    //         $phone = $client['telephone1'];
    //     } elseif ($client['telephone2']) {
    //         $phone = $client['telephone2'];
    //     } else {
    //         $phone = $client['mobile'];
    //     }

    //     if (!$client['email']) {
    //         return response()->json(["message" => "Δεν μπορεί να σταλεί προσφορά σε πελάτη που δεν εχει καταχωρημένη διεύθυνση email"], 422);
    //     }

    //     if (count($request->bullets) == 0) {
    //         return response()->json(["message" => "Η προσφορά δεν μπορεί να είναι κενή"], 422);
    //     }

    //     foreach ($request->bullets as $bullet_id) {
    //         $bullet = Bullet::where('id', $bullet_id)->first();
    //         if (!$bullet) {
    //             return response()->json(["message" => "Παρακαλώ ελέγξτε πάλι τις εγγραφές σας για την προσφορά"], 422);
    //         }
    //     }
    //     //create offer pdf start
    //     $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path() . '/test.docx');

    //     $offers = Offer::where('created_at', 'like', '%' . date('Y') . '%')->count();

    //     $bullet_id_array = array();
    //     $bullet_id_array = $request->bullets;

    //     $templateProcessor->setValue('client_name', $client['lastname'] . " " . $client['firstname']);
    //     $templateProcessor->setValue('client_address', $client['address']);
    //     $templateProcessor->setValue('client_location', $client['location']);
    //     $templateProcessor->setValue('client_phone', $phone);
    //     $templateProcessor->setValue('offer_number', $offers + 1);
    //     $templateProcessor->setValue('date', date('d / m / Y'));
    //     $amount = 0.00;
    //     foreach ($request->bullets as $bullet_id) {
    //         $bullet = Bullet::where('id', $bullet_id)->first();
    //         $amount += $bullet['price'];
    //     }

    //     $templateProcessor->setValue('total', $amount);

    //     $templateProcessor->cloneRow('value', count($request->bullets));
    //     for ($i = 1; $i <= count($request->bullets); $i++) {
    //         $templateProcessor->setValue('value#' . $i, Bullet::where('id', $bullet_id_array[$i - 1])->first()['description']);
    //     }

    //     $templateProcessor->saveAs(public_path() . '/xx.docx');
    //     $current_offer = $offers + 1;

    //     $offer_filename = Greeklish::remove_accent($client['lastname']) . '_' . Greeklish::remove_accent($client['firstname']) . '-' . 'prosfora_' . $current_offer . '-' . date('Y-m-d') . '.pdf';

    //     ConvertApi::setApiSecret('cqbWK6STXKAFVUVD');

    //     $result = ConvertApi::convert('pdf', ['File' => public_path() . '/xx.docx']);
    //     # save to file
    //     $result->getFile()->save(public_path() . '/' . $offer_filename);
    //     //end pdf

    //     copy(public_path() . '/' . $offer_filename, storage_path("Clients/" . $client->foldername . "/" . $offer_filename);

    //     $image = public_path() . "/imagesatlenergy_maillogo.jpg";
    //     // Read image path, convert to base64 encoding
    //     $imageData = base64_encode(file_get_contents($image));

    //     // Format the image SRC:  data:{mime};base64,{data};
    //     $src = 'data: ' . mime_content_type($image) . ';base64,' . $imageData;

    //     /*
    //     $email = new PHPMailer();
    //     $email->CharSet = "UTF-8";
    //     $email->SetFrom('support@atlenergy.gr', 'ATLEnergy'); //Name is optional
    //     $email->Subject   = 'ATL energy - Προσφορά ' . $current_offer;
    //     $email->Body      = '<p>Συνημμένη θα βρείτε την προσφορά μας.
    //     </p><br><hr><br>
    //     Με εκτίμηση<br>
    //     Για την A.T.L. Energy<br>
    //     Περικλής Π. Αθανασόπουλος<br>
    //     Πτ. Μηχανολόγος Μηχανικός Τ.Ε.<br>' . '<img src="' . $src . '">' . '<br>'
    //         . "<p>Kεντρικό: Κατάστημα Ν. Ελλάδος:<br>
    //     Πλατεία Αγ. Ευσταθίου 9 Ν. Ιωνία Τ.Κ. 14233<br>
    //     Τηλ.-Φαξ: +30 211 411 4030 ,Κιν.:+30 6938340219<br>
    //     e-mail: pathanasopoulos@atlenergy.gr<br>
    //     <a href='www.atlenergy,gr'>www.atlenergy.gr</a></p>";
    //     $email->isHTML(true);
    //     $email->AddAddress($request->email);

    //     $file_to_attach = public_path() . '/' . $offer_filename;

    //     $email->AddAttachment($file_to_attach, $offer_filename);

    //     $email->Send();
    //     */

    //     unlink(public_path() . '/xx.docx');
    //     unlink(public_path() . '/' . $offer_filename);




    //     $offer = Offer::create(['client_id' => $request->client_id, "offer_number" => $offers + 1, "offer_filename" => $offer_filename, "amount" => $amount]);

    //     foreach ($request->bullets as $bullet_id) {
    //         BulletOffer::create(['bullet_id' => $bullet_id, 'offer_id' => $offer->id]);
    //     }
    //     //pending mail and pdf generation
    //     return response()->json(['message' => 'Η προσφορά δημιουργήθηκε και εστάλη επιτυχώς'], 200);
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $offer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //2020-02-12 Se periptwsh p mas zitithei na vlepoun katastash einai etoimo
    // public function update(Request $request, $offer, $status)
    // {
    //     $selected_offer = Offer::where('id', $offer)->where('status_id', 1)->first();
    //     if (!$selected_offer) {
    //         return response()->json(["message" => "Δεν βρέθηκε προσφορά με τον κωδικό αυτό που να βρίσκεται σε κατάσταση Εκκρεμότητας"], 404);
    //     }

    //     if ($status === 'accepted') {
    //         $selected_offer->update(['status_id' => 3]);
    //         //δημιουργια Εργου
    //         return response()->json(["message" => "Η προσφορά έγινε Δεκτή!"], 200);
    //     } elseif ($status === 'rejected') {
    //         $selected_offer->update(['status_id' => 2]);
    //         return response()->json(["message" => "Η προσφορά Απορρίπτηκε!"], 200);
    //     } else {
    //         return response()->json(["Η κατάσταση προσφοράς δεν είναι έγκυρη"], 422);
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
