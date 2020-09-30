<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;

use ZipArchive;

use Response;

use App\Http\CustomClasses\v1\FlxZipArchive;

class ZipController extends Controller
{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function downloadZip()
    {
        $the_folder = storage_path('Clients');
        $zip_file_name = storage_path('client_files' . date('Y-m-d') . '.zip');

        //return response()->json(exec('dir'));
        //$zip = $this->createZipFromDir(storage_path('Clients'), storage_path('files.zip'));
        $za = new FlxZipArchive;
        $res = $za->open($zip_file_name, ZipArchive::CREATE);
        if ($res === TRUE) {
            $za->addDir($the_folder, basename($the_folder));
            $za->close();
            return response()->download($zip_file_name, 'client_files' . date('Y-m-d') . '.zip', array('Content-Type: application/zip'));
        } else {
            return response()->json(['message' => 'Could not create a zip archive'], 400);
        }
    }

    public function createZipFromDir($dir, $zip_file)
    {
        $zip = new ZipArchive;
        if (true !== $zip->open($zip_file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE)) {
            return false;
        }
        $this->zipDir($dir, $zip);
        return $zip;
    }

    public function zipDir($dir, $zip, $relative_path = DIRECTORY_SEPARATOR)
    {
        $dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                if (is_file($dir . $file)) {
                    $zip->addFile($dir . $file, $file);
                } elseif (is_dir($dir . $file)) {
                    $this->zipDir($dir . $file, $zip, $relative_path . $file);
                }
            }
        }
        closedir($handle);
    }
}
