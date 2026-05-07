<?php

test('stackwise logo asset path appears on the home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('images/StackWise_Logo.png', false);
});

test('stackwise logo asset path appears on the login page', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('images/StackWise_Logo.png', false);
});

test('stackwise logo file exists in public images', function () {
    expect(public_path('images/StackWise_Logo.png'))->toBeFile();
});
