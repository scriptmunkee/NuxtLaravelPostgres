export const useFavorites = () => {
  const { api } = useApi()

  const fetchFavorites = async () => {
    try {
      const data = await api('/favorites')
      return data.favorites || []
    } catch (error) {
      console.error('Error fetching favorites:', error)
      return []
    }
  }

  const addToFavorites = async (listingId: number | string) => {
    try {
      const data = await api(`/listings/${listingId}/favorite`, {
        method: 'POST',
      })
      return { success: true, favorited: data.favorited }
    } catch (error) {
      console.error('Error adding to favorites:', error)
      return { success: false, error }
    }
  }

  const removeFromFavorites = async (listingId: number | string) => {
    try {
      const data = await api(`/listings/${listingId}/unfavorite`, {
        method: 'DELETE',
      })
      return { success: true, favorited: false }
    } catch (error) {
      console.error('Error removing from favorites:', error)
      return { success: false, error }
    }
  }

  const checkFavorite = async (listingId: number | string) => {
    try {
      const data = await api(`/listings/${listingId}/favorite/check`)
      return data.favorited || false
    } catch (error) {
      console.error('Error checking favorite:', error)
      return false
    }
  }

  const toggleFavorite = async (listingId: number | string, currentState: boolean) => {
    if (currentState) {
      return await removeFromFavorites(listingId)
    } else {
      return await addToFavorites(listingId)
    }
  }

  return {
    fetchFavorites,
    addToFavorites,
    removeFromFavorites,
    checkFavorite,
    toggleFavorite,
  }
}
