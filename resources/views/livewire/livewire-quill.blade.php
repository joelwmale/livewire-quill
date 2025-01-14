<div wire:ignore>
    <div id="{{ $quillId }}" class="{{ $classes }} livewire-quill" name="{{ $quillId }}"></div>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.js"></script>

    <script>
        var toolbar = JSON.parse(JSON.stringify(@JSON($toolbar)));
        var quillContainer = null;

        function initQuill(id) {
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
                theme: "snow",
            });

            content.getModule('toolbar').addHandler('image', () => {
                selectLocalImage();
            });

            content.on("text-change", (delta, oldDelta, source) => {
                if (source === "user") {
                    let currrentContents = content.getContents();
                    let diff = currrentContents.diff(oldDelta);
                    try {
                        // loop through diff.ops to find image
                        diff.ops.forEach((op) => {
                            if (op.hasOwnProperty('insert')) {
                                if (op.insert.hasOwnProperty('image')) {
                                    // get image url
                                    var imageUrl = op.insert.image;

                                    if (imageUrl) {
                                        @this.deleteImage(imageUrl);
                                    }
                                }
                            }
                        });
                    } catch (_error) {

                    }
                }
            });

            content.root.innerHTML = @this.get('data');

            // on content change
            content.on("text-change", function(delta, oldDelta, source) {
                if (init) {
                    return;
                }

                // debounce it
                clearTimeout(quillContainer);

                // set a timeout to see if the user is still typing
                quillContainer = setTimeout(function() {
                    // set the content to the model
                    @this.dispatch('contentChanged', {
                        editorId: content.container.id,
                        content: content.root.innerHTML
                    })
                }, 500);
            });

            init = false;
        }

        document.addEventListener('livewire:navigated', () => {
            setTimeout(() => {
                document.querySelectorAll('.livewire-quill').forEach((element) => {
                    if (!element.dataset.initialized) {
                        initQuill(element.id);
                        element.dataset.initialized = true;
                    }
                });
            }, 800);
        });

        document.addEventListener('livewire-quill:init', () => {
            setTimeout(() => {
                document.querySelectorAll('.livewire-quill').forEach((element) => {
                    console.log('here');
                    if (!element.dataset.initialized) {
                        initQuill(element.id);
                        element.dataset.initialized = true;
                    }
                });
            }, 800);
        });
    </script>
</div>
