<?php

namespace App\Services;

use App\Common\BaseService;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfGenerator extends BaseService
{
    public function generatePdf(string $viewName, array $data = [], string $fileName = 'document.pdf'): string
    {
        return Pdf::loadView($viewName, $data)->stream($fileName);
    }
}