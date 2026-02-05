interface FoiAuthority {
  id: number
  wdtk_slug: string
  name: string
  short_name: string | null
  url: string | null
}

interface FoiRequest {
  id: number
  wdtk_url_title: string
  title: string
  authority_name: string | null
  status: string
  display_status: string | null
  description: string | null
  wdtk_url: string
  requester_name: string | null
  request_created_at: string | null
  request_updated_at: string | null
  is_tracked: boolean
  internal_notes: string | null
  tags: string[] | null
  last_polled_at: string | null
  authority: FoiAuthority | null
}

interface FoiStatusUpdate {
  id: number
  foi_request_id: number
  old_status: string | null
  new_status: string
  display_status: string | null
  description: string | null
  is_acknowledged: boolean
  detected_at: string
  foi_request?: FoiRequest
}

interface DashboardData {
  total_tracked: number
  status_summary: Record<string, number>
  unacknowledged_updates: number
  recent_updates: FoiStatusUpdate[]
  overdue_requests: FoiRequest[]
  awaiting_action: FoiRequest[]
}

interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export function useFoi() {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase as string

  async function fetchDashboard(): Promise<DashboardData> {
    const res = await $fetch<DashboardData>(`${apiBase}/foi/dashboard`)
    return res
  }

  async function fetchRequests(params?: Record<string, string | number | boolean>): Promise<PaginatedResponse<FoiRequest>> {
    const query = params ? '?' + new URLSearchParams(
      Object.entries(params).map(([k, v]) => [k, String(v)])
    ).toString() : ''
    return await $fetch<PaginatedResponse<FoiRequest>>(`${apiBase}/foi/requests${query}`)
  }

  async function fetchRequest(id: number): Promise<FoiRequest> {
    return await $fetch<FoiRequest>(`${apiBase}/foi/requests/${id}`)
  }

  async function importRequest(urlTitle: string): Promise<FoiRequest> {
    return await $fetch<FoiRequest>(`${apiBase}/foi/requests/import`, {
      method: 'POST',
      body: { url_title: urlTitle },
    })
  }

  async function pollRequest(id: number) {
    return await $fetch(`${apiBase}/foi/requests/${id}/poll`, {
      method: 'POST',
    })
  }

  async function updateRequest(id: number, data: Partial<FoiRequest>) {
    return await $fetch<FoiRequest>(`${apiBase}/foi/requests/${id}`, {
      method: 'PUT',
      body: data,
    })
  }

  async function deleteRequest(id: number) {
    return await $fetch(`${apiBase}/foi/requests/${id}`, {
      method: 'DELETE',
    })
  }

  async function fetchStatusUpdates(params?: Record<string, string | number | boolean>) {
    const query = params ? '?' + new URLSearchParams(
      Object.entries(params).map(([k, v]) => [k, String(v)])
    ).toString() : ''
    return await $fetch<PaginatedResponse<FoiStatusUpdate>>(`${apiBase}/foi/status-updates${query}`)
  }

  async function acknowledgeUpdate(id: number) {
    return await $fetch(`${apiBase}/foi/status-updates/${id}/acknowledge`, {
      method: 'POST',
    })
  }

  async function acknowledgeAll() {
    return await $fetch(`${apiBase}/foi/status-updates/acknowledge-all`, {
      method: 'POST',
    })
  }

  function statusColor(status: string): string {
    const colors: Record<string, string> = {
      waiting_response: '#3498db',
      waiting_response_overdue: '#e67e22',
      waiting_response_very_overdue: '#e74c3c',
      waiting_clarification: '#f39c12',
      waiting_classification: '#9b59b6',
      successful: '#27ae60',
      partially_successful: '#2ecc71',
      rejected: '#e74c3c',
      not_held: '#95a5a6',
      error_message: '#c0392b',
      internal_review: '#8e44ad',
      gone_postal: '#7f8c8d',
      user_withdrawn: '#bdc3c7',
      requires_admin: '#d35400',
    }
    return colors[status] || '#95a5a6'
  }

  function statusLabel(status: string): string {
    return status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
  }

  return {
    fetchDashboard,
    fetchRequests,
    fetchRequest,
    importRequest,
    pollRequest,
    updateRequest,
    deleteRequest,
    fetchStatusUpdates,
    acknowledgeUpdate,
    acknowledgeAll,
    statusColor,
    statusLabel,
  }
}
