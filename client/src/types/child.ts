export type Gender = 'male' | 'female' | 'other'

export interface Child {
  id: number
  surname: string
  first_name: string
  middle_name?: string
  educational_placement?: string
  is_initial_assessment: boolean
  is_follow_up: boolean
  address?: string
  email?: string
  date_of_birth: string
  date_of_assessment: string
  age_at_consult: string
  gender: Gender
  siblings?: string
  mother_name: string
  mother_occupation?: string
  mother_contact: string
  father_name: string
  father_occupation?: string
  father_contact: string
  medical_diagnosis?: string
  referring_doctor?: string
  last_assessment_date?: string
  follow_up_date?: string
  school?: string
  grade?: string
  placement?: string
  year?: number
  assessments_count?: number
  created_at?: string
  updated_at?: string
  reason?: string
  assessments?: Assessment[]
  therapies?: Therapy[]
}
export interface Therapy {
  type: TherapyType
  is_received: boolean
  therapy_center?: string
  therapist_name?: string
  therapist_contact_number?: string
  therapist_email?: string

}
export enum TherapyType {
  OCCUPATIONAL = 'occupational_therapy',
  PHYSICAL = 'physical_therapy',
  BEHAVIORAL = 'behavioral_therapy',
  SPEECH = 'speech_therapy',
}
export const TherapyTypeLabels: Record<TherapyType, string> = {
  [TherapyType.OCCUPATIONAL]: 'Occupational Therapy',
    [TherapyType.PHYSICAL]: 'Phyiscal Therapy',
    [TherapyType.BEHAVIORAL]: 'Behavioral Therapy',
  [TherapyType.SPEECH]: 'Speech Therapy',
  }
export interface Assessment {
  id: string
  child_id: string
  assessment_date: string
  notes?: string
  evaluations_count?: number
  evaluations?: Evaluation[]
}

export interface Evaluation {
  id: string
  assessment_id: string
  created_at?: string
  updated_at?: string
}

export interface ApiResponse<T> {
  data: T
  message?: string
  success: boolean
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

export interface ChildrenResponse extends PaginatedResponse<Child> {}

export interface ColumnDef<T> {
  id: string
  header: string | ((props: any) => any)
  cell: (props: any) => any
  accessorKey?: keyof T
  enableSorting?: boolean
  enableHiding?: boolean
}

export interface TableFilter {
  id: string
  value: any
}
