export const useListings = () => {
  const { api } = useApi()

  const fetchListings = async (params: any = {}) => {
    try {
      const queryParams = new URLSearchParams()

      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
          queryParams.append(key, params[key])
        }
      })

      const queryString = queryParams.toString()
      const url = queryString ? `/listings?${queryString}` : '/listings'

      return await api(url)
    } catch (error) {
      console.error('Error fetching listings:', error)
      return { data: [], total: 0 }
    }
  }

  const fetchListing = async (id: number | string) => {
    try {
      const data = await api(`/listings/${id}`)
      return data.listing
    } catch (error) {
      console.error('Error fetching listing:', error)
      return null
    }
  }

  const createListing = async (listingData: any) => {
    try {
      const data = await api('/listings', {
        method: 'POST',
        body: listingData,
      })

      return { success: true, listing: data.listing }
    } catch (error) {
      return { success: false, error }
    }
  }

  const updateListing = async (id: number | string, listingData: any) => {
    try {
      const data = await api(`/listings/${id}`, {
        method: 'PUT',
        body: listingData,
      })

      return { success: true, listing: data.listing }
    } catch (error) {
      return { success: false, error }
    }
  }

  const deleteListing = async (id: number | string) => {
    try {
      await api(`/listings/${id}`, {
        method: 'DELETE',
      })

      return { success: true }
    } catch (error) {
      return { success: false, error }
    }
  }

  const fetchMyListings = async () => {
    try {
      return await api('/my-listings')
    } catch (error) {
      console.error('Error fetching my listings:', error)
      return { data: [], total: 0 }
    }
  }

  const searchListings = async (query: string) => {
    try {
      return await api(`/listings/search?q=${encodeURIComponent(query)}`)
    } catch (error) {
      console.error('Error searching listings:', error)
      return { data: [], total: 0 }
    }
  }

  const uploadImages = async (listingId: number | string, images: File[]) => {
    try {
      const formData = new FormData()
      images.forEach((image) => {
        formData.append('images[]', image)
      })

      const data = await api(`/listings/${listingId}/images`, {
        method: 'POST',
        body: formData,
      })

      return { success: true, images: data.images }
    } catch (error) {
      return { success: false, error }
    }
  }

  const deleteImage = async (imageId: number) => {
    try {
      await api(`/listings/images/${imageId}`, {
        method: 'DELETE',
      })

      return { success: true }
    } catch (error) {
      return { success: false, error }
    }
  }

  return {
    fetchListings,
    fetchListing,
    createListing,
    updateListing,
    deleteListing,
    fetchMyListings,
    searchListings,
    uploadImages,
    deleteImage,
  }
}
