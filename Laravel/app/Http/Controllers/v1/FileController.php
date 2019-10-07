<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Fpdf;
use App\Http\CustomClasses\v1\Resizer;
use App\Client;
use Response;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$id)
    {
        //Check if logged in user is authorized
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 4)
        {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση σ αυτά τα στοιχεία"],401);
        }

        //check if client exists
        $client = Client::where('id',$id)->first();
        if(!$client)
        {
            return response()->json(["message" => "Δεν βρέθηκε ο χρήστης"],404);
        }

        //Url for stored files
        $mypath = storage_path().'/Clients/'.$id;

        //get images and store them as ready blobs
        $imagefiles = array();

        $countJPG = count(glob($mypath."/*.jpg"));
        $countPDF = count(glob($mypath."/*.pdf"));

        if($countJPG == 0 || $countPDF == 0 || $countJPG != $countPDF)
        {
            return response()->json(["message" => "Δεν βρέθηκαν αρχεία για τον πελάτη!"],404);
        }

        foreach (glob($mypath."/*.jpg") as $file)
        {
            $contents = file_get_contents($file);

            array_push($imagefiles,"data:image/jpeg;base64,".base64_encode($contents));
        }

        $pdfFiles = array();
        $filenames = array();
        foreach (glob($mypath."/*.pdf") as $file)
        {
            $filePath = explode("/",$file);
            $filename = (count($filePath) - 1);
            $route = url("/api/files/".$id."/".$filePath[$filename]);
            array_push($pdfFiles,$route);
            array_push($filenames,$filePath[$filename]);
        }

        $responseArray = array();
        for($i = 0; $i < count($pdfFiles); $i++)
        {
            $response = new \stdClass();
            $response->thumbnail = $imagefiles[$i];
            $response->url = $pdfFiles[$i];
            $response->name = $filenames[$i];

            array_push($responseArray,$response);
        }

        return response()->json(["data" => $responseArray]);


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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        ini_set('memory_limit','256M');

        $count = 1;
        foreach($request->all() as $file)
        {
            $filename = $file['filename'];
            $filee = $file['file'];

            if(!$filename || !$filee)
            {
                return response()->json(["To αρχείο στην θέση ".$count." είναι ελαττωματικό. Η διαδικασία ακυρώνεται για τα υπόλοιπα αρχεία!"],422);
            }

            $data = explode( ',', $filee );
            $toStorage = storage_path();
            $destinationPath = $toStorage."/Clients/".$id."/";
            $image_path = $destinationPath.$filename.".bmp";
            file_put_contents($image_path, base64_decode($data[1]));
            //return mime_content_type($image_path);
            $im = imagecreatefrombmp($image_path);

            // Convert it to a PNG file with default settings
            imagejpeg($im, $destinationPath.$filename.'.jpg');
            //return "ok";
            $jpegPath = $destinationPath.$filename.'.jpg';
            //$image = 'webcam-toy-photo3.jpg';
            $pdfName = $filename.".pdf";
            //$pdf = new Fpdf();
            Fpdf::addPage();
            Fpdf::Image($jpegPath,20,40,170);
            Fpdf::Output('F',$destinationPath.$pdfName);

            unlink($image_path);//delete the bmp file

            $resizedImage = Resizer::resize_image($jpegPath,200,200);
            unlink($jpegPath);
            imagejpeg($resizedImage,$destinationPath.$filename.".jpg");

            ++$count;
        }
        return response()->json(["message" => "Τα αρχεία ανέβηκαν με επιτυχία!"],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id, $filename)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 4)
        {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση σ αυτά τα στοιχεία"],401);
        }

        $client = Client::where('id',$id)->first();
        if(!$client)
        {
            return response()->json(["message" => "Δεν βρέθηκε ο χρήστης"],404);
        }

        $file= storage_path()."/Clients/".$id."/".$filename;

        if(!file_exists($file))
        {
            return response()->json(["message" => " Δεν υπάρχει το αρχείο που αναζητείτε!"],404);
        }

        $headers = array(
                  'Content-Type: application/pdf',
                );

        return Response::download($file, $filename, $headers);
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id,$filename)
    {
        //Check if logged in user is authorized
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 4)
        {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση σ αυτά τα στοιχεία"],401);
        }

        //check if client exists
        $client = Client::where('id',$id)->first();
        if(!$client)
        {
            return response()->json(["message" => "Δεν βρέθηκε ο χρήστης"],404);
        }

        //Url for stored files
        $searchPath = explode('.',$filename);
        $mypath = storage_path().'/Clients/'.$id."/".$searchPath[0];

        $files = glob($mypath."*");

        if(count($files) == 0 && count($files)%2 == 0)
        {
            return response()->json(["message" => "Το συγκεκριμένο αρχείο δεν ύπαρχει στο σύστημα"],404);
        }

        foreach($files as $file)
        {
            unlink($file);
        }
        return response()->json(["message" => "Το αρχείο διαγράφτηκε επιτυχώς!"],200);
    }
}
