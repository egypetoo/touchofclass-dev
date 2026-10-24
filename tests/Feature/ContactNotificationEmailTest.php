<?php

use App\Services\Mail\FormNotificationService;

it('renders the contact email in english with the card layout', function () {
    $html = app(FormNotificationService::class)->contactMessageHtml('Touch of Class', [
        'Request type' => 'Interior design',
        'Name' => 'Sarah Samir',
        'Phone' => '01112302002',
        'Email' => 'info@touchofclass-eg.com',
        'Message' => "First line\nSecond line",
    ]);

    expect($html)
        ->toContain('lang="en"')
        ->toContain('New request from Touch of Class')
        ->toContain('Request type')
        ->toContain('Interior design')
        ->toContain('Sarah Samir')
        ->toContain('01112302002')
        ->toContain('info@touchofclass-eg.com')
        ->toContain('border-left:5px solid #c6a664')
        ->toContain('First line<br />')
        ->not->toContain('رسالة تواصل');
});
