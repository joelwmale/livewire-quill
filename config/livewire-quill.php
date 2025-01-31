<?php

return [
    /*
     * The base folder to store the images in
     */
    'storage_folder' => env('LIVEWIRE_QUILL_STORAGE_FOLDER', 'images'),

    /*
     * Should the images be stored publically or not
     */
    'store_publically' => env('LIVEWIRE_QUILL_STORE_PUBLICALLY', true),

    /*
     * Should the images be deleted from the server once deleted in the editor
     * or retained for future use (note: the package will never re-use the same image)
     */
    'clean_up_deleted_images' => env('LIVEWIRE_QUILL_CLEAN_UP_DELETED_IMAGES', true),

    /*
     * The base classes to use for all instances of the editor
     */
    'editor_classes' => env('LIVEWIRE_QUILL_EDITOR_CLASSES', 'bg-white'),

    /**
     * The toolbar options to use for all instances of the editor
     */
    'toolbar' => env('LIVEWIRE_QUILL_TOOLBAR', [
        [
            [
                'header' => [1, 2, 3, 4, 5, 6, false],
            ],
        ],
        ['bold', 'italic', 'underline'],
        [
            [
                'list' => 'ordered',
            ],
            [
                'list' => 'bullet',
            ],
        ],
        ['link'],
        ['image'],
    ]),
];
