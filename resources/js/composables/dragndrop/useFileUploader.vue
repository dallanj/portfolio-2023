<script>
import { ref } from 'vue';

export function useFileUploader(initalUrl) {
    const url = ref(initalUrl);

    const uploadFile = async (file, hash = null) => {
        // Set up the request data
        let formData = new FormData()
        if (hash) {
            formData.append('hash', hash);
        }
        formData.append('file', file.file);

        // Track file status and upload file
        file.status = 'loading';

        try {
            const response = await axios.post(url.value, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
        
            // Set file status to success or error
            file.status = response.status === 200 ? 'success' : 'error';
            
            if (response?.data?.media?.hash) {
                file.hash = response.data.media.hash
            }
            return response;
        } catch (error) {
            file.status = 'error';
            throw error;
        }
    };

    const uploadFiles = (files, hash = null) => {
        console.log(files.value);
        return Promise.all(files.value.map((file) => uploadFile(file, hash)))
    };

	return {
		uploadFile,
        uploadFiles,
	};
}
</script>
