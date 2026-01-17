<template>
  <div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Saved Listings</h1>
        <p class="text-gray-600">
          {{ favorites.length }} {{ favorites.length === 1 ? 'listing' : 'listings' }} saved
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="text-gray-500">Loading your saved listings...</div>
      </div>

      <!-- Favorites Grid -->
      <div v-else-if="favorites.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="listing in favorites"
          :key="listing.id"
          class="relative"
        >
          <ListingCard :listing="listing" />
          
          <!-- Remove Button -->
          <button
            @click="handleRemoveFavorite(listing.id)"
            class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full shadow-lg hover:bg-red-600 transition z-10"
            title="Remove from favorites"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- Favorited Date -->
          <div class="mt-2 text-xs text-gray-500">
            Saved {{ formatRelativeTime(listing.favorited_at) }}
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16">
        <div class="text-6xl mb-4">❤️</div>
        <h2 class="text-2xl font-semibold text-gray-900 mb-2">No saved listings yet</h2>
        <p class="text-gray-600 mb-6">Start browsing and save your favorite pet listings</p>
        <NuxtLink
          to="/dogs"
          class="inline-block bg-primary-600 text-white px-6 py-3 rounded-md hover:bg-primary-700 transition"
        >
          Browse Dogs
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const { fetchFavorites, removeFromFavorites } = useFavorites()

const favorites = ref([])
const loading = ref(true)

onMounted(async () => {
  await loadFavorites()
})

const loadFavorites = async () => {
  loading.value = true
  try {
    favorites.value = await fetchFavorites()
  } catch (error) {
    console.error('Error loading favorites:', error)
  } finally {
    loading.value = false
  }
}

const handleRemoveFavorite = async (listingId: number) => {
  const result = await removeFromFavorites(listingId)
  if (result.success) {
    favorites.value = favorites.value.filter(f => f.id !== listingId)
  }
}

const formatRelativeTime = (date: string) => {
  const now = new Date()
  const past = new Date(date)
  const diffInSeconds = Math.floor((now.getTime() - past.getTime()) / 1000)

  if (diffInSeconds < 60) return 'just now'
  if (diffInSeconds < 3600) {
    const minutes = Math.floor(diffInSeconds / 60)
    return minutes + 'm ago'
  }
  if (diffInSeconds < 86400) {
    const hours = Math.floor(diffInSeconds / 3600)
    return hours + 'h ago'
  }
  if (diffInSeconds < 604800) {
    const days = Math.floor(diffInSeconds / 86400)
    return days + 'd ago'
  }
  
  return past.toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'short',
    year: past.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
  })
}
</script>
