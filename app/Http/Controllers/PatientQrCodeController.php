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
        $renderer = new ImageRenderer(new RendererStyle(256), new SvgImageBackEnd);
        $writer = new Writer($renderer);
        $profileUrl = route('patients.show', $patient);

        return response($writer->writeString($profileUrl), 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
