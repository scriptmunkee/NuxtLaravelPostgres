export const useReviews = () => {
  const { api } = useApi()

  const fetchUserReviews = async (userId: number | string) => {
    try {
      const data = await api(`/users/${userId}/reviews`)
      return {
        reviews: data.reviews.data || [],
        averageRating: data.average_rating || 0,
        totalReviews: data.total_reviews || 0,
        ratingCounts: data.rating_counts || {},
      }
    } catch (error) {
      console.error('Error fetching reviews:', error)
      return {
        reviews: [],
        averageRating: 0,
        totalReviews: 0,
        ratingCounts: {},
      }
    }
  }

  const submitReview = async (reviewedUserId: number, rating: number, comment: string, listingId?: number) => {
    try {
      const data = await api('/reviews', {
        method: 'POST',
        body: {
          reviewed_user_id: reviewedUserId,
          listing_id: listingId,
          rating,
          comment,
        },
      })
      return { success: true, review: data.review, message: data.message }
    } catch (error: any) {
      console.error('Error submitting review:', error)
      return { 
        success: false, 
        error: error.data?.error || error.data?.errors || 'Failed to submit review' 
      }
    }
  }

  const updateReview = async (reviewId: number, rating: number, comment: string) => {
    try {
      const data = await api(`/reviews/${reviewId}`, {
        method: 'PUT',
        body: {
          rating,
          comment,
        },
      })
      return { success: true, review: data.review, message: data.message }
    } catch (error: any) {
      console.error('Error updating review:', error)
      return { success: false, error: error.data?.error || 'Failed to update review' }
    }
  }

  const deleteReview = async (reviewId: number) => {
    try {
      await api(`/reviews/${reviewId}`, {
        method: 'DELETE',
      })
      return { success: true }
    } catch (error) {
      console.error('Error deleting review:', error)
      return { success: false, error: 'Failed to delete review' }
    }
  }

  return {
    fetchUserReviews,
    submitReview,
    updateReview,
    deleteReview,
  }
}
