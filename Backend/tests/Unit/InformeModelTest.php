<?php

namespace Tests\Unit;

use App\Models\Informe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InformeModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Un informe recién creado no debe considerarse bloqueado.
     */
    public function test_fresh_informe_is_not_locked(): void
    {
        $informe = Informe::factory()->create([
            'bloqueado_en' => null,
            'created_at'   => now(),
        ]);

        $this->assertFalse($informe->isBloqueado());
    }

    /**
     * Un informe con fecha límite vencida debe retornar isBloqueado() true.
     */
    public function test_expired_informe_is_locked(): void
    {
        $informe = Informe::factory()->create([
            'bloqueado_en' => now()->subDay(),
        ]);

        $this->assertTrue($informe->isBloqueado());
    }
}
