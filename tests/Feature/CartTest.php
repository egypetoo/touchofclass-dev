<?php

use App\Models\Product;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\OrganizationSeeder;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(function () {
    $this->seed(OrganizationSeeder::class);
    $this->seed(DemoContentSeeder::class);

    $this->withoutMiddleware([
        LocaleSessionRedirect::class,
        LocaleCookieRedirect::class,
        LaravelLocalizationRedirectFilter::class,
    ]);
});

it('shows an empty cart before anything is added', function () {
    $this->get('/cart')
        ->assertOk()
        ->assertSee('سلتك فاضية', false)
        ->assertDontSee('GRUNDFOS', false)
        ->assertSee('class="cart-badge">0', false);
});

it('adds the current product and quantity to the cart', function () {
    $product = Product::query()->where('code', 'KM-P-SP')->firstOrFail();

    $this->get('/products/'.$product->getRouteKey())
        ->assertOk()
        ->assertSee('action="'.route('cart.store').'"', false)
        ->assertSee('name="product_id" value="'.$product->id.'"', false);

    $this->post('/cart', [
        'product_id' => $product->id,
        'qty' => 2,
    ])->assertRedirect(route('cart'));

    $this->post('/cart', [
        'product_id' => $product->id,
        'qty' => 1,
    ])->assertRedirect(route('cart'));

    $this->get('/cart')
        ->assertOk()
        ->assertSee($product->name, false)
        ->assertSee('name="items['.$product->id.'][qty]" value="3"', false)
        ->assertSee('class="cart-badge">3', false)
        ->assertDontSee('GRUNDFOS', false);
});

it('rejects a product that is not in the catalog', function () {
    $this->from('/products')
        ->post('/cart', [
            'product_id' => 999999,
            'qty' => 1,
        ])
        ->assertRedirect('/products')
        ->assertSessionHasErrors('product_id');

    $this->get('/cart')->assertSee('سلتك فاضية', false);
});

it('sends the cart quote to WhatsApp', function () {
    $product = Product::query()->where('code', 'KM-P-SP')->firstOrFail();

    $this->post('/cart', [
        'product_id' => $product->id,
        'qty' => 2,
    ]);

    $response = $this->post('/cart/quote', [
        'name' => 'Ahmed',
        'phone' => '01000000000',
        'city' => 'cairo',
        'notes' => 'بئر 80 متر',
        'items' => [
            $product->id => ['qty' => 2],
        ],
    ]);

    $response->assertRedirect();
    expect($response->headers->get('Location'))
        ->toContain('https://wa.me/')
        ->toContain('KM-P-SP');
});

it('removes a product from the cart', function () {
    $product = Product::query()->where('code', 'KM-C-CARKIT')->firstOrFail();

    $this->post('/cart', [
        'product_id' => $product->id,
        'qty' => 1,
    ]);

    $this->post('/cart/'.$product->getRouteKey().'/remove', [], [
        'Accept' => 'application/json',
    ])->assertOk()->assertJson(['count' => 0]);

    $this->get('/cart')
        ->assertOk()
        ->assertSee('سلتك فاضية', false)
        ->assertDontSee('KM-C-CARKIT', false);
});
