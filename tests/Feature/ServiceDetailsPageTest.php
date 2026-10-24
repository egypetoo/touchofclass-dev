<?php

use App\Models\ContactMessage;
use App\Models\Service;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\OrganizationSeeder;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

it('renders the King Motors service details layout', function () {
    $this->seed(OrganizationSeeder::class);
    $this->seed(DemoContentSeeder::class);

    $service = Service::query()
        ->where('slug->en', 'home-renovation')
        ->firstOrFail();

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/services/'.$service->getTranslation('slug', 'en'));

    $response->assertOk()
        ->assertSee('km-page-title', false)
        ->assertSee($service->title, false)
        ->assertSee('km-sd-sidebar', false)
        ->assertSee('Interior Design', false)
        ->assertSee('name="service_id"', false)
        ->assertSee(__('site.service_submit'), false)
        ->assertDontSee('تجديد المنازل', false)
        ->assertDontSee('التصميم الداخلي', false);

    $this->post(route('contact.store'), [
        'name' => 'Service Visitor',
        'phone' => '01000000000',
        'service_id' => $service->id,
        'message' => 'Need spare parts',
    ])->assertRedirect(route('contact.thanks'));

    expect(ContactMessage::query()->where('email', null)->where('name', 'Service Visitor')->first()?->service_id)
        ->toBe($service->id);
});
