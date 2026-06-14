<div wire:ignore>
    <div x-data="{
        uploading: false,
        progress: 0,
        error: '',
        success: false,
        fileName: '',
        init() {
            this.fileName = {{ json_encode($get('file_name') ?? '') }};
            this.success = !!{{ json_encode($get('path') ?? '') }};
        },
        handleUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.error = '';
            this.uploading = true;
            this.progress = 0;
            this.fileName = file.name;
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name=csrf-token]').content);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('api.media.upload') }}');
            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    this.progress = Math.round((e.loaded / e.total) * 100);
                }
            };
            xhr.onload = () => {
                this.uploading = false;
                if (xhr.status >= 200 && xhr.status < 300) {
                    const data = JSON.parse(xhr.responseText);
                    @this.set('path', data.path);
                    @this.set('name', data.name);
                    @this.set('mime_type', data.mime_type);
                    @this.set('file_name', data.name + '.' + data.path.split('.').pop());
                    @this.set('disk', 'public');
                    @this.set('human_size', formatBytes(data.size));
                    @this.set('size', data.size);
                    this.success = true;
                } else {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        this.error = data.error || 'Upload failed';
                    } catch {
                        this.error = 'Upload failed (status ' + xhr.status + ')';
                    }
                }
            };
            xhr.onerror = () => {
                this.uploading = false;
                this.error = 'Network error - file may be too large for server/proxy';
            };
            xhr.send(formData);
        },
        formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }
    }">
        {{-- Current file display --}}
        <div x-show="success && !uploading && !error" class="mb-3">
            <div class="flex items-center gap-2 p-3 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                <x-heroicon-o-check-circle class="w-5 h-5 text-success-600" />
                <span x-text="fileName" class="text-sm font-medium truncate"></span>
                <button type="button" @click="
                    success = false;
                    fileName = '';
                    @this.set('path', null);
                    @this.set('name', null);
                    @this.set('mime_type', null);
                    @this.set('file_name', null);
                    @this.set('disk', null);
                    @this.set('human_size', null);
                    @this.set('size', null);
                    $refs.fileInput.value = '';
                " class="ml-auto text-sm text-danger-600 hover:text-danger-700">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Upload progress --}}
        <div x-show="uploading" class="mb-3">
            <div class="flex items-center gap-2 p-3 rounded-lg bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800">
                <x-heroicon-o-arrow-up-tray class="w-5 h-5 text-primary-600 animate-pulse" />
                <span class="text-sm text-primary-700 dark:text-primary-300">Uploading <span x-text="fileName"></span>...</span>
                <span class="ml-auto text-sm font-medium text-primary-600" x-text="progress + '%'"></span>
            </div>
            <div class="mt-2 h-2 rounded-full bg-gray-200 dark:bg-white/10 overflow-hidden">
                <div class="h-full rounded-full bg-primary-500 transition-all duration-300" :style="'width:' + progress + '%'"></div>
            </div>
        </div>

        {{-- Error --}}
        <div x-show="error" class="mb-3">
            <div class="flex items-center gap-2 p-3 rounded-lg bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-danger-600" />
                <span x-text="error" class="text-sm text-danger-700 dark:text-danger-300"></span>
            </div>
        </div>

        {{-- Drop zone / file input --}}
        <div x-show="!uploading" class="relative">
            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
                   :class="success ? 'border-success-300 bg-success-50/50 dark:border-success-800 dark:bg-success-900/10' : 'border-gray-300 dark:border-white/20 bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10'">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <x-heroicon-o-cloud-arrow-up class="w-8 h-8 mb-2 text-gray-400" />
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-semibold">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Max size: {{ \App\Models\Setting::get('media_max_size', 50) }}MB
                    </p>
                </div>
                <input x-ref="fileInput" type="file" class="hidden"
                       accept="{{ implode(',', \App\Models\Setting::get('media_allowed_types', '') ? array_map('trim', explode(',', \App\Models\Setting::get('media_allowed_types', ''))) : '*') }}"
                       @change="handleUpload($event)" />
            </label>
        </div>
    </div>
</div>
