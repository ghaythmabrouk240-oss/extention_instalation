<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class QrCodeService
{
    /**
     * Generate QR code URL for equipment
     *
     * @param int $equipmentId
     * @param int $size
     * @return string
     */
    public function generateEquipmentQrUrl(int $equipmentId, int $size = 200): string
    {
        $url = route('equipements.scan', $equipmentId);
        
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($url);
    }

    /**
     * Generate QR code URL for installation graph
     *
     * @param int $installationId
     * @param string $profile
     * @param int $size
     * @return string
     */
    public function generateInstallationGraphQrUrl(int $installationId, string $profile, int $size = 200): string
    {
        $url = route('installations.graph', ['installation_id' => $installationId, 'profile' => $profile]);
        
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($url);
    }

    /**
     * Generate QR code URL for any custom data
     *
     * @param string $data
     * @param int $size
     * @return stirng
     */
    public function generateCustomQrUrl(string $data, int $size = 200): string
    {
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($data);
    }
}
