export interface RecentApplication {
  id: number
  company_name: string
  position: string
  status: string
  applied_at: string | null
  created_at: string
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
}
