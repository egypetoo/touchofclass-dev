<?php

use Illuminate\Support\Facades\Route;

it('does not register visitor account routes', function (string $routeName) {
    expect(Route::has($routeName))->toBeFalse();
})->with([
    'login',
    'register',
    'logout',
    'dashboard',
    'profile.edit',
    'password.request',
    'password.reset',
    'verification.notice',
]);

it('keeps the admin panel login available', function () {
    $this->get('/admin/login')->assertOk();
});
