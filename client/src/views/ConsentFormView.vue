<script setup lang="ts">
import { ref } from 'vue'
import { toast } from 'vue-sonner'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import axios from 'axios'
import ConsultantHeader from '@/components/ConsultantHeader.vue'

const formData = ref({
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
  gender: '',
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
  occupational_therapy: false,
  physical_therapy: false,
  behavioral_therapy: false,
  speech_therapy: false,
  school: '',
  grade: '',
  placement: '',
  year: ''
})

const genderOptions = [
  { value: 'male', label: 'Male' },
  { value: 'female', label: 'Female' },
  { value: 'other', label: 'Other' }
]

const gradeOptions = [
  'Pre-K', 'Kindergarten', '1st', '2nd', '3rd', '4th', 
  '5th', '6th', '7th', '8th', '9th', '10th', '11th', '12th'
]

const placementOptions = [
  'Regular Class', 'Special Education Class', 
  'Inclusion Program', 'Home School', 'Other'
]

const isLoading = ref(false)

const handleSubmit = async () => {
  try {
    isLoading.value = true
    
    const toastId = toast('Saving child information', {
      description: 'Please wait while we process your request',
      duration: Infinity,
      action: {
        label: 'Cancel',
        onClick: () => {
          toast.dismiss(toastId)
          isLoading.value = false
        },
      },
    })

    const token = localStorage.getItem('auth_token')
    if (!token) {
      throw new Error('Authentication token not found. Please login again.')
    }

    const formatDate = (dateString: string) => {
      if (!dateString) return null
      const date = new Date(dateString)
      return date.toISOString().split('T')[0]
    }

    const payload = {
      ...formData.value,
      date_of_birth: formatDate(formData.value.date_of_birth),
      date_of_assessment: formatDate(formData.value.date_of_assessment),
      last_assessment_date: formatDate(formData.value.last_assessment_date),
      follow_up_date: formatDate(formData.value.follow_up_date)
    }

    const response = await axios.post('http://localhost:8000/api/children', payload, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })

    toast.success('Successfully saved child information', {
      description: 'The record has been created in the system',
      id: toastId,
      action: {
        label: 'View Details',
        onClick: () => {
          console.log('Navigating to child details')
        },
      },
    })

    resetForm()
  } catch (error: any) {
    console.error('Error submitting form:', error)
    
    let errorMessage = 'Failed to save child information'
    if (error.response?.status === 401) {
      errorMessage = 'Session expired. Please login again.'
    } else if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    } else if (error.response?.data?.errors) {
      errorMessage = Object.values(error.response.data.errors).join('\n')
    }

    toast.error('Submission failed', {
      description: errorMessage,
      action: {
        label: 'Try Again',
        onClick: () => handleSubmit(),
      },
    })
  } finally {
    isLoading.value = false
  }
}

