<template>
  <button
    @click="downloadEvaluation"
    :disabled="isLoading"
    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center gap-2"
  >
    <span v-if="isLoading">
      <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </span>
    <span v-else>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
      </svg>
    </span>
    Download Evaluation
  </button>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { toast } from 'vue-sonner';

const props = defineProps({
  evaluationId: {
    type: Number,
    required: true
  }
});

const isLoading = ref(false);

const downloadEvaluation = async () => {
  try {
    isLoading.value = true;
    const token = localStorage.getItem('auth_token');
    
    const response = await axios.get(
      `http://localhost:8000/api/evaluations/${props.evaluationId}/pdf`, 
      {
        responseType: 'blob',
        headers: {
          'Authorization': `Bearer ${token}`
        },
      }
    );

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `evaluation-${props.evaluationId}.pdf`);
    document.body.appendChild(link);
    link.click();
    
    // Clean up
    link.parentNode.removeChild(link);
    window.URL.revokeObjectURL(url);
    
  } catch (error) {
    console.error('Error downloading evaluation:', error);
    toast.error('Failed to download evaluation PDF');
  } finally {
    isLoading.value = false;
  }
};
</script>