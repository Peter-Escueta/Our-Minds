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
