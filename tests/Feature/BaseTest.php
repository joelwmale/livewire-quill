<?php

use Livewire\Livewire;
use Illuminate\Support\Facades\Config;
use Joelwmale\LivewireQuill\Http\Livewire\LivewireQuill;

beforeEach(function () {
    Config::set('livewire-quill.storage_folder', 'quill-images');
    Config::set('livewire-quill.store_publically', true);
    Config::set('livewire-quill.clean_up_deleted_images', true);
});

it('throws an exception if not quillId is passed in', function () {
    Livewire::test(LivewireQuill::class);
})->throws(Exception::class);

it('mounts the component with default values', function () {
    config(['livewire-quill.toolbar' => []]);

    Livewire::test(LivewireQuill::class, ['quillId' => 'testQuill'])
        ->assertSet('quillId', 'testQuill')
        ->assertSet('data', null)
        ->assertSet('classes', '')
        ->assertSet('toolbar', []);
});

it('mounts the toolbar from the config if it is set', function () {
    config(['livewire-quill.toolbar' => [['bold', 'italic']]]);

    Livewire::test(LivewireQuill::class, ['quillId' => 'testQuill'])
        ->assertSet('quillId', 'testQuill')
        ->assertSet('data', null)
        ->assertSet('classes', '')
        ->assertSet('toolbar', [['bold', 'italic']]);
});

it('merges the toolbar from the config if it is set', function () {
    config(['livewire-quill.toolbar' => ['bold', 'italic']]);

    Livewire::test(LivewireQuill::class, ['quillId' => 'testQuill', 'mergeToolbar' => true, 'toolbar' => ['underline']])
        ->assertSet('quillId', 'testQuill')
        ->assertSet('data', null)
        ->assertSet('classes', '')
        ->assertSet('toolbar', ['bold', 'italic', 'underline']);
});

it('mounts the component with custom values', function () {
    Livewire::test(LivewireQuill::class, [
        'quillId' => 'customQuill',
        'data' => 'Initial content',
        'classes' => 'custom-class',
        'toolbar' => [['bold', 'italic']],
        'mergeToolbar' => false,
    ])
        ->assertSet('quillId', 'customQuill')
        ->assertSet('data', 'Initial content')
        ->assertSet('classes', 'custom-class')
        ->assertSet('toolbar', [['bold', 'italic']]);
});

it('renders the correct view', function () {
    $component = Livewire::test(LivewireQuill::class, ['quillId' => 'renderQuill']);
    $component->assertViewIs('livewire-quill::livewire.livewire-quill');
});

it('includes necessary Quill scripts and styles', function () {
    Livewire::test(LivewireQuill::class, ['quillId' => 'scriptsQuill'])
        ->assertSeeHtml('quill.snow.min.css')
        ->assertSeeHtml('quill.js');
});
