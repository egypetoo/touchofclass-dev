<?php

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Organization;
use App\Models\Product;
use App\Models\Service;
use App\Models\SiteSetting;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\OrganizationSeeder;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

function seedPumpSelectorCatalogue(): void
{
    $category = Category::query()->where('slug->en', 'deep-well-pumps')->first()
        ?? Category::query()->create([
            'organization_id' => Organization::query()->firstOrFail()->id,
            'name' => ['en' => 'Deep Well Pumps', 'ar' => 'طلمبات اعماق'],
            'slug' => ['en' => 'deep-well-pumps', 'ar' => 'deep-well-pumps'],
            'order' => 90,
        ]);

    $category->update(['is_active' => true]);

    $pumps = [
        ['KM-P-GOLD', 'GOLD', ['highlights' => ['270 m³/h']]],
        ['KM-P-VANSAN', 'VAN SAN', ['brand' => 'VAN SAN']],
        ['KM-P-SP', 'SP', ['highlights' => ['42 m³/h']]],
    ];

    foreach ($pumps as [$code, $name, $specifications]) {
        Product::query()->updateOrCreate(
            ['code' => $code],
            [
                'category_id' => $category->id,
                'name' => ['en' => $name, 'ar' => $name],
                'slug' => ['en' => strtolower($code), 'ar' => strtolower($code)],
                'is_active' => true,
                'specifications' => $specifications,
                'sort_order' => 1,
            ]
        );
    }
}

beforeEach(function () {
    $this->seed(OrganizationSeeder::class);
    $this->seed(DemoContentSeeder::class);
    seedPumpSelectorCatalogue();

    SiteSetting::query()->updateOrCreate(
        ['organization_id' => Organization::query()->firstOrFail()->id],
        ['turnstile_enabled' => false]
    );

    $this->withoutMiddleware([
        LocaleSessionRedirect::class,
        LocaleCookieRedirect::class,
        LaravelLocalizationRedirectFilter::class,
    ]);
});

it('lists catalogue pumps that cover the requested flow', function () {
    $this->get('/pump-selector?flow=60&flow_unit=m3h&head=120&head_unit=m')
        ->assertOk()
        ->assertSee('data-match="KM-P-GOLD"', false)
        ->assertSee('data-unrated="KM-P-VANSAN"', false)
        ->assertDontSee('data-match="KM-P-SP"', false)
        ->assertDontSee('data-unrated="KM-P-SP"', false)
        ->assertDontSee('GRUNDFOS', false)
        ->assertSee('60', false)
        ->assertSee('120', false)
        ->assertSee('Start a project', false)
        ->assertSee('Understand the space', false)
        ->assertDontSee('اختيار الطلمبة', false);
});

it('drops a pump whose catalogue flow is below the request', function () {
    $this->get('/pump-selector?flow=500&flow_unit=m3h&head=40&head_unit=m')
        ->assertOk()
        ->assertDontSee('data-match="KM-P-GOLD"', false)
        ->assertSee('data-unrated="KM-P-VANSAN"', false);
});

it('rejects a negative flow', function () {
    $this->from('/pump-selector')
        ->get('/pump-selector?flow=-1&head=10')
        ->assertRedirect('/pump-selector')
        ->assertSessionHasErrors('flow');
});

it('stores the home contact form against a dashboard service', function () {
    $service = Service::query()->where('is_active', true)->firstOrFail();

    $this->post(route('contact.store'), [
        'name' => 'Home Visitor',
        'phone' => '01000000000',
        'email' => 'home@example.com',
        'service_id' => $service->id,
        'message' => 'Need a pump for a well',
    ])->assertRedirect(route('contact.thanks'));

    $this->get(route('contact.thanks'))
        ->assertOk()
        ->assertSee(__('site.thank_you'), false)
        ->assertSee(__('site.contact_message_sent'), false);

    $message = ContactMessage::query()->where('email', 'home@example.com')->sole();

    expect($message->service_id)->toBe($service->id)
        ->and($message->name)->toBe('Home Visitor')
        ->and($message->message)->toBe('Need a pump for a well');
});
