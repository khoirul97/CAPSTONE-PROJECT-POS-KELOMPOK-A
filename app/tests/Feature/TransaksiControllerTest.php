<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\TransaksiController;
use App\Services\TransaksiService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class TransaksiControllerTest extends TestCase
{
    public function test_batal_mengirim_user_id_dan_id_transaksi_dalam_urutan_yang_benar()
    {
        $service = Mockery::mock(TransaksiService::class);
        $service->shouldReceive('batal')
            ->once()
            ->with(7, '42')
            ->andReturn((object) ['id' => 42]);

        $request = Request::create('/api/transaksi/42/batal', 'POST');
        $request->setUserResolver(function () {
            return (object) ['id' => 7];
        });

        $response = (new TransaksiController($service))->batal($request, '42');

        $this->assertSame(200, $response->getStatusCode());
    }
}
