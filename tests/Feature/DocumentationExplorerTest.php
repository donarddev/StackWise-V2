<?php

use function Pest\Laravel\get;

test('documentation explorer loads', function () {
    get(route('documentation.index'))
        ->assertOk()
        ->assertSee('Explore languages, frameworks, and SDLC models', false)
        ->assertSee('>12<', false)
        ->assertSee('>13<', false)
        ->assertSee('>11<', false);
});

test('documentation explorer filters by search and category', function () {
    get(route('documentation.index', [
        'search' => 'Python',
        'category' => 'languages',
    ]))
        ->assertOk()
        ->assertSee('Python', false);
});

test('documentation explorer shows empty state when nothing matches', function () {
    get(route('documentation.index', [
        'search' => 'zzzznonexistenttopic',
        'category' => 'all',
    ]))
        ->assertOk()
        ->assertSee('No documentation topics found', false);
});
