<template>
  <div class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
      <div class="mb-6">
        <h1 class="text-4xl font-bold mb-2">{{ formattedBreed }} in {{ formattedLocation }}</h1>
        <p class="text-gray-600">Browse available {{ formattedBreed.toLowerCase() }} listings in {{ formattedLocation }}</p>
      </div>

      <div v-if="loading" class="text-center py-12">
        <p class="text-xl">Loading listings...</p>
      </div>

      <div v-else-if="listings.length === 0" class="text-center py-12">
        <p class="text-xl text-gray-600">No listings found for {{ formattedBreed }} in {{ formattedLocation }}</p>
        <NuxtLink to="/" class="text-blue-500 hover:underline mt-4 inline-block">Browse all listings</NuxtLink>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="listing in listings"
          :key="listing.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
        >
          <div class="h-48 bg-gray-300 flex items-center justify-center">
            <span class="text-gray-500">No image</span>
          </div>
          <div class="p-4">
            <h3 class="text-xl font-bold mb-2">{{ listing.title }}</h3>
            <p class="text-gray-600 mb-2">{{ listing.breed }} • {{ listing.location }}</p>
            <p class="text-gray-600 mb-2">{{ listing.age_months }} months old</p>
            <p class="text-2xl font-bold text-blue-600">${{ listing.price }}</p>
            <p class="text-sm text-gray-500 mt-2">Listed by {{ listing.user?.name }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const api = useApi()

const breed = computed(() => route.params.breed as string)
const location = computed(() => route.params.location as string)

const formattedBreed = computed(() => {
  return breed.value.charAt(0).toUpperCase() + breed.value.slice(1).replace(/-/g, ' ')
})

const formattedLocation = computed(() => {
  return location.value.charAt(0).toUpperCase() + location.value.slice(1).replace(/-/g, ' ')
})

const listings = ref<any[]>([])
const loading = ref(true)

const fetchListings = async () => {
  loading.value = true
  const { data } = await api.get('/pet-listings', {
    query: {
      breed: breed.value.replace(/-/g, ' '),
      location: location.value.replace(/-/g, ' ')
    }
  })
  
  if (data) {
    listings.value = data.data || []
  }
  loading.value = false
}

onMounted(() => {
  fetchListings()
})

watch([() => route.params.breed, () => route.params.location], () => {
  fetchListings()
})

useHead({
  title: `${formattedBreed.value} in ${formattedLocation.value} - Pet Listings`,
  meta: [
    {
      name: 'description',
      content: `Find ${formattedBreed.value.toLowerCase()} for sale in ${formattedLocation.value}. Browse our selection of quality ${formattedBreed.value.toLowerCase()} listings.`
    }
  ]
})
</script>
