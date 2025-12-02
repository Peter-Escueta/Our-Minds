<?php

namespace App\Http\Controllers\Api;

use App\Models\Child;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\Log;

class ChildConsentController extends Controller
{
    public function generateConsent(Child $child)
    {
        try {
            $data = [
                'child' => $child,
                'date' => now()->format('F j, Y'),
                'centerName' => 'OUR MINDS Intervention & Therapy Center',
                'centerLocation' => 'San Pedro Laguna',
                'centerContact' => '(09912583429)',
                'specialist' => 'Raymond E. Mindanao MA LPT RPm',
                'specialistTitle' => 'Special Education Specialist/Consultant',
                'logo' => $this->getBase64Image(public_path('images/logo.png'))
            ];

            $pdf = Pdf::loadView('consent-form', $data)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'defaultFont' => 'Calibri',
                    'chroot' => public_path()
                ]);

            return $pdf->stream("consent-form-{$child->surname}.pdf");
        } catch (\Exception $e) {
            Log::error("PDF Generation Error: " . $e->getMessage());
            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getBase64Image($path)
    {
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }
}
