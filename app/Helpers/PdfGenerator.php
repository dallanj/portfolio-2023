<?php

namespace App\Helpers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class PdfGenerator
{
    /**
     * Render HTML as PDF using DomPDF
     *
     * @param string $html The HTML to render.
     * @param string|null $path If provided, PDF will be saved here.
     * @return string|null The PDF binary if no path is given.
     */
    public static function fromHtml(string $html, ?string $path = null): ?string
    {
        try {
            if ($path) {
                // Ensure directory exists
                $dir = dirname($path);
                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }
    
                Pdf::loadHtml($html)
                    ->setPaper('a4')
                    ->save($path);
    
                return $path;
            }
    
            return Pdf::loadHtml($html)
                ->setPaper('a4')
                ->output();
        } catch (\Throwable $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            return null;
        }
    }
}
