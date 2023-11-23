<?php

use Joelwmale\LivewireQuill\Http\Livewire\LivewireQuill;

use function Pest\Livewire\livewire;

it('throws an exception if not quillId is passed in', function () {
    livewire(LivewireQuill::class);
})->throws(Exception::class);

it('loads the editor if correct parameters are passed in', function () {
    livewire(LivewireQuill::class, [
        'quillId' => 'quillId',
    ])->assertSeeHtml('id="quillId"');
});
