<template>
  <div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
        <p class="text-gray-600">Welcome back, {{ user?.name }}</p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">My Listings</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.listings }}</p>
            </div>
            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
              <span class="text-2xl">📋</span>
            </div>
          </div>
          <NuxtLink to="/my-listings" class="text-sm text-primary-600 hover:text-primary-700 mt-2 inline-block">
            View all →
          </NuxtLink>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Favorites</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.favorites }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
              <span class="text-2xl">❤️</span>
            </div>
          </div>
          <NuxtLink to="/favorites" class="text-sm text-primary-600 hover:text-primary-700 mt-2 inline-block">
            View all →
          </NuxtLink>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Messages</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.messages }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <span class="text-2xl">💬</span>
            </div>
          </div>
          <NuxtLink to="/messages" class="text-sm text-primary-600 hover:text-primary-700 mt-2 inline-block">
            View all →
          </NuxtLink>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Profile Views</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.views }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
              <span class="text-2xl">👀</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <NuxtLink
            to="/listings/create"
            class="flex items-center gap-3 p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition"
          >
            <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center text-white text-xl">
              +
            </div>
            <div>
              <div class="font-medium text-gray-900">Create Listing</div>
              <div class="text-sm text-gray-600">Post a new pet listing</div>
            </div>
          </NuxtLink>

          <NuxtLink
            to="/dogs"
            class="flex items-center gap-3 p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition"
          >
            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-xl">
              🔍
            </div>
            <div>
              <div class="font-medium text-gray-900">Browse Listings</div>
              <div class="text-sm text-gray-600">Find your perfect pet</div>
            </div>
          </NuxtLink>

          <NuxtLink
            to="/profile"
            class="flex items-center gap-3 p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition"
          >
            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-xl">
              ⚙️
            </div>
            <div>
              <div class="font-medium text-gray-900">Edit Profile</div>
              <div class="text-sm text-gray-600">Update your information</div>
            </div>
          </NuxtLink>
        </div>
      </div>

      <!-- Recent Listings -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold text-gray-900">My Recent Listings</h2>
          <NuxtLink to="/my-listings" class="text-sm text-primary-600 hover:text-primary-700">
            View all →
          </NuxtLink>
        </div>

        <div v-if="loading" class="text-center py-8 text-gray-500">
          Loading...
        </div>

        <div v-else-if="recentListings.length > 0" class="space-y-4">
          <div
            v-for="listing in recentListings"
            :key="listing.id"
            class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition"
          >
            <img
              v-if="listing.primary_image"
              :src="'/api/storage/' + listing.primary_image.image_path"
              :alt="listing.title"
              class="w-20 h-20 object-cover rounded"
            />
            <div v-else class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center text-2xl">
              🐕
            </div>

            <div class="flex-1 min-w-0">
              <h3 class="font-medium text-gray-900 truncate">{{ listing.title }}</h3>
              <p class="text-sm text-gray-600">{{ listing.breed?.name }}</p>
              <div class="flex items-center gap-2 mt-1">
                <span
                  :class="[
                    'inline-flex items-center px-2 py-1 rounded text-xs font-medium',
                    listing.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                  ]"
                >
                  {{ listing.status }}
                </span>
                <span class="text-sm text-gray-600">{{ listing.views_count || 0 }} views</span>
              </div>
            </div>

            <div class="text-right">
              <div class="font-semibold text-primary-600">£{{ formatPrice(listing.price) }}</div>
              <NuxtLink
                :to="'/listings/' + listing.slug"
                class="text-sm text-primary-600 hover:text-primary-700 mt-1 inline-block"
              >
                View →
              </NuxtLink>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-8">
          <p class="text-gray-600 mb-4">You haven't created any listings yet</p>
          <NuxtLink
            to="/listings/create"
            class="inline-block bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 transition"
          >
            Create Your First Listing
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const { user } = useAuth()
const { fetchListings } = useListings()
const { fetchFavorites } = useFavorites()
const { fetchConversations } = useMessages()

const loading = ref(true)
const recentListings = ref([])

const stats = ref({
  listings: 0,
  favorites: 0,
  messages: 0,
  views: 0,
})

onMounted(async () => {
  await loadDashboardData()
})

const loadDashboardData = async () => {
  loading.value = true
  try {
    const [myListings, favorites, conversations] = await Promise.all([
      fetchListings({ user_id: user.value?.id, per_page: 5 }),
      fetchFavorites(),
      fetchConversations(),
    ])

    recentListings.value = myListings.data || []
    stats.value.listings = myListings.total || 0
    stats.value.favorites = favorites.length || 0
    stats.value.messages = conversations.length || 0
    
    stats.value.views = recentListings.value.reduce(
      (sum, listing) => sum + (listing.views_count || 0),
      0
    )
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  } finally {
    loading.value = false
  }
}

const formatPrice = (price: number) => {
  if (!price) return '0'
  return new Intl.NumberFormat('en-GB').format(price)
}
</script>
