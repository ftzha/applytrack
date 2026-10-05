export interface RecentApplication {
  id: number
  company_name: string
  position: string
  status: string
  applied_at: string | null
  created_at: string
}

interface RecentActivity {
  id: number
  application_id: number
  from_status: string | null
  to_status: string
  created_at: string

  application: {
    id: number
    company_name: string
    position: string
  }
}

export interface DashboardStats {
  total: number
  interested: number
  applied: number
  screening: number
  interview: number
  offer: number
  rejected: number
  withdrawn: number
  recent_applications: RecentApplication[]
  recent_activity: RecentActivity[]
}
