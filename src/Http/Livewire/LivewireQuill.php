<?php

namespace Joelwmale\LivewireQuill\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class LivewireQuill extends Component
{
    use WithFileUploads;

    // configurable
    public $quillId;

    public $data;

    public $classes;

    public $toolbar;

    // vendor
    public $quillImages;

    public $quillUploadedImages;

    public function mount(
        $quillId,
        $data = null,
        $classes = '',
        $toolbar = [],
    ) {
        $this->quillId = $quillId;
        $this->data = $data;
        $this->classes = $classes;
        $this->toolbar = $toolbar;
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
        $this->dispatch('livewire-quill:init', [
            'quillId' => $this->quillId,
            'data' => $this->data,
            'classes' => $this->classes,
            'toolbar' => $this->toolbar,
        ]);

        return view('livewire-quill::livewire.livewire-quill');
    }
}
