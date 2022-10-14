<?php

namespace App\Service;


use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;

class PdfManager
{
    private $pdf;

    public function __construct()
    {
        // Instantiate
        $this->pdf = new Html2Pdf('P', 'A4', 'fr');
    }

    /**
     * @throws Html2PdfException
     */
    public function showPdf($html){

        $this->pdf->writeHTML($html);
        $this->pdf->output('document.pdf');

    }
}