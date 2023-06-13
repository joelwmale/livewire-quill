<div wire:ignore>
  <div name="{{ $quillId }}"
       id="{{ $quillId }}"
       class="{{ $classes }}"></div>

  <link href="/third-party/quill/quill.snow.css"
        rel="stylesheet">
  <script src="/third-party/quill/quill.js"></script>

  <script>
    var toolbar = JSON.parse(JSON.stringify(@JSON($toolbar)));
    var quillContainer = null;

    document.addEventListener('livewire:load', function() {
      var content = null;

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

      content = new Quill("#{{ $quillId }}", {
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
        @this.emit('contentChanged', '{{ $quillId }}', content.root.innerHTML)
      });
    });
  </script>
</div>
