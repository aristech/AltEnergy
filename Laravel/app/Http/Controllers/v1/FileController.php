<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use File;
use Illuminate\Support\Facades\Storage;

use App\Client;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$id)
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

        $files_array = array();
        $mypath = '/Clients/'.$id;
        $files = Storage::allFiles($mypath);

        foreach($files as $file)
        {
            return $url = Storage::url(
                'file.jpg'
            );
            return storage_path().$file;
            $fileObj = new \stdClass();
            $filename = storage_path().$file;
            //$filename = $filename(count($filename)-1);
            $fileObj->filename = $filename;
            array_push($files_array,$fileObj);

        }


        // return $files_array;




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
        $validator = Validator::make(
            $request->all(),
            [
                'files' =>'required',
                'files.*' => 'required|mimes:pdf,jpeg,png,jpg |max:4096',
            ]);

        if($validator->fails())
        {
            return response()->json(["message"=>$validator->errors()->first()],422);
        }

        $files = $request->files;

        foreach($files as $file)
        {
            $location = $file->getClientOriginalName();
            $toStorage = storage_path();
            $destinationPath = $toStorage."/Clients/".$id;
            $file->move($destinationPath, $location);
        }

        return response()->json(["message" => "Τα αρχεία ανέβηκαν με επιτυχία"],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$file)
    {
        // Return the document as a response
        //return Storage::response($document->path);
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
    public function destroy($id)
    {
        //
    }
}
