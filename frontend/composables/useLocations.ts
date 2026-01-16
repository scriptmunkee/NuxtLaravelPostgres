export const useLocations = () => {
  const { api } = useApi()

  const fetchLocations = async () => {
    try {
      const data = await api('/locations')
      return data.locations || []
    } catch (error) {
      console.error('Error fetching locations:', error)
      return []
    }
  }

  const searchLocations = async (query: string) => {
    try {
      const data = await api(`/locations/search?q=${encodeURIComponent(query)}`)
      return data.locations || []
    } catch (error) {
      console.error('Error searching locations:', error)
      return []
    }
  }

  return {
    fetchLocations,
    searchLocations,
  }
}
