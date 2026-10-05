export interface Application {
  id: number
  company_name: string
  position: string
  location: string | null
  employment_type: string | null
  work_mode: string | null
  salary_min: string | null
  salary_max: string | null
  currency: string | null
  source: string | null
  job_url: string | null
  status: string
  applied_at: string | null
  notes: string | null
  created_at: string
  updated_at: string

  status_histories?: ApplicationStatusHistory[]
}

export interface ApplicationFormData {
  company_name: string
  position: string
  location: string
  employment_type: string
  work_mode: string
  salary_min: string
  salary_max: string
  currency: string
  source: string
  job_url: string
  status: string
  applied_at: string
  notes: string
}

export interface StatusOption {
  value: string
  label: string
}

export interface ApplicationPagination {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number | null
  to: number | null
}

export interface ApplicationStatusHistory {
  id: number
  application_id: number
  from_status: string | null
  to_status: string
  created_at: string
  updated_at: string
}
