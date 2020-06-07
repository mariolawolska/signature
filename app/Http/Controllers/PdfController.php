<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use PDF;

//use App\CentralSetting;

class PdfController extends Controller {

    /**
     * Convert content to pdf file
     * @return pdf file
     */
    public function toPdf() {

        $user = Auth::user()->name;

        $model = new ReportController;
        $signatures = $model->getLastSignatureOrginalArray(Auth::user()->id);
        $signature = $signatures[0];

        $link = Storage::path('public/ratrak' . DIRECTORY_SEPARATOR . Auth::user()->id . DIRECTORY_SEPARATOR . $signature);


        $pdf = PDF::loadView('pdf.layout', compact('user', 'link'));
        return $pdf->download('Ignition(' . date('Y-m-d') . ').pdf');
    }

}