const resetForm = () => {
  formData.value = {
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
    gender: '',
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
    occupational_therapy: false,
    physical_therapy: false,
    behavioral_therapy: false,
    speech_therapy: false,
    school: '',
    grade: '',
    placement: '',
    year: ''
  }
  
  toast('Form has been reset', {
    description: 'All fields have been cleared',
  })
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <!-- Header -->
   <ConsultantHeader/>

    <!-- Main Content with Borders -->
    <div class="flex-1 border-l border-r border-gray-200 mx-auto w-full max-w-[90rem] bg-white">
      <main class="container mx-auto font-display py-8 px-20 h-full">
        <Card class="border-0 shadow-none">
          <CardHeader>
            <CardTitle class="text-center text-2xl">Child Information Form</CardTitle>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="handleSubmit" class="space-y-6">
              <!-- Personal Information Section -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                  <Label for="surname">Surname</Label>
                  <Input id="surname" v-model="formData.surname" required />
                </div>
                <div class="space-y-2">
                  <Label for="first_name">First Name</Label>
                  <Input id="first_name" v-model="formData.first_name" required />
                </div>
                <div class="space-y-2">
                  <Label for="middle_name">Middle Name</Label>
                  <Input id="middle_name" v-model="formData.middle_name" />
                </div>
              </div>

              <!-- Educational Placement -->
              <div class="space-y-4">
                <Label>Current Educational Placement</Label>
                <Input v-model="formData.educational_placement" placeholder="School/Program name" />
                
                <div class="flex items-center space-x-4">
                  <div class="flex items-center space-x-2">
                    <Checkbox 
                      id="is_initial_assessment" 
                      v-model="formData.is_initial_assessment" 
                    />
                    <Label for="is_initial_assessment">Initial Assessment</Label>
                  </div>
                  <div class="flex items-center space-x-2">
                    <Checkbox 
                      id="is_follow_up" 
                      v-model="formData.is_follow_up" 
                    />
                    <Label for="is_follow_up">Follow-Up</Label>
                  </div>
                </div>
              </div>

              <!-- Contact Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <Label for="address">Address</Label>
                  <Input id="address" v-model="formData.address" />
                </div>
                <div class="space-y-2">
                  <Label for="email">Email Address</Label>
                  <Input id="email" v-model="formData.email" type="email" />
                </div>
              </div>

              <!-- Dates and Demographics -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="space-y-2">
                  <Label for="date_of_birth">Date of Birth</Label>
                  <Input id="date_of_birth" v-model="formData.date_of_birth" type="date" />
                </div>
                <div class="space-y-2">
                  <Label for="date_of_assessment">Date of Assessment</Label>
                  <Input id="date_of_assessment" v-model="formData.date_of_assessment" type="date" />
                </div>
                <div class="space-y-2">
                  <Label for="age_at_consult">Age at Consult</Label>
                  <Input id="age_at_consult" v-model="formData.age_at_consult" placeholder="e.g. 5 years" />
                </div>
                <div class="space-y-2">
                  <Label for="gender">Gender</Label>
                  <Select v-model="formData.gender">
                    <SelectTrigger>
                      <SelectValue placeholder="Select gender" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem 
                        v-for="option in genderOptions" 
                        :key="option.value" 
                        :value="option.value"
                      >
                        {{ option.label }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <!-- Siblings -->
              <div class="space-y-2">
                <Label for="siblings">Siblings (names and ages)</Label>
                <Input id="siblings" v-model="formData.siblings" placeholder="e.g. John (8), Mary (5)" />
              </div>

              <!-- Parent Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <Card>
                  <CardHeader class="pb-2">
                    <CardTitle class="text-lg">Mother's Information</CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div class="space-y-2">
                      <Label for="mother_name">Name</Label>
                      <Input id="mother_name" v-model="formData.mother_name" />
                    </div>
                    <div class="space-y-2">
                      <Label for="mother_occupation">Occupation</Label>
                      <Input id="mother_occupation" v-model="formData.mother_occupation" />
                    </div>
                    <div class="space-y-2">
                      <Label for="mother_contact">Contact Number</Label>
                      <Input id="mother_contact" v-model="formData.mother_contact" type="tel" />
                    </div>
                  </CardContent>
                </Card>

                <Card>
                  <CardHeader class="pb-2">
                    <CardTitle class="text-lg">Father's Information</CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div class="space-y-2">
                      <Label for="father_name">Name</Label>
                      <Input id="father_name" v-model="formData.father_name" />
                    </div>
                    <div class="space-y-2">
                      <Label for="father_occupation">Occupation</Label>
                      <Input id="father_occupation" v-model="formData.father_occupation" />
                    </div>
                    <div class="space-y-2">
                      <Label for="father_contact">Contact Number</Label>
                      <Input id="father_contact" v-model="formData.father_contact" type="tel" />
                    </div>
                  </CardContent>
                </Card>
              </div>

              <!-- Medical Information -->
              <div class="space-y-4">
                <div class="space-y-2">
                  <Label for="medical_diagnosis">Medical Diagnosis/Impression</Label>
                  <Input id="medical_diagnosis" v-model="formData.medical_diagnosis" />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <Label for="referring_doctor">Referring Doctor</Label>
                    <Input id="referring_doctor" v-model="formData.referring_doctor" />
                  </div>
                  <div class="space-y-2">
                    <Label for="last_assessment_date">Last Assessment Date</Label>
                    <Input id="last_assessment_date" v-model="formData.last_assessment_date" type="date" />
                  </div>
                </div>
                
                <div class="space-y-2">
                  <Label for="follow_up_date">Follow-Up Date</Label>
                  <Input id="follow_up_date" v-model="formData.follow_up_date" type="date" />
                </div>
              </div>

              <!-- Therapy Services -->
              <div class="space-y-2">
                <Label>Therapy Services</Label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                  <div class="flex items-center space-x-2">
                    <Checkbox 
                      id="occupational_therapy" 
                      v-model="formData.occupational_therapy" 
                    />
                    <Label for="occupational_therapy">Occupational Therapy</Label>
                  </div>
                  <div class="flex items-center space-x-2">
                    <Checkbox 
                      id="physical_therapy" 
                      v-model="formData.physical_therapy" 
                    />
                    <Label for="physical_therapy">Physical Therapy</Label>
                  </div>
                  <div class="flex items-center space-x-2">
                    <Checkbox 
                      id="behavioral_therapy" 
                      v-model="formData.behavioral_therapy" 
                    />
                    <Label for="behavioral_therapy">Behavioral Therapy</Label>
                  </div>
                  <div class="flex items-center space-x-2">
                    <Checkbox 
                      id="speech_therapy" 
                      v-model="formData.speech_therapy" 
                    />
                    <Label for="speech_therapy">Speech Therapy</Label>
                  </div>
                </div>
              </div>

              <!-- School Information -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="space-y-2">
                  <Label for="school">School</Label>
                  <Input id="school" v-model="formData.school" />
                </div>
                <div class="space-y-2">
                  <Label for="grade">Grade</Label>
                  <Select v-model="formData.grade">
                    <SelectTrigger>
                      <SelectValue placeholder="Select grade" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem 
                        v-for="grade in gradeOptions" 
                        :key="grade" 
                        :value="grade"
                      >
                        {{ grade }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div class="space-y-2">
                  <Label for="placement">Placement</Label>
                  <Select v-model="formData.placement">
                    <SelectTrigger>
                      <SelectValue placeholder="Select placement" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem 
                        v-for="placement in placementOptions" 
                        :key="placement" 
                        :value="placement"
                      >
                        {{ placement }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div class="space-y-2">
                  <Label for="year">Year</Label>
                  <Input id="year" v-model="formData.year" type="number" min="2000" :max="new Date().getFullYear()" />
                </div>
              </div>

              <!-- Form Actions -->
              <div class="flex justify-end space-x-4 pt-6">
                <Button 
                  type="button" 
                  variant="outline" 
                  @click="resetForm"
                  :disabled="isLoading"
                >
                  Reset Form
                </Button>
                <Button 
                  type="submit"
                  :disabled="isLoading"
                >
                  <span v-if="isLoading">Saving...</span>
                  <span v-else>Submit Information</span>
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </main>
    </div>
  </div>
</template>