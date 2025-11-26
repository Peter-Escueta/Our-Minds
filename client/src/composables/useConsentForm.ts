import { ref } from 'vue'
import { useApi } from '@/composables/useApi'
import type { Child, Therapy } from '@/types/child'
import { useAuth } from '@/composables/useAuth'
import { formatDate } from '@/utils/date'
import { toast } from 'vue-sonner'

export function useConsentForm() {
  const { isLoading } = useAuth()
  const { api, handleApiError } = useApi()
  const API_CHILDREN_ENDPOINT = import.meta.env.VITE_API_CHILDREN_ENDPOINT
  const hasOccupationalTherapy = ref(false)
  const hasPhysicalTherapy = ref(false)
  const hasBehavioralTherapy = ref(false)
  const hasSpeechTherapy = ref(false)


  const formData = ref<Partial<Child>>({
    surname: '',
    first_name: '',
    middle_name: '',
    educational_placement: '',
    is_initial_assessment: false,
    is_follow_up: false,
    address: '',
    email: '',
    date_of_birth: '',
    date_of_assessment: '',
    age_at_consult: '',
    gender: undefined,
    siblings: '',
    mother_name: '',
    mother_occupation: '',
    mother_contact: '',
    father_name: '',
    father_occupation: '',
    father_contact: '',
    medical_diagnosis: '',
    referring_doctor: '',
    last_assessment_date: '',
    follow_up_date: '',
    therapies: [] as Therapy[],
    school: '',
    grade: '',
    placement: '',
    year: undefined,
    reason: '',
  })

const resetForm = () => {
  Object.keys(formData.value).forEach((key) => {
    // @ts-expect-error could be empty
    formData.value[key] =  typeof formData.value[key] === 'boolean' ? false : ''
  })

  toast('Form has been reset', {
    description: 'All fields have been cleared',
  })
}

  const submitForm = async () => {
    try {
      isLoading.value = true
      const toastId = toast('Saving child information...', {
        description: 'Please wait...',
        duration: Infinity,
      })

      const payload = {
        ...formData.value,
        date_of_birth: formatDate(formData.value.date_of_birth!),
        date_of_assessment: formatDate(formData.value.date_of_assessment!),
        last_assessment_date: formData.value.last_assessment_date
          ? formatDate(formData.value.last_assessment_date)
          : undefined,
        follow_up_date: formData.value.follow_up_date
          ? formatDate(formData.value.follow_up_date)
          : undefined,
      }

      await api.post(`${API_CHILDREN_ENDPOINT}`, payload)

      toast.success('Child information saved successfully', {
        description: 'Record has been created.',
        id: toastId,
      })

      resetForm()
    } catch (error) {
      handleApiError(error, 'Failed to submit consent form')
    } finally {
      isLoading.value = false
      toast.dismiss()
    }
  }

  return { formData, isLoading, submitForm, resetForm, hasOccupationalTherapy, hasPhysicalTherapy, hasBehavioralTherapy, hasSpeechTherapy }
}
