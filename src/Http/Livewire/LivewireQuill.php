<?php

namespace Joelwmale\LivewireQuill\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class LivewireQuill extends Component
{
    use WithFileUploads;

    public $rendered = false;

    // configurable
    public $quillId;
    public $data;
    public $classes;
    public $toolbar;
    public $placeholder;

    public $mergeToolbar = true;

    // vendor
    public $quillImages;

    public $quillUploadedImages;

    public function mount(
        $quillId,
        $data = null,
        $placeholder = null,
        $classes = '',
        $toolbar = [],
        $mergeToolbar = true
    ) {
        $this->quillId = $quillId;
        $this->data = $data;
        $this->placeholder = $placeholder;
        $this->classes = $classes;
        $this->toolbar = $mergeToolbar ? array_merge(
            config('livewire-quill.toolbar'),
            $toolbar
        ) : $toolbar;
    }

    public function updatedQuillImages()
    {
        foreach ($this->quillImages as $image) {
            if (! isset($this->quillUploadedImages[$image->getFilename()])) {
                $this->quillUploadedImages[$image->getFilename()] = $image->store(
                    config('livewire-quill.storage_folder'),
                    config('livewire-quill.store_publically') ? 'public' : null
                );
            }
        }
    }

    public function deleteImage($imageUrl)
    {
        if (strpos($imageUrl, '/storage/') !== false) {
            $imageUrl = str_replace('/storage/', '', $imageUrl);
        }

        if ($imageUrl && config('livewire-quill.clean_up_deleted_images')) {
            Storage::disk('public')->delete($imageUrl);
        }
    }

    public function render()
    {
        if (! $this->rendered) {
            $this->dispatch('livewire-quill:init', [
                'quillId' => $this->quillId,
                'data' => $this->data,
                'placeholder' => $this->placeholder,
                'classes' => $this->classes,
                'toolbar' => $this->toolbar,
            ]);
        }

        $this->rendered = true;

        return view('livewire-quill::livewire.livewire-quill');
    }
}
