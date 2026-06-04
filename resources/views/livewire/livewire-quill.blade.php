<div wire:ignore>
    <div
        id="{{ $quillId }}"
        class="{{ $classes }} {{ config('livewire-quill.editor_classes') }} livewire-quill"
        name="{{ $quillId }}"
        wire:key="quill-{{ $quillId }}"
    ></div>

    @assets
    <link
        href="/vendor/livewire-quill/quill.snow.min.css"
        rel="stylesheet"
    >
    <script src="/vendor/livewire-quill/quill.js"></script>
    @endassets

    @script
    <script>
        let quillContainer = null;

        function initQuill(id, data, placeholder, toolbar, lazy) {
            var content = null;
            var init = true;

            function selectLocalImage() {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.click();

                // Listen upload local image and save to server
                input.onchange = () => {
                    const file = input.files[0];

                    // file type is only image.
                    if (/^image\//.test(file.type)) {
                        imageHandler(file);
                    } else {
                        alert('You can only upload images.');
                    }
                };
            }

            function imageHandler(image) {
                var uploadedImagesBefore = @this.quillUploadedImages;

                @this.uploadMultiple('quillImages', [image], (uploadedFilename) => {
                    // now get images after upload
                    var uploadedImagesAfterUpload = @this.quillUploadedImages;

                    var imageName = uploadedFilename;
                    var imageUrl = null;

                    for (var key in uploadedImagesAfterUpload) {
                        if (uploadedImagesAfterUpload.hasOwnProperty(key)) {
                            imageUrl = uploadedImagesAfterUpload[key];
                        }
                    }

                    if (imageUrl) {
                        imageUrl = '/storage/' + imageUrl;
                    }

                    insertToEditor(imageUrl, content);
                });
            }

            function insertToEditor(url, editor) {
                const range = editor.getSelection();
                editor.insertEmbed(range.index, 'image', url);
            }

            content = new Quill(`#${id}`, {
                modules: {
                    toolbar: toolbar,
                },
                placeholder: placeholder,
                theme: "snow",
            });

            content.getModule('toolbar').addHandler('image', () => {
                selectLocalImage();
            });

            content.on("text-change", (delta, oldDelta, source) => {
                if (source === "user") {
                    let currrentContents = content.getContents();
                    let diff = currrentContents.diff(oldDelta);
                    let imageDeleted = false;
                    try {
                        // loop through diff.ops to find image
                        diff.ops.forEach((op) => {
                            if (op.hasOwnProperty('insert')) {
                                if (op.insert.hasOwnProperty('image')) {
                                    // get image url
                                    var imageUrl = op.insert.image;

                                    if (imageUrl) {
                                        @this.deleteImage(imageUrl);
                                        imageDeleted = true;
                                    }
                                }
                            }
                        });
                    } catch (_error) {

                    }

                    // in lazy mode, sync content immediately when an image is deleted
                    // so the parent model doesn't retain a broken image reference
                    if (lazy && imageDeleted) {
                        Livewire.dispatch('contentChanged', {
                            editorId: content.container.id,
                            content: content.root.innerHTML
                        });
                    }
                }
            });

            content.root.innerHTML = data;

            // on content change
            if (lazy) {
                var initialContent = content.root.innerHTML;
                content.container.addEventListener('focusout', function(e) {
                    // ignore focus moving within the editor (e.g. toolbar button clicks)
                    if (content.container.contains(e.relatedTarget)) {
                        return;
                    }
                    // only dispatch if content actually changed
                    if (content.root.innerHTML === initialContent) {
                        return;
                    }
                    Livewire.dispatch('contentChanged', {
                        editorId: content.container.id,
                        content: content.root.innerHTML
                    });
                });
            } else {
                content.on("text-change", function(delta, oldDelta, source) {
                    if (init) {
                        return;
                    }

                    // debounce it
                    clearTimeout(quillContainer);

                    // set a timeout to see if the user is still typing
                    quillContainer = setTimeout(function() {
                        // set the content to the model
                        Livewire.dispatch('contentChanged', {
                            editorId: content.container.id,
                            content: content.root.innerHTML
                        })
                    }, 500);
                });
            }

            init = false;
        }

        document.addEventListener('livewire-quill:init', (event) => {
            var event = event.detail[0];

            var quillContainer = document.getElementById(event.quillId);

            if (!quillContainer.dataset.initialized) {
                initQuill(event.quillId, event.data, event.placeholder, event.toolbar, event.lazy);
                quillContainer.dataset.initialized = true;
            }
        });
    </script>
    @endscript
</div>
