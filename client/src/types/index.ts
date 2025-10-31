export interface Question {
  id: number
  text: string
  age: number
  skill_category_id: number
  category?: SkillCategory
}

export interface SkillCategory {
  id: number
  name: string
  slug: string
  color: string
  questions_count?: number
}

export interface AssessmentResult {
  child_name: string
  assessment_date: string
  assessed_ages?: number[]
  categories: {
    name: string
    age?: number
    responses: string[]
    competency: string
  }[]
}
export interface Evaluation {
  id?: string
  created_at?: string
  background_information: string
  recommendations: string[]
  summary_notes: string
  assessment?: Assessment
    assessed_ages?: number[]
}

export interface Assessment {
  id?: string
  child_name: string
  assessment_date?: string
  categories: {
    name: string
    age: number
    responses: string[]
    competency: string
  }[]
    assessed_ages?: number[]
}

