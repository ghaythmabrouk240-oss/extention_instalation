<?php

namespace Tests\Unit;

use App\Services\QrCodeService;
use Tests\TestCase;

class QrCodeServiceTest extends TestCase
{
    public function test_generate_equipment_qr_url()
    {
        $service = new QrCodeService();
        $url = $service->generateEquipmentQrUrl(1, 200);

        $this->assertStringContainsString('https://api.qrserver.com/v1/create-qr-code/', $url);
        $this->assertStringContainsString('size=200x200', $url);
        $this->assertStringContainsString('equipements%2F1%2Fscan', $url);
    }

    public function test_generate_equipment_qr_url_with_custom_size()
    {
        $service = new QrCodeService();
        $url = $service->generateEquipmentQrUrl(1, 300);

        $this->assertStringContainsString('size=300x300', $url);
    }

    public function test_generate_installation_graph_qr_url()
    {
        $service = new QrCodeService();
        $url = $service->generateInstallationGraphQrUrl(1, 'CATHETERISME', 200);

        $this->assertStringContainsString('https://api.qrserver.com/v1/create-qr-code/', $url);
        $this->assertStringContainsString('size=200x200', $url);
        $this->assertStringContainsString('CATHETERISME', $url);
    }

    public function test_generate_custom_qr_url()
    {
        $service = new QrCodeService();
        $url = $service->generateCustomQrUrl('https://example.com', 200);

        $this->assertStringContainsString('https://api.qrserver.com/v1/create-qr-code/', $url);
        $this->assertStringContainsString('https%3A%2F%2Fexample.com', $url);
    }
}
