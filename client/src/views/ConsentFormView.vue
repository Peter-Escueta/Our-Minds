<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Textarea } from '@/components/ui/textarea'
import { TherapyType, TherapyTypeLabels, type Therapy } from '@/types/child'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { genderOptions, gradeOptions, placementOptions } from '@/data/childFormOptions'
import { useConsentForm } from '@/composables/useConsentForm'
import { useAuth } from '@/composables/useAuth'

const { formData, submitForm } = useConsentForm()
const { isLoading } = useAuth()

const therapyDetails = ref<Record<TherapyType, Therapy>>({
  [TherapyType.BEHAVIORAL]: {
    type: TherapyType.BEHAVIORAL,
    is_received: false,
    therapy_center: '',
    therapist_name: '',
    therapist_contact_number: '',
    therapist_email: '',
  },
  [TherapyType.SPEECH]: {
    type: TherapyType.SPEECH,
    is_received: false,
    therapy_center: '',
    therapist_name: '',
    therapist_contact_number: '',
    therapist_email: '',
  },
  [TherapyType.OCCUPATIONAL]: {
    type: TherapyType.OCCUPATIONAL,
    is_received: false,
    therapy_center: '',
    therapist_name: '',
    therapist_contact_number: '',
    therapist_email: '',
  },
  [TherapyType.PHYSICAL]: {
    type: TherapyType.PHYSICAL,
    is_received: false,
    therapy_center: '',
    therapist_name: '',
    therapist_contact_number: '',
    therapist_email: '',
  },
})

const therapyList = computed(() =>
  Object.values(TherapyType).map((type) => ({
    type,
    details: therapyDetails.value[type],
    label: TherapyTypeLabels[type],
  })),
)

function toggleTherapy(therapyType: TherapyType) {
  const therapy = therapyDetails.value[therapyType]
  therapy.is_received = !therapy.is_received
}

watch(
  therapyDetails,
  (newDetails) => {
    const activeTherapies = Object.values(newDetails).filter((t) => t.is_received)

    if (formData.value) {
      formData.value.therapies = activeTherapies.map((t) => ({ ...t }))
    }
  },
  { deep: true },
)

if (formData.value && !formData.value.therapies) {
  formData.value.therapies = []
}
</script>

<template>
  <Card class="border-0 shadow-none">
    <CardHeader>
      <CardTitle class="text-center text-2xl font-bold text-primary">
        Child Information Form
      </CardTitle>
    </CardHeader>
    <CardContent>
      <form @submit.prevent="submitForm" class="space-y-6">
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

        <div class="space-y-4">
          <Label>Current Educational Placement</Label>
          <Input v-model="formData.educational_placement" placeholder="School/Program name" />

          <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
              <Checkbox
                id="is_initial_assessment"
                v-model:checked="formData.is_initial_assessment"
              />
              <Label for="is_initial_assessment">Initial Assessment</Label>
            </div>
            <div class="flex items-center space-x-2">
              <Checkbox id="is_follow_up" v-model:checked="formData.is_follow_up" />
              <Label for="is_follow_up">Follow-Up</Label>
            </div>
          </div>
        </div>

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
            <Input
              id="age_at_consult"
              v-model="formData.age_at_consult"
              placeholder="e.g. 5 years"
            />
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

        <div class="space-y-2">
          <Label for="siblings">Siblings (names and ages)</Label>
          <Input id="siblings" v-model="formData.siblings" placeholder="e.g. John (8), Mary (5)" />
        </div>

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
              <Input
                id="last_assessment_date"
                v-model="formData.last_assessment_date"
                type="date"
              />
            </div>
          </div>

          <div class="space-y-2">
            <Label for="follow_up_date">Follow-Up Date</Label>
            <Input id="follow_up_date" v-model="formData.follow_up_date" type="date" />
          </div>
        </div>

        <div class="space-y-4">
          <Label class="text-lg font-semibold">Therapy Services</Label>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div
              v-for="therapy in therapyList"
              :key="therapy.type"
              class="flex flex-col space-y-3 p-4 border rounded-lg bg-gray-50"
            >
              <div class="flex items-center space-x-3">
                <Checkbox
                  :id="`therapy-${therapy.type}`"
                  :checked="therapy.details.is_received"
                  @click="toggleTherapy(therapy.type)"
                />
                <Label
                  :for="`therapy-${therapy.type}`"
                  class="font-medium text-base cursor-pointer"
                >
                  {{ therapy.label }}
                </Label>
              </div>

              <div
                v-if="therapy.details.is_received"
                class="ml-8 space-y-3 transition-all duration-200"
              >
                <Input
                  type="text"
                  placeholder="Therapy center"
                  v-model="therapy.details.therapy_center"
                  class="w-full"
                />
                <Input
                  type="text"
                  placeholder="Therapist name"
                  v-model="therapy.details.therapist_name"
                  class="w-full"
                />
                <Input
                  type="tel"
                  placeholder="Therapist contact number"
                  v-model="therapy.details.therapist_contact_number"
                  class="w-full"
                />
                <Input
                  type="email"
                  placeholder="Therapist email"
                  v-model="therapy.details.therapist_email"
                  class="w-full"
                />
              </div>
            </div>
          </div>
        </div>

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
                <SelectItem v-for="grade in gradeOptions" :key="grade" :value="grade">
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
            <Input
              id="year"
              v-model="formData.year"
              type="number"
              min="2000"
              :max="new Date().getFullYear()"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
          <div class="space-y-2">
            <Label for="reason">Reason for referral</Label>
            <Textarea id="reason" v-model="formData.reason" />
          </div>
        </div>

        <div class="flex justify-end space-x-4 pt-6">
          <Button type="submit" :disabled="isLoading">
            <span v-if="isLoading">Saving...</span>
            <span v-else>Submit Information</span>
          </Button>
        </div>
      </form>
    </CardContent>
  </Card>
</template>
