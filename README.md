# Livewire Quill

[![Latest Version on Packagist](https://img.shields.io/packagist/v/joelwmale/livewire-quill.svg?style=flat-square)](https://packagist.org/packages/joelwmale/livewire-quill)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/joelwmale/livewire-quill/tests.yml?branch=master&label=Tests)](https://github.com/joelwmale/livewire-quill/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/joelwmale/livewire-quill.svg?style=flat-square)](https://packagist.org/packages/joelwmale/livewire-quill)
[![License](https://poser.pugx.org/joelwmale/livewire-quill/license.svg)](https://packagist.org/packages/joelwmale/livewire-quill)

---

This package adds an easy to use Livewire component to your application, which will create and largely manage a [Quill]([Quill](https://quilljs.com/)) editor for you.

It supports image uploads out of the box with zero work from you.

## Updating from v4.x to v5.x

If you're updating from v4.x to v5.x, please note you'll need to force publish the assets again:

```bash
php artisan vendor:publish --tag=livewire-quill:assets --force
```
This is due to the fact that the underlying Quill library has been updated to v2.x, and without force, the assets will not be updated.

## Livewire v2.x Support

v2.0 of this package only supports Livewire v3.x. If you're still using Livewire 2.x, please use [v1.0](https://github.com/joelwmale/livewire-quill/tree/v1.0.0) of this package.

## Installation

You can install the package via composer:

```bash
composer require joelwmale/livewire-quill
```

Once installed, publish the assets:

```bash
php artisan vendor:publish --tag=livewire-quill:assets
```

### Config (optional)

After you've installed the package, you can optionally publish the config to change any defaults:

```bash
php artisan vendor:publish --tag=livewire-quill:config
```

This is the contents of the published config file:

```php
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
    'editor_toolbar' => env('LIVEWIRE_QUILL_EDITOR_TOOLBAR', [
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
```

## Usage

Use it in any Livewire component like so:

```php
@livewire('livewire-quill', [
    'quillId' => 'customQuillId',
    'data' => $content,
    'placeholder' => 'Type something...',
    'classes' => 'bg-white text-primary', // optional classes that can be added to the editor, that are added for this instance only
    'toolbar' => [
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
    ],
    'mergeToolbar' => true, // optional, if you want to merge the toolbar with the default toolbar configuration
])
```

On your Livewire component, add the following:

```php
use Joelwmale\LivewireQuill\Traits\HasQuillEditor;

class SomeLivewireComponent extends Component
{
    use HasQuillEditor;

    public function contentChanged($editorId, $content)
    {
        // $editorId is the id use when you initiated the livewire component
        // $content is the raw text editor content

        // save to the local variable...
        $this->content = $content;
    }
}
```

## Configuration

### QuillId

A div is created with this id, this allows for easy use of multiple quill instances on the same page.

### Data

This is the initial value of the text editor (i.e: a previous saved version of the text editor)

### Placeholder

The placeholder text for the editor

### Classes

Any custom classes you wish to add to the base editor class.

Note: for any customisation, we recommend using CSS to make changes. You can always edit a specific Quill instance by referring to the #quillId variable.

### Toolbar

An array of arrays to manage and create a toolbar for Quill to use

### MergeToolbar

If you want to merge the toolbar with the default toolbar configuration (i.e the one set in the config file) then set this to true.

If you set this to false, the toolbar will be replaced with the one you provide.

## Initialising Livewire Quill Manually

If you wish to initialise Livewire Quill manually (i.e after you show a modal) you can dispatch an event to the window:

### Javascript:

```javascript
dispatchEvent('livewire-quill:init')
```

### Livewire:

```php
$this->dispatchBrowserEvent('livewire-quill:init');
```

## Testing

```bash
composer test
```

## Credits

- [joelwmale](https://github.com/joelwmale)
- [All Contributors](../../contributors)

## Licence

Copyright © Joel Male

Livewire Quill is open-sourced software licensed under the [MIT license](LICENSE.md).
