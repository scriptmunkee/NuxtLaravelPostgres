<template>
  <div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">My Listings</h1>
          <p class="text-gray-600">
            {{ listings.length }} {{ listings.length === 1 ? 'listing' : 'listings' }}
          </p>
        </div>
        <NuxtLink
          to="/listings/create"
          class="bg-primary-600 text-white px-6 py-3 rounded-md hover:bg-primary-700 transition inline-flex items-center gap-2"
        >
          <span class="text-xl">+</span>
          Create Listing
        </NuxtLink>
      </div>

      <!-- Filter Tabs -->
      <div class="mb-6 border-b border-gray-200">
        <nav class="flex gap-8">
          <button
            v-for="tab in tabs"
            :key="tab.value"
            @click="selectedTab = tab.value"
            :class="[
              'pb-4 px-1 border-b-2 font-medium text-sm transition',
              selectedTab === tab.value
                ? 'border-primary-500 text-primary-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.label }}
            <span
              :class="[
                'ml-2 px-2 py-1 rounded-full text-xs',
                selectedTab === tab.value
                  ? 'bg-primary-100 text-primary-600'
                  : 'bg-gray-100 text-gray-600'
              ]"
            >
              {{ getTabCount(tab.value) }}
            </span>
          </button>
        </nav>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="text-gray-500">Loading your listings...</div>
      </div>

      <!-- Listings Grid -->
      <div v-else-if="filteredListings.length > 0" class="space-y-4">
        <div
          v-for="listing in filteredListings"
          :key="listing.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition"
        >
          <div class="md:flex">
            <!-- Image -->
            <div class="md:w-64 h-48 bg-gray-200 flex-shrink-0">
              <img
                v-if="listing.primary_image"
                :src="'/api/storage/' + listing.primary_image.image_path"
                :alt="listing.title"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-6xl">
                🐕
              </div>
            </div>

            <!-- Content -->
            <div class="flex-1 p-6">
              <div class="flex items-start justify-between mb-2">
                <div class="flex-1">
                  <h3 class="text-xl font-semibold text-gray-900 mb-1">
                    {{ listing.title }}
                  </h3>
                  <p class="text-gray-600">
                    {{ listing.breed?.name }} • {{ listing.location?.city }}
                  </p>
                </div>
                <div class="text-right ml-4">
                  <div class="text-2xl font-bold text-primary-600">
                    £{{ formatPrice(listing.price) }}
                  </div>
                  <span
                    :class="[
                      'inline-flex items-center px-2 py-1 rounded text-xs font-medium mt-1',
                      getStatusClass(listing.status)
                    ]"
                  >
                    {{ listing.status }}
                  </span>
                </div>
              </div>

              <div class="flex items-center gap-6 text-sm text-gray-600 mb-4">
                <span>👁️ {{ listing.views_count || 0 }} views</span>
                <span>❤️ {{ listing.favorites_count || 0 }} saves</span>
                <span>📅 Listed {{ formatDate(listing.created_at) }}</span>
              </div>

              <!-- Actions -->
              <div class="flex gap-2">
                <NuxtLink
                  :to="'/listings/' + listing.slug"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
                >
                  View
                </NuxtLink>
                <NuxtLink
                  :to="'/listings/' + listing.id + '/edit'"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
                >
                  Edit
                </NuxtLink>
                <button
                  v-if="listing.status === 'active'"
                  @click="markAsSold(listing.id)"
                  class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                >
                  Mark as Sold
                </button>
                <button
                  v-else-if="listing.status === 'sold'"
                  @click="reactivateListing(listing.id)"
                  class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition"
                >
                  Reactivate
                </button>
                <button
                  @click="confirmDelete(listing.id)"
                  class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition ml-auto"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16 bg-white rounded-lg shadow-md">
        <div class="text-6xl mb-4">📋</div>
        <h2 class="text-2xl font-semibold text-gray-900 mb-2">
          {{ selectedTab === 'all' ? 'No listings yet' : 'No ' + selectedTab + ' listings' }}
        </h2>
        <p class="text-gray-600 mb-6">
          {{ selectedTab === 'all' ? 'Create your first listing to get started' : 'Try viewing all listings' }}
        </p>
        <NuxtLink
          v-if="selectedTab === 'all'"
          to="/listings/create"
          class="inline-block bg-primary-600 text-white px-6 py-3 rounded-md hover:bg-primary-700 transition"
        >
          Create Listing
        </NuxtLink>
        <button
          v-else
          @click="selectedTab = 'all'"
          class="inline-block bg-primary-600 text-white px-6 py-3 rounded-md hover:bg-primary-700 transition"
        >
          View All Listings
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const { user } = useAuth()
const { fetchListings, updateListing, deleteListing } = useListings()

const listings = ref([])
const loading = ref(true)
const selectedTab = ref('all')

const tabs = [
  { label: 'All', value: 'all' },
  { label: 'Active', value: 'active' },
  { label: 'Sold', value: 'sold' },
  { label: 'Draft', value: 'draft' },
]

onMounted(async () => {
  await loadListings()
})

const loadListings = async () => {
  loading.value = true
  try {
    const result = await fetchListings({ user_id: user.value?.id, per_page: 100 })
    listings.value = result.data || []
  } catch (error) {
    console.error('Error loading listings:', error)
  } finally {
    loading.value = false
  }
}

const filteredListings = computed(() => {
  if (selectedTab.value === 'all') return listings.value
  return listings.value.filter(l => l.status === selectedTab.value)
})

const getTabCount = (tab: string) => {
  if (tab === 'all') return listings.value.length
  return listings.value.filter(l => l.status === tab).length
}

const getStatusClass = (status: string) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    sold: 'bg-blue-100 text-blue-800',
    draft: 'bg-gray-100 text-gray-800',
    inactive: 'bg-yellow-100 text-yellow-800',
    flagged: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatPrice = (price: number) => {
  if (!price) return '0'
  return new Intl.NumberFormat('en-GB').format(price)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const markAsSold = async (listingId: number) => {
  const result = await updateListing(listingId, { status: 'sold' })
  if (result.success) {
    const listing = listings.value.find(l => l.id === listingId)
    if (listing) listing.status = 'sold'
  }
}

const reactivateListing = async (listingId: number) => {
  const result = await updateListing(listingId, { status: 'active' })
  if (result.success) {
    const listing = listings.value.find(l => l.id === listingId)
    if (listing) listing.status = 'active'
  }
}

const confirmDelete = async (listingId: number) => {
  if (!confirm('Are you sure you want to delete this listing? This action cannot be undone.')) {
    return
  }

  const result = await deleteListing(listingId)
  if (result.success) {
    listings.value = listings.value.filter(l => l.id !== listingId)
  }
}
</script>
