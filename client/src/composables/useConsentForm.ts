import { ref } from 'vue'
import { useApi } from '@/composables/useApi'
import type { Child } from '@/types/child'
import { useAuth } from '@/composables/useAuth'
import { formatDate } from '@/utils/date'
import { toast } from 'vue-sonner'
import router from '@/router'

export function useConsentForm() {
  const { isLoading } = useAuth()
  const { api, handleApiError } = useApi()
  const API_CHILDREN_ENDPOINT = import.meta.env.VITE_API_CHILDREN_ENDPOINT

  const initialFormState: Partial<Child> = {
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
    therapies: [],
    school: '',
    grade: '',
    placement: '',
    year: undefined,
    reason: '',
  }

  const formData = ref<Partial<Child>>({ ...initialFormState })

  const resetForm = () => {
    formData.value = JSON.parse(JSON.stringify(initialFormState))

    toast('Form has been reset', {
      description: 'All fields have been cleared',
    })
  }

  const submitForm = async () => {
    try {
      isLoading.value = true


      const payload = {
        ...formData.value,
        date_of_birth: formData.value.date_of_birth ? formatDate(formData.value.date_of_birth) : undefined,
        date_of_assessment: formData.value.date_of_assessment ? formatDate(formData.value.date_of_assessment) : undefined,
        last_assessment_date: formData.value.last_assessment_date
          ? formatDate(formData.value.last_assessment_date)
          : undefined,
        follow_up_date: formData.value.follow_up_date
          ? formatDate(formData.value.follow_up_date)
          : undefined,
        year: formData.value.year ? Number(formData.value.year) : undefined
      }

      await api.post(`${API_CHILDREN_ENDPOINT}`, payload)


      router.push('/children')

      return true
    } catch (error) {
      handleApiError(error, 'Failed to submit consent form')
      return false
    } finally {
      isLoading.value = false
      toast.dismiss()
    }
  }

  return {
    formData,
    isLoading,
    submitForm,
    resetForm
  }
}
