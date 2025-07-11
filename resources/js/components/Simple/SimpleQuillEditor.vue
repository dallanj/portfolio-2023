<script setup>
import { ref, defineEmits, defineModel, onMounted, nextTick, onBeforeUnmount } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const modelValue = defineModel({ required: true });
const editorRef = ref(null);
const quillInstance = ref(null);

onMounted(async () => {
    // Wait for DOM to be ready before Quill init
    await nextTick();

    // Editor element reference
    const editorEl = editorRef.value;

    if (!editorEl) {
        console.error('Editor element not found');
        return;
    }

    const quill = new Quill(editorEl, {
        theme: 'snow',
        placeholder: 'Start typing your resume...',
        modules: {
          syntax: true,
          toolbar: '#toolbar-container',
        }
    });

    quillInstance.value = quill;

    // Wait for defineModel to load or else content will not load into the editor
    setTimeout(() => {
        quill.setContents(modelValue.value.delta.ops);
    }, 50);

    // Listen for when user edits content
    quill.on('text-change', () => {
        const delta = quill.getContents();
        const html = quill.getSemanticHTML();
        modelValue.value.delta = delta;
        modelValue.value.html = html;
    });
});

onBeforeUnmount(() => {
  if (quillInstance.value) {
      // Remove event listeners and destroy the instance if needed
      quillInstance.value.off('text-change'); // remove event
      quillInstance.value = null;
  }
});
</script>

<template>
<div class="grid w-full">
    <!-- Toolbar modules -->
    <div id="toolbar-container" class="flex flex-wrap px-3 py-2 border border-gray-300 dark:border-app-header-bb rounded-md focus:ring focus:ring-orange focus:outline-none bg-white dark:bg-app-header-bg text-gray-900 dark:text-gray-100">
        <span class="ql-formats">
            <select class="ql-font"></select>
            <select class="ql-size"></select>
        </span>
        <span class="ql-formats">
            <button class="ql-bold"></button>
            <button class="ql-italic"></button>
            <button class="ql-underline"></button>
            <button class="ql-strike"></button>
        </span>
        <span class="ql-formats">
            <select class="ql-color"></select>
            <select class="ql-background"></select>
        </span>
        <span class="ql-formats">
            <button class="ql-script" value="sub"></button>
            <button class="ql-script" value="super"></button>
        </span>
        <span class="ql-formats">
            <button class="ql-header" value="1"></button>
            <button class="ql-header" value="2"></button>
            <button class="ql-blockquote"></button>
            <button class="ql-code-block"></button>
        </span>
        <span class="ql-formats">
            <button class="ql-list" value="ordered"></button>
            <button class="ql-list" value="bullet"></button>
            <button class="ql-indent" value="-1"></button>
            <button class="ql-indent" value="+1"></button>
        </span>
        <span class="ql-formats">
            <button class="ql-direction" value="rtl"></button>
            <select class="ql-align"></select>
        </span>
        <span class="ql-formats">
            <button class="ql-link"></button>
            <button class="ql-image"></button>
            <button class="ql-video"></button>
            <button class="ql-formula"></button>
        </span>
        <span class="ql-formats">
            <button class="ql-clean"></button>
        </span>
    </div>
    <!-- Text Editor -->
    <div id="editor" ref="editorRef" class="w-full px-3 py-2 border border-gray-300 dark:border-app-header-bb rounded-md focus:ring focus:ring-orange focus:outline-none bg-white dark:bg-app-header-bg text-gray-900 dark:text-gray-100"></div>
</div>

<!-- Testing Delta and HTML -->
<div class="mt-20">
  Delta: {{ modelValue.delta }}
</div>
<div v-if="quillInstance" class="mt-10">
    Html: {{ JSON.stringify(quillInstance.getSemanticHTML()) }}
</div>
    
</template>
  