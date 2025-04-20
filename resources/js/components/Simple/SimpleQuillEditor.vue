<script setup>
import { ref, defineEmits, defineModel, onMounted, watch, nextTick } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const modelValue = defineModel({
  type: [Object],
  default: () => ({
    html: '',
    delta: {
      ops: [{ insert: '\n' }]
    }
  })
});

const emit = defineEmits(['update:modelValue']);

const editorRef = ref(null);
const quillInstance = ref(null);
let initialized = false;

onMounted(async () => {
  // Wait for DOM to be ready before Quill init
  await nextTick();

  const editorEl = editorRef.value;

  if (!editorEl) {
    console.error('Editor element not found');
    return;
  }

  const quill = new Quill(editorEl, {
    theme: 'snow',
    placeholder: 'Start typing your resume...',
    modules: {
      toolbar: [
        ['bold', 'italic', 'underline'],
        [{ header: [1, 2, 3, false] }],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link'],
        ['clean']
      ]
    }
  });

  quillInstance.value = quill;

  applyDelta(modelValue.value.delta);
  initialized = true;

  quill.on('text-change', () => {
    const delta = quill.getContents();

    emit('update:modelValue', {
      html: quill.root.innerHTML,
      delta
    });
  });
});

watch(() => modelValue.value.delta, (newDelta) => {
  if (initialized) {
    applyDelta(newDelta);
  }
});

function applyDelta(delta) {
  try {
    const parsedDelta = typeof delta === 'string' ? JSON.parse(delta) : delta;
    if (parsedDelta?.ops?.length > 0) {
      quillInstance.value.setContents(parsedDelta);
    }
  } catch (e) {
    console.warn('Invalid delta in applyDelta:', e);
  }
}
</script>

<template>
  <div>
    <div ref="editorRef" class="quill-editor"></div>
  </div>

      <div class="mt-10">
            <ul>
                <li v-for="delta in modelValue.delta" :key="`delta-${delta}`">
                    {{ delta }}
                </li>
            </ul>
      </div>
    
</template>
  