<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-md">
      <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <NuxtLink to="/" class="text-2xl font-bold text-blue-600">Pet Marketplace</NuxtLink>
        <div class="space-x-4">
          <template v-if="isAuthenticated">
            <NuxtLink to="/dashboard" class="text-gray-700 hover:text-blue-600">Dashboard</NuxtLink>
            <button @click="handleLogout" class="text-gray-700 hover:text-blue-600">Logout</button>
          </template>
          <template v-else>
            <NuxtLink to="/auth/login" class="text-gray-700 hover:text-blue-600">Login</NuxtLink>
            <NuxtLink to="/auth/register" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Register</NuxtLink>
          </template>
        </div>
      </div>
    </nav>

    <div class="container mx-auto px-4 py-12">
      <div class="text-center mb-12">
        <h1 class="text-5xl font-bold mb-4">Find Your Perfect Pet</h1>
        <p class="text-xl text-gray-600">Browse dogs for sale by breed and location</p>
      </div>

      <div class="max-w-2xl mx-auto mb-12">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-2xl font-bold mb-4">Search by Breed and Location</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <input
              v-model="searchBreed"
              type="text"
              placeholder="Enter breed (e.g., Golden Retriever)"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <input
              v-model="searchLocation"
              type="text"
              placeholder="Enter location (e.g., New York)"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <button
            @click="handleSearch"
            class="w-full bg-blue-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-600"
          >
            Search Listings
          </button>
        </div>
      </div>

      <div class="mb-8">
        <h2 class="text-3xl font-bold mb-6">All Pet Listings</h2>
        
        <div v-if="loading" class="text-center py-12">
          <p class="text-xl">Loading listings...</p>
        </div>

        <div v-else-if="listings.length === 0" class="text-center py-12">
          <p class="text-xl text-gray-600">No listings available yet</p>
          <NuxtLink v-if="isSeller" to="/dashboard" class="text-blue-500 hover:underline mt-4 inline-block">
            Create your first listing
          </NuxtLink>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="listing in listings"
            :key="listing.id"
            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
            @click="viewListing(listing)"
          >
            <div class="h-48 bg-gray-300 flex items-center justify-center">
              <span class="text-gray-500">No image</span>
            </div>
            <div class="p-4">
              <h3 class="text-xl font-bold mb-2">{{ listing.title }}</h3>
              <p class="text-gray-600 mb-2">{{ listing.breed }} {{ listing.location ? `• ${listing.location}` : '' }}</p>
              <p class="text-gray-600 mb-2">{{ listing.age_months }} months old</p>
              <p class="text-2xl font-bold text-blue-600">${{ listing.price }}</p>
              <p class="text-sm text-gray-500 mt-2">Listed by {{ listing.user?.name }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { isAuthenticated, isSeller, logout } = useAuth()
const api = useApi()
const router = useRouter()

const searchBreed = ref('')
const searchLocation = ref('')
const listings = ref<any[]>([])
const loading = ref(true)

const handleSearch = () => {
  if (searchBreed.value && searchLocation.value) {
    const breed = searchBreed.value.toLowerCase().replace(/\s+/g, '-')
    const location = searchLocation.value.toLowerCase().replace(/\s+/g, '-')
    router.push(`/${breed}/${location}`)
  }
}

const viewListing = (listing: any) => {
  if (listing.breed && listing.location) {
    const breed = listing.breed.toLowerCase().replace(/\s+/g, '-')
    const location = listing.location.toLowerCase().replace(/\s+/g, '-')
    router.push(`/${breed}/${location}`)
  }
}

const handleLogout = async () => {
  await logout()
}

const fetchListings = async () => {
  loading.value = true
  const { data } = await api.get('/pet-listings')
  
  if (data) {
    listings.value = data.data || []
  }
  loading.value = false
}

onMounted(() => {
  fetchListings()
})

useHead({
  title: 'Pet Marketplace - Find Dogs for Sale',
  meta: [
    {
      name: 'description',
      content: 'Browse quality dogs for sale. Find puppies and adult dogs by breed and location. Connect with trusted sellers in your area.'
    }
  ]
})
</script>
