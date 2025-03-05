<script>
import { ref } from 'vue';
import { useMediaStore } from '@/stores/media';

export function useFileList() {
    const files = ref([]);

	const { shortenFileSize } = useMediaStore();

	const addFiles = (newFiles, folderId = null) => {
		let newUploadableFiles = [...newFiles]
		  	.map((file) => new UploadableFile(file, folderId))
		  	.filter((file) => !fileExists(file.id));
		files.value = files.value.concat(newUploadableFiles);
	};

	const fileExists = (otherId) => {
		return files.value.some(({ id }) => id === otherId);
	};

	const removeFile = (file) => {
		// Mark the file as being removed
		file.removing = true;
  
		// Wait for the animation to complete before actually removing the file from the list
		setTimeout(() => {
			const index = files.value.indexOf(file);
			if (index > -1) files.value.splice(index, 1);
		}, 500);
	};

	const removeAllFiles = _ => {
		files.value.forEach((file) => {
			file.removing = true;
		});
		setTimeout(() => {
			files.value = [];
		}, 500);
	};

	class UploadableFile {
		constructor(file, folderId = null) {
			this.file = file
			this.id = `${file.name}-${file.size}-${file.lastModified}-${file.type}`
			this.url = URL.createObjectURL(file)
			this.size = shortenFileSize(file.size, 1000);
			this.type = file.type;
			this.status = null;
			this.folder_id = folderId;
			this.hash = null;
		}
	};

    return {
        files,
        addFiles,
        removeFile,
        removeAllFiles
    };
};
</script>
