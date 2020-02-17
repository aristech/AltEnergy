<?php

namespace App\Http\Controllers\v1;

require '../vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Damage;
use App\Http\CustomClasses\v1\TechMail;
use PDF;
use ConvertApi\ConvertApi;


class TestController extends Controller
{
    public function test(Request $request)
    {
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path() . '/test.docx');


        $templateProcessor->cloneRow('value', 100);
        for ($i = 1; $i <= 100; $i++) {
            $templateProcessor->setValue('value#' . $i, 'element');
        }

        $templateProcessor->saveAs(public_path() . '/xx.docx');

        ConvertApi::setApiSecret('cqbWK6STXKAFVUVD');

        $result = ConvertApi::convert('pdf', ['File' => public_path() . '/xx.docx']);

        # save to file
        $result->getFile()->save(public_path() . '/product.pdf');
    }
}
