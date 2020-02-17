<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Offer;
use App\Bullet;
use App\Client;
use App\Http\Resources\OfferResource;
use Validator;
use Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
    public function store(Request $request)
    {
        $client = Client::where('id', $request->client_id)->first();
        if (!$client) {
            return response()->json(["message" => "Δεν υπάρχει ο πελατης καταχωρημένος στο σύστημα"], 404);
        }

        if (!$client['email']) {
            return response()->json(["message" => "Δεν μπορεί να σταλεί προσφορά σε πελάτη που δεν εχει καταχωρημένη διεύθυνση email"], 422);
        }

        foreach ($request->bullets as $bullet_id) {
            $bullet = Bullet::where('id', $bullet_id)->first();
            if (!$bullet) {
                return response()->json(["message" => "Παρακαλώ ελέγξτε πάλι τις εγγραφές σας για την προσφορά"], 422);
            }
        }

        $offers = Offer::where('created_at', 'like', '%' . date('Y') . '%')->count();
        $offer = Offer::create(['client_id' => $request->client_id, "status_id" => 1, "number" => $offers + 1]);

        //pending mail and pdf generation
        return response()->json(['message' => 'Η προσφορά δημιουργήθηκε και εστάλη επιτυχώς'], 200);
    }

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
