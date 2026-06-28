<?php

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('print-deal page loads successfully', function () {
    $response = $this->get(route('print-deal', [
        'credit' => '720',
        'price' => '25000',
        'term' => '60',
        'rate' => '5.99',
        'down' => '10',
        'tradein' => '5000',
        'balance' => '2000',
        'title' => '2022 Toyota Camry',
        'stock' => 'P1234',
        'vin' => '12345678901234567',
        'actual_down' => '2000'
    ]));

    $response->assertStatus(200);
    $response->assertSee('Print Your Deal');
    $response->assertSee('2022 Toyota Camry');
    $response->assertSee('P1234');
});
