<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
	file: { type: Object, required: true },
	tag: { type: String, default: 'li' },
})

const emit = defineEmits(['remove'])

const fileTypes = {
	MP3: 'text-blue-500',
	WAV: 'text-blue-500',
	MP4: 'text-green-500',
	AVI: 'text-green-500',
	WEBM: 'text-green-500',
	PNG: 'text-red-500',
	JPG: 'text-red-500',
	JPEG: 'text-red-500',
	PDF: 'text-orange-500',
	DOC: 'text-yellow-500',
	DOCX: 'text-yellow-500',
	// Add more file types and colors as needed
};

// Function to get the file extension from MIME type
function getFileExtension(mimeType) {
	return mimeType.split('/').pop().toUpperCase();
}

function getFileTypeDisplay(fileType) {
	return Object.keys(fileTypes).includes(fileType) ? fileType : '';
}

// Function to determine the color based on file type
function getColorByFileType(fileType) {
	const fileTypeColors = {
		MP3: 'text-blue-500',
		WAV: 'text-blue-500',
		MP4: 'text-green-500',
		AVI: 'text-green-500',
		WEBM: 'text-green-500',
		PNG: 'text-red-500',
		JPG: 'text-red-500',
		JPEG: 'text-red-500',
		PDF: 'text-orange-500',
		DOC: 'text-yellow-500',
		DOCX: 'text-yellow-500',
		// Add more file types and colors as needed
	};

  	return fileTypeColors[fileType] || 'text-gray-500'; // Default color if file type is not mapped
}

// Extract the file extension and determine the color
const fileExtension = computed(() => getFileExtension(props.file.type));
const fileColor = computed(() => getColorByFileType(fileExtension.value));
const fileType = computed(() => getFileTypeDisplay(fileExtension.value));

const statusIcon = computed(() => {
	if (props.file.status === 'loading') return 'spinner';
	if (props.file.status === 'success') return 'check';
	if (props.file.status === 'error') return 'exclamation';
	return 'times';
});

const buttonClass = computed(() => {
    if (props.file.status === null) return 'bg-red-500';
    if (props.file.status === 'loading') return 'bg-blue-500';
    if (props.file.status === 'success') return 'bg-green-500';
    if (props.file.status === 'error') return 'bg-yellow-500';
    return 'bg-gray-500'; // Default color
});

const spinnerClass = computed(() => ({
  'fa-spin': props.file.status === 'loading',
}));

const animateIn = ref(false);
const hover = ref(false);

const hoverIcon = computed(() => {
	if (props.file.status === 'success' && hover.value) {
		return 'folder';
	}
	return statusIcon.value;
});

watch(() => props.file, () => {
	animateIn.value = true;
	setTimeout(() => {
		animateIn.value = false;
	}, 500); // duration of the animation
}, { immediate: true });

function handleClick() {
	if (props.file.status === 'success') {
		router.get(`/media/${props.file.hash}`);
	} else {
		emit('remove', props.file);
	}
}

function onMouseEnter() {
	if (props.file.status === 'success') {
		hover.value = true;
	}
}

function onMouseLeave() {
	hover.value = false;
}

const previewUrl = ref(null);

// Function to generate base64 preview
function generatePreview(file) {
	if (!file || !file.file) return;

	const fileType = getFileExtension(file.type);
	console.log(file.type);
	if (['PNG', 'JPG', 'JPEG'].includes(fileType)) {
		const reader = new FileReader();
		reader.onload = () => {
			previewUrl.value = reader.result;
		};
		reader.readAsDataURL(file.file);
	} else {
		previewUrl.value = null;
	}
}

// Watch file changes and generate preview
watch(() => props.file, () => {
	generatePreview(props.file);
}, { immediate: true });

</script>

<template>
	<component 
		:is="tag"
		class="relative"
		:class="[
			{ 'slide-out-to-left': file.removing },
			{ 'slide-in-from-left': animateIn },
		]">
		<div class="grid grid-cols-[auto,1fr,40px] items-center text-brand-dark-gray px-2">	
			<img v-if="previewUrl" :src="previewUrl" class="mt-1 w-16 h-16 object-cover rounded-lg shadow-md mr-3" />
			<div v-else class="relative inline-block w-16 mr-3 grid justify-center">		
				<FontAwesomeIcon icon="file" size="3x" class="px-2" :class="fileColor" />
				<span class="absolute bottom-2 left-5 text-white text-xs font-bold">
					{{ fileType }}
				</span>
			</div>

			<div class="grid px-1 pr-3">
				<span v-tooltip="file.file.name" class="truncate">{{ file.file.name }}</span>
				<span class="truncate">{{ file.size }}</span>
			</div>
		</div>
		<button
			v-tooltip.right="file.status === 'success' ? 'View your uploaded file' : ''"
			class="absolute right-4 top-1/2 transform -translate-y-1/2 px-2 ml-auto h-7 w-7 rounded-full"
			:class="[buttonClass, 'transition-transform', { 'hover:scale-110': file.status === 'success' }]"
			@click="handleClick"
			@mouseenter="onMouseEnter"
			@mouseleave="onMouseLeave">
			<FontAwesomeIcon :icon="hoverIcon" :class="spinnerClass" class="ml-auto" />
		</button>
	</component>
</template>
