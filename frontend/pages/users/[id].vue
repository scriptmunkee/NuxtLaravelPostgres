<template>
  <div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="loading" class="text-center py-12">
        <div class="text-gray-500">Loading profile...</div>
      </div>

      <div v-else-if="userProfile" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar - User Info -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
            <!-- Avatar -->
            <div class="flex flex-col items-center mb-6">
              <div class="w-24 h-24 bg-primary-500 rounded-full flex items-center justify-center text-white font-bold text-3xl mb-3">
                {{ userProfile.name.charAt(0).toUpperCase() }}
              </div>
              <h2 class="text-2xl font-bold text-gray-900">{{ userProfile.name }}</h2>
              <p v-if="userProfile.is_verified" class="text-sm text-green-600 flex items-center mt-1">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Verified Seller
              </p>
            </div>

            <!-- Rating Summary -->
            <div class="mb-6 pb-6 border-b border-gray-200">
              <div class="text-center mb-4">
                <div class="text-4xl font-bold text-gray-900">{{ reviews.averageRating.toFixed(1) }}</div>
                <div class="flex items-center justify-center mt-1">
                  <div class="flex">
                    <span v-for="i in 5" :key="i" :class="i <= Math.round(reviews.averageRating) ? 'text-yellow-400' : 'text-gray-300'">
                      ★
                    </span>
                  </div>
                </div>
                <p class="text-sm text-gray-600 mt-1">{{ reviews.totalReviews }} reviews</p>
              </div>

              <!-- Rating breakdown -->
              <div class="space-y-2">
                <div v-for="i in [5, 4, 3, 2, 1]" :key="i" class="flex items-center gap-2">
                  <span class="text-sm text-gray-600 w-8">{{ i }}★</span>
                  <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div
                      class="h-full bg-yellow-400 rounded-full"
                      :style="{ width: getRatingPercentage(i) + '%' }"
                    ></div>
                  </div>
                  <span class="text-sm text-gray-600 w-8 text-right">{{ reviews.ratingCounts[i] || 0 }}</span>
                </div>
              </div>
            </div>

            <!-- Member Since -->
            <div class="text-sm text-gray-600">
              <p class="font-medium text-gray-900 mb-1">Member since</p>
              <p>{{ formatDate(userProfile.created_at) }}</p>
            </div>

            <!-- Active Listings Count -->
            <div class="mt-4 text-sm text-gray-600">
              <p class="font-medium text-gray-900 mb-1">Active Listings</p>
              <p>{{ userListings.length }} listings</p>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Active Listings -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Active Listings</h3>
            
            <div v-if="userListings.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <ListingCard
                v-for="listing in userListings"
                :key="listing.id"
                :listing="listing"
              />
            </div>

            <div v-else class="text-center py-8 text-gray-500">
              No active listings at the moment
            </div>
          </div>

          <!-- Reviews -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Reviews</h3>

            <div v-if="reviews.reviews.length > 0" class="space-y-4">
              <div
                v-for="review in reviews.reviews"
                :key="review.id"
                class="border-b border-gray-200 pb-4 last:border-0"
              >
                <div class="flex items-start gap-3">
                  <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-semibold">
                    {{ review.reviewer.name.charAt(0).toUpperCase() }}
                  </div>
                  <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                      <div>
                        <p class="font-medium text-gray-900">{{ review.reviewer.name }}</p>
                        <div class="flex items-center gap-2">
                          <div class="flex text-yellow-400">
                            <span v-for="i in review.rating" :key="i">★</span>
                            <span v-for="i in (5 - review.rating)" :key="'empty-' + i" class="text-gray-300">★</span>
                          </div>
                          <span class="text-xs text-gray-500">{{ formatRelativeTime(review.created_at) }}</span>
                        </div>
                      </div>
                    </div>
                    <p v-if="review.comment" class="text-gray-700 text-sm">{{ review.comment }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8 text-gray-500">
              No reviews yet
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const { isAuthenticated, user } = useAuth()
const { fetchUserReviews } = useReviews()
const { fetchListings } = useListings()

const loading = ref(true)
const userProfile = ref(null)
const userListings = ref([])

const reviews = ref({
  reviews: [],
  averageRating: 0,
  totalReviews: 0,
  ratingCounts: {},
})

onMounted(async () => {
  const userId = route.params.id
  await loadUserProfile(userId)
})

const loadUserProfile = async (userId: number | string) => {
  loading.value = true
  try {
    const reviewsData = await fetchUserReviews(userId)
    reviews.value = reviewsData

    const listingsData = await fetchListings({ user_id: userId, status: 'active', per_page: 6 })
    userListings.value = listingsData.data || []

    if (userListings.value.length > 0) {
      userProfile.value = userListings.value[0].user
    } else {
      userProfile.value = {
        id: userId,
        name: 'User',
        created_at: new Date().toISOString(),
        is_verified: false
      }
    }
  } catch (error) {
    console.error('Error loading user profile:', error)
  } finally {
    loading.value = false
  }
}

const getRatingPercentage = (rating: number) => {
  if (reviews.value.totalReviews === 0) return 0
  const count = reviews.value.ratingCounts[rating] || 0
  return (count / reviews.value.totalReviews) * 100
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-GB', {
    month: 'long',
    year: 'numeric'
  })
}

const formatRelativeTime = (date: string) => {
  const now = new Date()
  const past = new Date(date)
  const diffInDays = Math.floor((now.getTime() - past.getTime()) / (1000 * 60 * 60 * 24))

  if (diffInDays === 0) return 'Today'
  if (diffInDays === 1) return 'Yesterday'
  if (diffInDays < 7) return diffInDays + ' days ago'
  if (diffInDays < 30) return Math.floor(diffInDays / 7) + ' weeks ago'
  if (diffInDays < 365) return Math.floor(diffInDays / 30) + ' months ago'
  return Math.floor(diffInDays / 365) + ' years ago'
}
</script>
