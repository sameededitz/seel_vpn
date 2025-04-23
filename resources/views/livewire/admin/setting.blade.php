<div>
    <div class="row gy-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Set Settings</h6>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="py-2">
                            @foreach ($errors->all() as $error)
                                <x-alert type="danger" :message="$error" />
                            @endforeach
                        </div>
                    @endif
                    <form wire:submit.prevent="save">
                        <div class="row gy-3">
                            <div class="col-12" wire:ignore>
                                <label class="form-label" for="privacy_policy">Privacy Policy</label>
                                <textarea name="privacy_policy" id="myeditorinstance" wire:model="privacy_policy" class="form-control tinymce-editor"></textarea>
                            </div>
                            <div class="col-12" wire:ignore>
                                <label class="form-label" for="terms_of_service">Terms of Service</label>
                                <textarea id="tosEditor" name="tos" wire:model="tos" class="form-control tinymce-editor"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit"
                                    class="btn btn-outline-info d-flex align-items-center justify-content-center">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- card end -->
        </div>
    </div>
</div>
@assets
    <script src="https://cdn.tiny.cloud/1/profov2dlbtwaoggjfvbncp77rnjhgyfnl3c2hx3kzpmhif1/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
@endassets
@script
    <script>
        tinymce.init({
            selector: 'textarea.tinymce-editor', // Replace this CSS selector to match the placeholder element for TinyMCE
            plugins: 'code table lists',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table',
            setup: function(editor) {
                editor.on('blur', function() {
                    let content = editor.getContent();
                    let livewireField = editor.getElement().getAttribute('wire:model');
                    @this.set(livewireField, content);
                });
                editor.on('change', function() {
                    let content = editor.getContent();
                    let livewireField = editor.getElement().getAttribute('wire:model');
                    @this.set(livewireField, content);
                });
            },
        });

        $wire.on('sweetAlert', (event) => {
            Swal.fire({
                title: event.title,
                text: event.message,
                icon: event.type,
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
@endscript
