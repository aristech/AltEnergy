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


class TestController extends Controller
{

    public function test(Request $request)
    {
        /*
        TCPDF::SetHeaderData(public_path('offer_header.jpg'));
        TCPDF::SetAuthor('Nicola Asuni');
        TCPDF::SetTitle('TCPDF Example 003');
        TCPDF::SetSubject('TCPDF Tutorial');
        TCPDF::SetKeywords('TCPDF, PDF, example, test, guide');

        TCPDF::setHeaderCallback(function ($pdf) {
            $pdf->writeHTML('<img src="' . public_path('offer_header.jpg') . '"style="">');
            $pdf->SetTopMargin(70);
        });

        // set default monospaced font
        TCPDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        TCPDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //TCPDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        TCPDF::SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        TCPDF::SetAutoPageBreak(true, 0);

        // set image scale factor
        TCPDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            TCPDF::setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        TCPDF::SetFont('times', 'BI', 12);

        // // add a page
        TCPDF::AddPage();

        // // set some text to print

        // print a block of text using Write()
        TCPDF::writeHTML('<br><br><br>' . $request->test, true, false, true, false, '');

        // ---------------------------------------------------------

        //Close and output PDF document
        TCPDF::Output(public_path('example_3000.pdf'), 'F');
        return 'Ok';
        */
    }
}
