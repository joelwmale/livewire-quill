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
    // Livewire::test() does not render a full HTTP response, so @assets content
    // is never injected into <head> and cannot be asserted via assertSeeHtml().
    // Instead, we verify the snapshot memo confirms assets were registered during render,
    // and check the view template contains the expected asset references.
    $component = Livewire::test(LivewireQuill::class, ['quillId' => 'scriptsQuill']);

    expect($component->snapshot['memo']['assets'])->not->toBeEmpty();

    $viewPath = view('livewire-quill::livewire.livewire-quill')->getPath();
    $viewContent = file_get_contents($viewPath);

    expect($viewContent)->toContain('quill.snow.min.css');
    expect($viewContent)->toContain('quill.js');
});

it('does not use var declarations at the top level of @script blocks', function () {
    // Livewire 3 wraps @script content in a module/strict context where top-level
    // var declarations cause "Uncaught SyntaxError: Unexpected token var" on page load.
    // Livewire::test() never executes JS, so this static check prevents regressions.
    $viewPath = view('livewire-quill::livewire.livewire-quill')->getPath();
    $viewContent = file_get_contents($viewPath);

    // Extract content between @script and @endscript
    preg_match('/@script\s*<script>(.*?)<\/script>\s*@endscript/s', $viewContent, $matches);
    expect($matches)->not->toBeEmpty('Expected to find a @script block in the view');

    $scriptContent = $matches[1];

    // Check for top-level var declarations (lines that start with var after optional whitespace,
    // but not inside a function body — we approximate by checking lines at the shallowest indent)
    $lines = explode("\n", $scriptContent);
    $scriptIndent = null;

    foreach ($lines as $line) {
        $trimmed = ltrim($line);
        if ($trimmed === '' || $trimmed === '//') {
            continue;
        }

        // Determine the base indent of the script block from the first non-empty line
        if ($scriptIndent === null) {
            $scriptIndent = strlen($line) - strlen($trimmed);
        }

        $currentIndent = strlen($line) - strlen($trimmed);

        // Only check lines at the script block's base indent level (top-level declarations)
        if ($currentIndent === $scriptIndent && preg_match('/^var\s/', $trimmed)) {
            $this->fail("Found top-level 'var' declaration in @script block: \"{$trimmed}\". Use 'let' or 'const' instead — Livewire 3 wraps @script in a module context where 'var' causes syntax errors.");
        }
    }
});
