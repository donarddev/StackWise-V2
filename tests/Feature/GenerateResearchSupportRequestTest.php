<?php

use App\Http\Requests\GenerateResearchSupportRequest;
use Illuminate\Support\Facades\Validator;

test('generate research support request accepts optional study fields', function () {
    $validator = Validator::make([
        'study_title' => 'Capstone Research Title',
        'study_context' => 'Focus on authentication and reporting modules.',
    ], (new GenerateResearchSupportRequest)->rules());

    expect($validator->fails())->toBeFalse();
});

test('generate research support request rejects study title longer than 255 characters', function () {
    $validator = Validator::make([
        'study_title' => str_repeat('a', 256),
    ], (new GenerateResearchSupportRequest)->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('study_title'))->toBeTrue();
});

test('generate research support request rejects study context longer than 5000 characters', function () {
    $validator = Validator::make([
        'study_context' => str_repeat('a', 5001),
    ], (new GenerateResearchSupportRequest)->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('study_context'))->toBeTrue();
});
