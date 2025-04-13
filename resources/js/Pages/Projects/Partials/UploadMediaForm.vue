<script setup>
import { ref, computed, onMounted, watch, nextTick, defineExpose } from 'vue';
import { useFileList } from '@/composables/dragndrop/useFileList.vue';
import { useFileUploader } from '@/composables/dragndrop/useFileUploader.vue';
import { storeToRefs } from 'pinia';
import { useProjectsStore } from '@/stores/projects';

const emit = defineEmits(['save-project']);

const { files, addFiles, removeFile, removeAllFiles } = useFileList();

const { active } = storeToRefs(useProjectsStore());

const form = ref({ processing: false });
const media = ref([]); // Store project media files

// Define model binding
const model = defineModel({
    type: Object,
    default: [],
});

const project = computed(() => model.value);
const action = computed(() => `/api/v1/projects/${active.value?.hash}/media`);
const { uploadFiles } = useFileUploader(action);

watch(active, async (obj) => {
    await nextTick();
    if (obj) {
        console.log('watcher',obj);
        media.value = obj.media;
    }
}, {
    deep: true
});

// Fetch media files
onMounted(() => {
    if (model.value.length) {
        media.value = model.value; // Assign project media from API response
    }
});

const onInputChange = (e) => {
    addFiles(e.target.files)
    e.target.value = null // reset so that selecting the same file again will still cause it to fire this change
}

const uploadMedia = async () => {
    form.value.processing = true;
    try {
        await uploadFiles(files, active.value.hash);
        form.value.processing = false;
        $toast.success('Your media files have been sucessfully uploaded');
        // Reload media from API after upload
    } catch (error) {
        console.log(error);
        form.value.processing = false;
        $toast.error('Your media files failed to upload');
    } finally {
        form.value.processing = false;
    }
};

defineExpose({ uploadMedia });

// Fetch updated media after upload
const fetchProjectMedia = async () => {
    try {
        const response = await axios.get(route('project.show', project.value.hash));
        return response.data.media;
    } catch (error) {
        console.error('Failed to fetch media:', error);
        return [];
    }
};

const destroy = (item) => axios.delete(`/api/v1/media/${item.hash}`);
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
                                    <input type="file" id="file-input" accept="image/*" multiple @change="onInputChange" />
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
                                @click.prevent="uploadMedia">
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

    <div v-if="media.length" class="mt-6 grid grid-cols-3 gap-4">
        <div v-for="image in media" :key="image.id" class="relative group">
            <img
                :id="image.hash"
                :src="`/api/v1/media/${image.hash}`"
                :alt="image.filename"
                class="w-full h-40 object-cover rounded-lg shadow-lg" />
            <button
                class="bg-red-500 rounded-full absolute top-0.5 right-0.5 px-0.5"
                @click="destroy(image)">
                <FontAwesomeIcon icon="times" class="fa-fw" />
            </button>
        </div>
    </div>
</section>
</template>
    