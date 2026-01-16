<template>
  <div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dogs for Sale</h1>
        <p class="text-lg text-gray-600">
          Browse {{ totalListings }} dogs for sale, adoption, and stud services across the UK
        </p>
      </div>

      <div class="lg:grid lg:grid-cols-4 lg:gap-8">
        <!-- Filters Sidebar -->
        <aside class="lg:col-span-1 mb-8 lg:mb-0">
          <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>

            <!-- Listing Type Filter -->
            <div class="mb-6">
              <h3 class="text-sm font-medium text-gray-900 mb-3">Listing Type</h3>
              <div class="space-y-2">
                <label class="flex items-center">
                  <input
                    v-model="filters.listing_type"
                    type="radio"
                    value=""
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">All Types</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="filters.listing_type"
                    type="radio"
                    value="sale"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">For Sale</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="filters.listing_type"
                    type="radio"
                    value="adoption"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Adoption</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="filters.listing_type"
                    type="radio"
                    value="stud"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Stud Service</span>
                </label>
              </div>
            </div>

            <!-- Breed Filter -->
            <div class="mb-6">
              <h3 class="text-sm font-medium text-gray-900 mb-3">Breed</h3>
              <select
                v-model="filters.breed_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="">All Breeds</option>
                <option v-for="breed in breeds" :key="breed.id" :value="breed.id">
                  {{ breed.name }}
                </option>
              </select>
            </div>

            <!-- Gender Filter -->
            <div class="mb-6">
              <h3 class="text-sm font-medium text-gray-900 mb-3">Gender</h3>
              <div class="space-y-2">
                <label class="flex items-center">
                  <input
                    v-model="filters.gender"
                    type="radio"
                    value=""
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">All</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="filters.gender"
                    type="radio"
                    value="male"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Male ♂</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="filters.gender"
                    type="radio"
                    value="female"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Female ♀</span>
                </label>
              </div>
            </div>

            <!-- Price Range -->
            <div class="mb-6">
              <h3 class="text-sm font-medium text-gray-900 mb-3">Price Range</h3>
              <div class="space-y-3">
                <div>
                  <label class="text-xs text-gray-600">Min Price (£)</label>
                  <input
                    v-model.number="filters.min_price"
                    type="number"
                    min="0"
                    placeholder="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                  />
                </div>
                <div>
                  <label class="text-xs text-gray-600">Max Price (£)</label>
                  <input
                    v-model.number="filters.max_price"
                    type="number"
                    min="0"
                    placeholder="No limit"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                  />
                </div>
              </div>
            </div>

            <!-- Location Filter -->
            <div class="mb-6">
              <h3 class="text-sm font-medium text-gray-900 mb-3">Location</h3>
              <input
                v-model="filters.location"
                type="text"
                placeholder="City or County"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
              />
            </div>

            <!-- Apply Filters Button -->
            <button
              @click="applyFilters"
              class="w-full bg-primary-600 text-white py-2 px-4 rounded-md hover:bg-primary-700 transition"
            >
              Apply Filters
            </button>

            <!-- Clear Filters -->
            <button
              @click="clearFilters"
              class="w-full mt-2 bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition"
            >
              Clear All
            </button>
          </div>
        </aside>

        <!-- Listings Grid -->
        <main class="lg:col-span-3">
          <!-- Sort and View Options -->
          <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Sort by:</span>
                <select
                  v-model="filters.sort_by"
                  @change="applyFilters"
                  class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="recent">Most Recent</option>
                  <option value="price_low">Price: Low to High</option>
                  <option value="price_high">Price: High to Low</option>
                  <option value="popular">Most Popular</option>
                </select>
              </div>
              <div class="text-sm text-gray-600">
                Showing {{ listings.length }} of {{ totalListings }} results
              </div>
            </div>
          </div>

          <!-- Listings Grid -->
          <div v-if="loading" class="text-center py-12">
            <div class="text-gray-500">Loading listings...</div>
          </div>

          <div v-else-if="listings.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <ListingCard
              v-for="listing in listings"
              :key="listing.id"
              :listing="listing"
            />
          </div>

          <div v-else class="bg-white rounded-lg shadow-md p-12 text-center">
            <div class="text-4xl mb-4">🐕</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No listings found</h3>
            <p class="text-gray-600 mb-4">Try adjusting your filters to see more results</p>
            <button
              @click="clearFilters"
              class="inline-block bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 transition"
            >
              Clear Filters
            </button>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="mt-8 flex justify-center">
            <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>

              <button
                v-for="page in displayedPages"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                  page === currentPage
                    ? 'z-10 bg-primary-50 border-primary-500 text-primary-600'
                    : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>

              <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Next
              </button>
            </nav>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { fetchListings } = useListings()
const { fetchBreeds } = useCategories()
const route = useRoute()

const listings = ref([])
const breeds = ref([])
const loading = ref(false)
const totalListings = ref(0)
const currentPage = ref(1)
const totalPages = ref(1)

const filters = ref({
  listing_type: route.query.listing_type || '',
  breed_id: route.query.breed_id || '',
  gender: route.query.gender || '',
  min_price: route.query.min_price || null,
  max_price: route.query.max_price || null,
  location: route.query.location || '',
  sort_by: route.query.sort_by || 'recent',
  page: 1,
})

// Fetch breeds for filter
onMounted(async () => {
  // Fetch dogs category breeds (assuming category_id 1 is dogs)
  const categories = await useCategories().fetchCategories()
  const dogsCategory = categories.find(c => c.slug === 'dogs')
  if (dogsCategory) {
    breeds.value = await fetchBreeds(dogsCategory.id)
  }

  // Load listings
  await loadListings()
})

const loadListings = async () => {
  loading.value = true
  try {
    const params = {
      ...filters.value,
      per_page: 12,
    }

    // Remove empty values
    Object.keys(params).forEach(key => {
      if (params[key] === '' || params[key] === null) {
        delete params[key]
      }
    })

    const result = await fetchListings(params)
    listings.value = result.data || []
    totalListings.value = result.total || 0
    currentPage.value = result.current_page || 1
    totalPages.value = result.last_page || 1
  } catch (error) {
    console.error('Error loading listings:', error)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  filters.value.page = 1
  loadListings()
}

const clearFilters = () => {
  filters.value = {
    listing_type: '',
    breed_id: '',
    gender: '',
    min_price: null,
    max_price: null,
    location: '',
    sort_by: 'recent',
    page: 1,
  }
  loadListings()
}

const goToPage = (page: number) => {
  if (page < 1 || page > totalPages.value) return
  filters.value.page = page
  loadListings()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const displayedPages = computed(() => {
  const pages = []
  const maxPages = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxPages / 2))
  let end = Math.min(totalPages.value, start + maxPages - 1)

  if (end - start < maxPages - 1) {
    start = Math.max(1, end - maxPages + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

// Watch for query parameter changes
watch(() => route.query, (newQuery) => {
  filters.value = {
    listing_type: newQuery.listing_type || '',
    breed_id: newQuery.breed_id || '',
    gender: newQuery.gender || '',
    min_price: newQuery.min_price || null,
    max_price: newQuery.max_price || null,
    location: newQuery.location || '',
    sort_by: newQuery.sort_by || 'recent',
    page: Number(newQuery.page) || 1,
  }
  loadListings()
})
</script>
