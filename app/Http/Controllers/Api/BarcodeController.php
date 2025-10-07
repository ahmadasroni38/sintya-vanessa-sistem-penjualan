<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class BarcodeController extends Controller
{
    /**
     * Generate barcode for a single asset.
     */
    public function generateAssetBarcode(Request $request, Asset $asset)
    {
        $generator = new BarcodeGeneratorPNG();

        // Generate barcode using asset code or ID as fallback
        $barcodeData = $asset->code ?: 'ASSET-' . $asset->id;

        try {
            // Generate barcode image
            $barcode = $generator->getBarcode($barcodeData, $generator::TYPE_CODE_128, 3, 100);

            // Create PDF
            $pdf = $this->createBarcodePDF([
                [
                    'asset' => $asset,
                    'barcode' => base64_encode($barcode),
                    'barcodeData' => $barcodeData
                ]
            ]);

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="barcode_' . $barcodeData . '.pdf"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate barcode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate barcodes for multiple assets.
     */
    public function generateBulkBarcodes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_ids' => 'required|array|min:1',
            'asset_ids.*' => 'required|integer|exists:assets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $assets = Asset::whereIn('id', $request->asset_ids)->get();

        if ($assets->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No assets found'
            ], 404);
        }

        $generator = new BarcodeGeneratorPNG();
        $barcodeData = [];

        try {
            foreach ($assets as $asset) {
                $code = $asset->code ?: 'ASSET-' . $asset->id;
                $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128, 3, 100);

                $barcodeData[] = [
                    'asset' => $asset,
                    'barcode' => base64_encode($barcode),
                    'barcodeData' => $code
                ];
            }

            // Create PDF with multiple barcodes
            $pdf = $this->createBarcodePDF($barcodeData);

            $filename = 'bulk_barcodes_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate bulk barcodes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create PDF with barcode(s).
     */
    private function createBarcodePDF(array $barcodeData): Dompdf
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        // Generate HTML for PDF
        $html = $this->generateBarcodeHTML($barcodeData);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf;
    }

    /**
     * Generate HTML template for barcode PDF.
     */
    private function generateBarcodeHTML(array $barcodeData): string
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Asset Barcodes</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                }
                .barcode-container {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 20px;
                    justify-content: space-around;
                }
                .barcode-item {
                    border: 2px solid #333;
                    padding: 15px;
                    text-align: center;
                    width: 300px;
                    margin-bottom: 20px;
                    border-radius: 8px;
                    background: #fff;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                .barcode-item h3 {
                    margin: 0 0 10px 0;
                    font-size: 16px;
                    font-weight: bold;
                    color: #333;
                }
                .barcode-image {
                    margin: 15px 0;
                }
                .barcode-code {
                    font-family: monospace;
                    font-size: 14px;
                    font-weight: bold;
                    margin: 10px 0;
                    letter-spacing: 2px;
                }
                .asset-info {
                    font-size: 12px;
                    color: #666;
                    margin-top: 10px;
                }
                .asset-info div {
                    margin: 3px 0;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 20px;
                }
                .header h1 {
                    margin: 0;
                    color: #333;
                }
                .header p {
                    margin: 5px 0 0 0;
                    color: #666;
                }
                .page-break {
                    page-break-before: always;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Asset Management System</h1>
                <p>Asset Barcodes - Generated on ' . now()->format('F j, Y \a\t g:i A') . '</p>
            </div>

            <div class="barcode-container">';

        $itemsPerPage = 6; // Adjust based on your needs
        $currentItem = 0;

        foreach ($barcodeData as $data) {
            if ($currentItem > 0 && $currentItem % $itemsPerPage === 0) {
                $html .= '</div><div class="page-break"></div><div class="barcode-container">';
            }

            $asset = $data['asset'];
            $html .= '
                <div class="barcode-item">
                    <h3>' . htmlspecialchars($asset->name) . '</h3>
                    <div class="barcode-image">
                        <img src="data:image/png;base64,' . $data['barcode'] . '" alt="Barcode">
                    </div>
                    <div class="barcode-code">' . htmlspecialchars($data['barcodeData']) . '</div>
                    <div class="asset-info">
                        <div><strong>Asset Code:</strong> ' . htmlspecialchars($asset->code ?: 'N/A') . '</div>
                        <div><strong>Serial:</strong> ' . htmlspecialchars($asset->serial_number ?: 'N/A') . '</div>
                        <div><strong>Category:</strong> ' . htmlspecialchars($asset->category->name ?? 'N/A') . '</div>
                        <div><strong>Location:</strong> ' . htmlspecialchars($asset->location->name ?? 'N/A') . '</div>
                        <div><strong>Status:</strong> ' . htmlspecialchars(ucfirst(str_replace('_', ' ', $asset->status))) . '</div>
                    </div>
                </div>';

            $currentItem++;
        }

        $html .= '
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Generate QR code for asset (bonus feature).
     */
    public function generateAssetQRCode(Request $request, Asset $asset)
    {
        try {
            // Asset URL or identifier for QR code
            $qrData = url('/assets/' . $asset->id);

            // You can use a QR code library like endroid/qr-code
            // For now, we'll return the data that should be encoded
            return response()->json([
                'success' => true,
                'data' => [
                    'qr_data' => $qrData,
                    'asset' => [
                        'id' => $asset->id,
                        'name' => $asset->name,
                        'code' => $asset->code,
                        'url' => $qrData
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export QR Code for a single asset to PDF.
     */
    public function exportAssetQRCodePDF(Request $request, Asset $asset)
    {
        try {
            $qrCodeData = [
                $this->generateQRCodeData($asset, $request->input('size', 'medium'))
            ];

            $pdf = $this->createQRCodePDF($qrCodeData, $request->input('size', 'medium'));

            $filename = 'qrcode_' . ($asset->code ?: $asset->id) . '.pdf';

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export QR Codes for multiple selected assets to PDF.
     */
    public function exportBulkQRCodePDF(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_ids' => 'required|array|min:1',
            'asset_ids.*' => 'required|integer|exists:assets,id',
            'size' => 'nullable|string|in:small,medium,large',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $assets = Asset::with(['category', 'location'])
            ->whereIn('id', $request->asset_ids)
            ->get();

        if ($assets->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No assets found'
            ], 404);
        }

        try {
            $size = $request->input('size', 'medium');
            $qrCodeData = [];

            foreach ($assets as $asset) {
                $qrCodeData[] = $this->generateQRCodeData($asset, $size);
            }

            $pdf = $this->createQRCodePDF($qrCodeData, $size);

            $filename = 'bulk_qrcodes_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export bulk QR codes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export QR Codes for all assets to PDF.
     */
    public function exportAllQRCodePDF(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'size' => 'nullable|string|in:small,medium,large',
            'limit' => 'nullable|integer|min:1|max:500',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = Asset::with(['category', 'location']);

        // Apply limit and offset if provided
        if ($request->has('limit')) {
            $query->limit($request->input('limit'));
        }
        if ($request->has('offset')) {
            $query->offset($request->input('offset'));
        }

        $assets = $query->get();

        if ($assets->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No assets found'
            ], 404);
        }

        try {
            $size = $request->input('size', 'medium');
            $qrCodeData = [];

            foreach ($assets as $asset) {
                $qrCodeData[] = $this->generateQRCodeData($asset, $size);
            }

            $pdf = $this->createQRCodePDF($qrCodeData, $size);

            $filename = 'all_qrcodes_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export all QR codes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate QR code data for an asset.
     */
    private function generateQRCodeData(Asset $asset, string $size = 'medium'): array
    {
        // Get QR code URL from config
        $baseUrl = config('asset.qr_base_url');
        $urlType = config('asset.qr_url_type', 'id');

        // Determine identifier based on config
        $identifier = $urlType === 'code' ? $asset->code : $asset->id;
        $assetUrl = $baseUrl . '/' . $identifier;

        // QR Code content: URL to asset detail page
        $qrContent = $assetUrl;

        // Create QR code
        $qrCode = new QrCode($qrContent);

        // Set size based on parameter
        $qrSize = $this->getQRCodeSize($size);
        $qrCode->setSize($qrSize);
        $qrCode->setMargin(10);

        // Generate PNG image
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return [
            'asset' => $asset,
            'qrcode' => base64_encode($result->getString()),
            'qrData' => $asset->code ?: 'ASSET-' . $asset->id,
            'qrUrl' => $assetUrl,
            'size' => $size
        ];
    }

    /**
     * Get QR code size based on size parameter.
     */
    private function getQRCodeSize(string $size): int
    {
        return match($size) {
            'small' => 150,
            'large' => 300,
            default => 200, // medium
        };
    }

    /**
     * Create PDF with QR code(s) in grid layout.
     */
    private function createQRCodePDF(array $qrCodeData, string $size = 'medium'): Dompdf
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        // Generate HTML for PDF
        $html = $this->generateQRCodeHTML($qrCodeData, $size);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf;
    }

    /**
     * Generate HTML template for QR code PDF with grid layout.
     */
    private function generateQRCodeHTML(array $qrCodeData, string $size = 'medium'): string
    {
        // Determine grid layout based on size
        $gridConfig = $this->getGridConfig($size);

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Asset QR Codes</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 15px;
                }
                .qrcode-container {
                    display: flex;
                    flex-wrap: wrap;
                    gap: ' . $gridConfig['gap'] . 'px;
                    justify-content: flex-start;
                }
                .qrcode-item {
                    border: 1px solid #ddd;
                    padding: ' . $gridConfig['padding'] . 'px;
                    text-align: center;
                    width: ' . $gridConfig['itemWidth'] . 'px;
                    margin-bottom: ' . $gridConfig['marginBottom'] . 'px;
                    border-radius: 4px;
                    background: #fff;
                    box-sizing: border-box;
                    break-inside: avoid;
                }
                .qrcode-item h3 {
                    margin: 0 0 8px 0;
                    font-size: ' . $gridConfig['titleSize'] . 'px;
                    font-weight: bold;
                    color: #333;
                    word-wrap: break-word;
                }
                .qrcode-image {
                    margin: 8px 0;
                }
                .qrcode-image img {
                    max-width: 100%;
                    height: auto;
                }
                .qrcode-code {
                    font-family: monospace;
                    font-size: ' . $gridConfig['codeSize'] . 'px;
                    font-weight: bold;
                    margin: 8px 0;
                    letter-spacing: 1px;
                    color: #000;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 15px;
                }
                .header h1 {
                    margin: 0;
                    color: #333;
                    font-size: 20px;
                }
                .header p {
                    margin: 5px 0 0 0;
                    color: #666;
                    font-size: 11px;
                }
                .page-break {
                    page-break-before: always;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Daftar QR Code Aset</h1>
                <p>Generated on ' . now()->format('d F Y, H:i:s') . '</p>
                <p>Total Assets: ' . count($qrCodeData) . '</p>
            </div>

            <div class="qrcode-container">';

        $itemsPerPage = $gridConfig['itemsPerPage'];
        $currentItem = 0;

        foreach ($qrCodeData as $data) {
            if ($currentItem > 0 && $currentItem % $itemsPerPage === 0) {
                $html .= '</div><div class="page-break"></div>';
                $html .= '<div class="header">
                    <h1>Daftar QR Code Aset</h1>
                    <p>Continued - Page ' . (floor($currentItem / $itemsPerPage) + 1) . '</p>
                </div>';
                $html .= '<div class="qrcode-container">';
            }

            $asset = $data['asset'];
            $html .= '
                <div class="qrcode-item">
                    <h3>' . htmlspecialchars($asset->name) . '</h3>
                    <div class="qrcode-image">
                        <img src="data:image/png;base64,' . $data['qrcode'] . '" alt="QR Code">
                    </div>
                    <div class="qrcode-code">' . htmlspecialchars($data['qrData']) . '</div>
                </div>';

            $currentItem++;
        }

        $html .= '
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Get grid configuration based on QR code size.
     */
    private function getGridConfig(string $size): array
    {
        return match($size) {
            'small' => [
                'itemWidth' => 130,
                'gap' => 8,
                'padding' => 8,
                'marginBottom' => 8,
                'titleSize' => 10,
                'codeSize' => 9,
                'itemsPerPage' => 20, // 4 columns × 5 rows
            ],
            'large' => [
                'itemWidth' => 250,
                'gap' => 15,
                'padding' => 12,
                'marginBottom' => 15,
                'titleSize' => 14,
                'codeSize' => 12,
                'itemsPerPage' => 6, // 2 columns × 3 rows
            ],
            default => [ // medium
                'itemWidth' => 180,
                'gap' => 10,
                'padding' => 10,
                'marginBottom' => 10,
                'titleSize' => 12,
                'codeSize' => 10,
                'itemsPerPage' => 15, // 3 columns × 5 rows
            ],
        };
    }
}