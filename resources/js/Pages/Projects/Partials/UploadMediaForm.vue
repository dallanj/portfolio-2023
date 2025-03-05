<script setup>
import { ref } from 'vue';

// File Management
import { useFileList } from '@/composables/dragndrop/useFileList.vue';
const { files, addFiles, removeFile, removeAllFiles } = useFileList();

const onInputChange = (e) => {
    addFiles(e.target.files)
    e.target.value = null // reset so that selecting the same file again will still cause it to fire this change
}

// Uploader
import { useFileUploader } from '@/composables/dragndrop/useFileUploader.vue';
const { uploadFiles } = useFileUploader(route('media.store'));

const form = ref({ processing: false });

const uploadMedia = async (files) => {
    form.value.processing = true;
    try {
        await uploadFiles(files);
        form.value.processing = false;
        $toast.success('Your media files have been sucessfully uploaded');
    } catch (error) {
        form.value.processing = false;
        $toast.success('Your media files failed to upload');
    } finally {
        form.value.processing = false;
    }
};
</script>

<template>
<section class="space-y-6">
    <form @submit.prevent="submit">
        <div class="p-6 rounded-lg space-y-4 overflow-hidden h-full">
            <div class="items-center gap-1 sm:gap-4 overflow-hidden h-full">
                <SimpleDragnDrop
                    @files-dropped="addFiles"
                    #default="{ dropZoneActive }">
                    <div class="drop-area">
                        <div class="drop-area__container">
                            <FontAwesomeIcon
                                icon="cloud-arrow-up"
                                class="text-blue-500"
                                size="4x" />
                            <h2>{{ dropZoneActive ? 'Drop your files here' : 'Drag & Drop your files here' }}</h2>
                            <p>or</p>
                            <label for="file-input">
                                <SimpleButton :size="'small'">
                                    <span class="text-center w-full">Browse</span>
                                    <input type="file" id="file-input" multiple @change="onInputChange" />
                                </SimpleButton>
                            </label>
                        </div>
                        <div
                            v-if="files.length > 0"
                            class="flex justify-between">
                            <SimpleButton 
                                :class="{ 'opacity-25': form.processing }" 
                                :disabled="form.processing"
                                icon="file-arrow-up"
                                @click.prevent="uploadMedia(files)">
                                Upload
                            </SimpleButton>
                            <SimpleButton
                                state="plain"
                                :class="{ 'opacity-25': form.processing }" 
                                :disabled="form.processing"
                                @click.prevent="removeAllFiles">
                                Clear List
                            </SimpleButton>  
                        </div>
                        <div v-if="files.length" class="w-full h-fit overflow-hidden">
                            <ul class="drop-area__file-list overflow-scroll max-h-[24.5rem]">
                                <SimpleFilePreview
                                    v-for="file of files"
                                    :key="file.id"
                                    :file="file"
                                    tag="li"
                                    @remove="removeFile" />
                            </ul>
                        </div>
                    </div>
                </SimpleDragnDrop>
            </div>
        </div>
    </form>

    <template>
        <div class="flex gap-x-4">
            <SimpleButton state="secondary" @click="$emit('close')">Cancel</SimpleButton>
            <SimpleButton
                :icon="saving ? 'spinner' : ''"
                :icon-spin="saving"
                @click="submit">
                Create
            </SimpleButton>
        </div>
    </template>
</section>
</template>
    