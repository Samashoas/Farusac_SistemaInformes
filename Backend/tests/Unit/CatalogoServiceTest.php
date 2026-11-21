<?php

namespace Tests\Unit;

use App\Services\CatalogoService;
use Tests\TestCase;

class CatalogoServiceTest extends TestCase
{
    /**
     * Verificar que el servicio retorna las carreras institucionales.
     */
    public function test_catalogo_service_returns_carreras(): void
    {
        $service = new CatalogoService();
        $carreras = $service->getCarreras();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $carreras);
        $this->assertTrue($carreras->contains('Arquitectura'));
        $this->assertTrue($carreras->contains('Diseño Gráfico'));
    }

    /**
     * Verificar que el servicio retorna las áreas predeterminadas para Arquitectura.
     */
    public function test_catalogo_service_returns_areas(): void
    {
        $service = new CatalogoService();
        $areas = $service->getAreasPorCarrera();

        $this->assertIsArray($areas);
        $this->assertArrayHasKey('Arquitectura', $areas);
        $this->assertContains('Diseño Arquitectónico', $areas['Arquitectura']);
    }
}
