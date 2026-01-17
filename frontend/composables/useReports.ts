export const useReports = () => {
  const { api } = useApi()

  const submitReport = async (listingId: number | string, reason: string, description: string) => {
    try {
      const data = await api('/reports', {
        method: 'POST',
        body: {
          listing_id: listingId,
          reason,
          description,
        },
      })
      return { success: true, message: data.message }
    } catch (error: any) {
      console.error('Error submitting report:', error)
      return { 
        success: false, 
        error: error.data?.error || error.data?.errors || 'Failed to submit report' 
      }
    }
  }

  const fetchReports = async () => {
    try {
      const data = await api('/reports')
      return data.data || []
    } catch (error) {
      console.error('Error fetching reports:', error)
      return []
    }
  }

  return {
    submitReport,
    fetchReports,
  }
}
