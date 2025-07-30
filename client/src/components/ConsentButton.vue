<template>
  <button 
    @click="downloadConsent"
    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
  >
    Download Consent Form
  </button>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  childId: {
    type: Number,
    required: true
  }
});

const isLoading = ref(false);

const downloadConsent = async () => {
  try {
    isLoading.value = true;
      const token = localStorage.getItem('auth_token')
    const response = await axios.get(`http://localhost:8000/api/children/${props.childId}/consent`, {
      responseType: 'blob',
         headers: {
        'Authorization': `Bearer ${token}`
      },
    });

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `consent-form-${props.childId}.pdf`);
    document.body.appendChild(link);
    link.click();
    
    // Clean up
    link.parentNode.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error('Error downloading consent form:', error);
    // Handle error (show toast, etc.)
  } finally {
    isLoading.value = false;
  }
};
</script>