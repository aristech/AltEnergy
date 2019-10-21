<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Fpdf;
use App\Http\CustomClasses\v1\Resizer;
use App\Client;
use Response;
use Validator;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$id)
    {
        ini_set('memory_limit','256M');

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
        $mypath = storage_path().'/Clients/'.$client->foldername;

        //get images and store them as ready blobs
        $responseArray = array();

        $files = glob($mypath."/*");
        // $countJPG = count(glob($mypath."/*.jpg"));
        // $countPDF = count(glob($mypath."/*.pdf"));

        foreach($files as $file)
        {
            $class = new \stdClass();
            $info = pathinfo($file);//extension checked for jpeg and jpg differences
			$type = mime_content_type($file);

            if($info['extension'] == "jpeg")
            {
                $filename = explode('/',$file);
                $n = count($filename);
                $name = $filename[$n-1];

                $contents = file_get_contents($file);
                $contents = "data:image/jpeg;base64,".base64_encode($contents);
                $class->file = $contents;
                $class->type = "jpeg";
                $class->filename = $name;
            }

            if($info['extension'] == "jpg")
            {
                $filename = explode('/',$file);
                $n = count($filename);
                $name = $filename[$n-1];

                $contents = file_get_contents($file);
                $contents = "data:image/jpg;base64,".base64_encode($contents);
                $class->file = $contents;
                $class->type = "jpg";
                $class->filename = $name;
            }

            if($type == "application/pdf")
            {
                $filename = explode('/',$file);
                $n = count($filename);
                $name = $filename[$n-1];

                $contents = url("/api/files/v1/".$id."/".$name);
                $class->file = $contents;
                $class->type = "pdf";
                $class->filename = $name;
            }

            if($type == "image/png")
            {
                $filename = explode('/',$file);
                $n = count($filename);
                $name = $filename[$n-1];

				$file =

                $contents = file_get_contents($file);
                $contents = "data:image/png;base64,".base64_encode($contents);
                $class->file = $contents;
                $class->type = "png";
                $class->filename = $name;
            }

            array_push($responseArray,$class);
        }




        // if($countJPG == 0 || $countPDF == 0 || $countJPG != $countPDF)
        // {
        //     return response()->json(["message" => "Δεν βρέθηκαν αρχεία για τον πελάτη!"],404);
        // }

        // foreach (glob($mypath."/*.jpg") as $file)
        // {
        //     $contents = file_get_contents($file);

        //     array_push($imagefiles,"data:image/jpeg;base64,".base64_encode($contents));
        // }

        // $pdfFiles = array();
        // $filenames = array();
        // foreach (glob($mypath."/*.pdf") as $file)
        // {
        //     $filePath = explode("/",$file);
        //     $filename = (count($filePath) - 1);
        //     $route = url("/api/files/".$id."/".$filePath[$filename]);
        //     array_push($pdfFiles,$route);
        //     array_push($filenames,$filePath[$filename]);
        // }

        // $responseArray = array();
        // for($i = 0; $i < count($pdfFiles); $i++)
        // {
        //     $response = new \stdClass();
        //     $response->thumbnail = $imagefiles[$i];
        //     $response->url = $pdfFiles[$i];
        //     $response->name = $filenames[$i];

        //     array_push($responseArray,$response);
        // }

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

        $client = Client::find($id);
        if(!$client)
        {
            return response()->json(["message" => "Δεν βρέθηκε ο χρήστης με κωδικό ".$id],404);
        }

        $count = 1;
        if(empty($request->all()))
        {
            return response()->json(["message" => "Δεν υπάρχουν σκαναρισμένα αρχεία για ανέβασμα"],404);
        }

        foreach($request->all() as $file)
        {
            $filename = $file['filename'];
            $filee = $file['preview'];

            if(!$filename || !$filee)
            {
                return response()->json(["To αρχείο στην θέση ".$count." είναι ελαττωματικό. Η διαδικασία ακυρώνεται για τα υπόλοιπα αρχεία!"],422);
            }

            $data = explode( ',', $filee );
            $toStorage = storage_path();
            $destinationPath = $toStorage."/Clients/".$client->foldername."/";
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


            //unlink($image_path);//delete the bmp file

            //$resizedImage = Resizer::resize_image($jpegPath,200,200);
            unlink($jpegPath);
           // imagejpeg($resizedImage,$destinationPath.$filename.".jpg");

            ++$count;
        }
        array_map('unlink', glob( storage_path()."/Clients/".$client->foldername."/*.bmp"));
        return response()->json(["message" => "Το σκαναρισμένο αρχείο αποθηκεύτηκε ως pdf στο σύστημα με επιτυχία!"],200);
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

        $file = storage_path()."/Clients/".$client->foldername."/".$filename;

        if(!file_exists($file))
        {
            return response()->json(["message" => "Δεν υπάρχει το αρχείο που αναζητείτε!"],404);
        }

        $fileExtension = explode('.' ,$filename);
        $n = count($fileExtension);
        $extension = $fileExtension[$n];

        if($extension == "pdf")
        {
            $headers = array(
                'Content-Type: application/pdf',
              );
        }

        if($extension == "jpeg")
        {
            $headers = array(
                'Content-Type: image/jpeg',
              );
        }

        if($extension == "jpg")
        {
            $headers = array(
                'Content-Type: image/jpg',
              );
        }

        if($extension == "png")
        {
            $headers = array(
                'Content-Type: image/png',
              );
        }

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
    public function upload(Request $request, $id)
    {

       $file = $request->file;
       return $file->getMimeType();
       // return $request;
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

            $file = $request->File;

            if($request->File == null)
            {
                return response()->json(["message" => "Θα πρέπει να υπάρχει αρχείο προς ανέβασμα"],422);
            }

		//foreach($files as $file)
       // {
			//return $file;
            //return $file->getMimeType();
            if($file->getMimeType() != "image/jpeg" && $file->getMimeType() != "application/pdf" && $file->getMimeType() != "image/png" )
            {
                return response()->json(["message" => "Μονο αρχεια τυπου jpeg, png και pdf επιτρέπονται!"],422);
            }

            if($file->getSize() > 5000000)
            {
                return response()->json(["message" => "Το αρχείο ".$file->getClientOriginalName()." είναι μεγαλύτερου του επιτρεπτού μεγέθους!"],422);
            }

           $filename_array = explode(".",$file->getClientOriginalName());
           $n = count($filename_array);
           $filename_array[$n - 1] = strtolower($filename_array[$n - 1]);

           $filename =  implode('.',$filename_array);



           if(!move_uploaded_file($file, storage_path("Clients/".$client->foldername."/".$filename)))
           {
               return response()->json(['message' => 'Παρουσιάστηκε πρόβλημα με το αρχείο '.$file->getClientOriginalName()]);
           }


       // }

        return response()->json(["message" => "Τo αρχείο ".$file->getClientOriginalName()." ανέβηκε επιτυχώς!"],200);
    }

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
        //$searchPath = explode('.',$filename);
        $mypath = storage_path().'/Clients/'.$client->foldername."/".$filename;

        $file = glob($mypath);

        if(!$file)
        {
            return response()->json(["message" => "Το συγκεκριμένο αρχείο δεν ύπαρχει στο σύστημα"],404);
        }

        unlink($mypath);

        return response()->json(["message" => "Το αρχείο διαγράφτηκε επιτυχώς!"],200);
    }
}
