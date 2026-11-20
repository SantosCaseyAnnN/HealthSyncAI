<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Response;

class PatientQrCodeController extends Controller
{
    public function __invoke(Patient $patient): Response
    {
        return $this->qrCodeResponse(route('patient-check-in'));
    }

    public function publicCheckIn(): Response
    {
        return $this->qrCodeResponse(route('patient-check-in'));
    }

    private function qrCodeResponse(string $url): Response
    {
        $renderer = new ImageRenderer(new RendererStyle(256), new SvgImageBackEnd);
        $writer = new Writer($renderer);

        return response($writer->writeString($url), 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
