<template>
  <div v-if="loading" class="min-h-screen flex items-center justify-center">
    <div class="text-gray-500">Loading listing...</div>
  </div>

  <div v-else-if="listing" class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb -->
      <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600">
          <li><NuxtLink to="/" class="hover:text-primary-600">Home</NuxtLink></li>
          <li>/</li>
          <li><NuxtLink :to="`/${listing.category?.slug || 'dogs'}`" class="hover:text-primary-600">{{ listing.category?.name || 'Dogs' }}</NuxtLink></li>
          <li>/</li>
          <li class="text-gray-900">{{ listing.title }}</li>
        </ol>
      </nav>

      <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
          <!-- Image Gallery -->
          <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="relative h-96 bg-gray-200">
              <img
                v-if="currentImage"
                :src="`/api/storage/${currentImage.image_path}`"
                :alt="listing.title"
                class="w-full h-full object-cover cursor-pointer"
                @click="openLightbox"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                <span class="text-8xl">🐕</span>
              </div>

              <!-- Image Navigation Arrows -->
              <button
                v-if="listing.images?.length > 1"
                @click="previousImage"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition"
              >
                ←
              </button>
              <button
                v-if="listing.images?.length > 1"
                @click="nextImage"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition"
              >
                →
              </button>
            </div>

            <!-- Thumbnail Strip -->
            <div v-if="listing.images?.length > 1" class="p-4 flex gap-2 overflow-x-auto">
              <img
                v-for="(image, index) in listing.images"
                :key="image.id"
                :src="`/api/storage/${image.image_path}`"
                :alt="`${listing.title} - Image ${index + 1}`"
                @click="currentImageIndex = index"
                :class="[
                  'w-20 h-20 object-cover rounded cursor-pointer border-2 transition',
                  index === currentImageIndex ? 'border-primary-500' : 'border-transparent hover:border-gray-300'
                ]"
              />
            </div>
          </div>

          <!-- Listing Details -->
          <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
              <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ listing.title }}</h1>
                <div class="flex items-center gap-3 text-sm text-gray-600">
                  <span v-if="listing.breed" class="font-medium">{{ listing.breed.name }}</span>
                  <span v-if="listing.gender">{{ genderIcon }} {{ listing.gender }}</span>
                  <span v-if="listing.age_years || listing.age_months">{{ age }}</span>
                </div>
              </div>
              <span
                :class="[
                  'px-3 py-1 text-sm font-semibold rounded',
                  listingTypeBadgeClass
                ]"
              >
                {{ listingTypeLabel }}
              </span>
            </div>

            <!-- Price -->
            <div class="mb-6">
              <div v-if="listing.price" class="text-4xl font-bold text-primary-600">
                £{{ formatPrice(listing.price) }}
              </div>
              <div v-else class="text-2xl font-semibold text-gray-600">
                Contact for price
              </div>
            </div>

            <!-- Key Details Grid -->
            <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
              <div>
                <div class="text-sm text-gray-600">Breed</div>
                <div class="font-medium text-gray-900">{{ listing.breed?.name || 'Not specified' }}</div>
              </div>
              <div>
                <div class="text-sm text-gray-600">Gender</div>
                <div class="font-medium text-gray-900">{{ genderIcon }} {{ listing.gender || 'Unknown' }}</div>
              </div>
              <div>
                <div class="text-sm text-gray-600">Age</div>
                <div class="font-medium text-gray-900">{{ age }}</div>
              </div>
              <div>
                <div class="text-sm text-gray-600">Location</div>
                <div class="font-medium text-gray-900">
                  📍 {{ listing.location ? `${listing.location.city}, ${listing.location.county}` : 'Not specified' }}
                </div>
              </div>
            </div>

            <!-- Description -->
            <div>
              <h2 class="text-xl font-semibold text-gray-900 mb-3">Description</h2>
              <div class="prose max-w-none text-gray-700 whitespace-pre-line">
                {{ listing.description }}
              </div>
            </div>
          </div>

          <!-- Additional Info -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Additional Information</h2>
            <div class="space-y-3 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Listed on</span>
                <span class="font-medium text-gray-900">{{ formatDate(listing.published_at || listing.created_at) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Listing ID</span>
                <span class="font-medium text-gray-900">#{{ listing.id }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Views</span>
                <span class="font-medium text-gray-900">{{ listing.views_count || 0 }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 mt-6 lg:mt-0">
          <!-- Seller Card -->
          <div class="bg-white rounded-lg shadow-md p-6 mb-6 sticky top-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Seller Information</h3>

            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                {{ listing.user?.name?.charAt(0).toUpperCase() || 'U' }}
              </div>
              <div class="ml-3">
                <div class="font-medium text-gray-900">{{ listing.user?.name || 'Anonymous' }}</div>
                <div v-if="listing.user?.is_verified" class="text-xs text-green-600 flex items-center">
                  ✓ Verified Seller
                </div>
              </div>
            </div>

            <div v-if="listing.location" class="text-sm text-gray-600 mb-4">
              📍 {{ listing.location.city }}, {{ listing.location.county }}
            </div>

            <!-- Contact Buttons -->
            <div class="space-y-3">
              <button
                v-if="isAuthenticated && listing.user_id !== user?.id"
                @click="openMessageModal"
                class="w-full bg-primary-600 text-white py-3 px-4 rounded-md font-semibold hover:bg-primary-700 transition"
              >
                Send Message
              </button>
              <button
                v-else-if="!isAuthenticated"
                @click="navigateTo('/auth/login')"
                class="w-full bg-primary-600 text-white py-3 px-4 rounded-md font-semibold hover:bg-primary-700 transition"
              >
                Login to Contact
              </button>
              <button
                v-if="listing.user?.phone"
                class="w-full bg-green-600 text-white py-3 px-4 rounded-md font-semibold hover:bg-green-700 transition"
              >
                Call Seller
              </button>

              <button
                v-if="isAuthenticated"
                @click="toggleFavorite"
                :class="[
                  'w-full py-3 px-4 rounded-md font-semibold transition border',
                  isFavorited
                    ? 'bg-red-50 text-red-600 border-red-600 hover:bg-red-100'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                ]"
              >
                {{ isFavorited ? '❤️ Saved' : '🤍 Save' }}
              </button>
            </div>

            <!-- Safety Tip -->
            <div class="mt-6 p-4 bg-yellow-50 rounded-md">
              <h4 class="text-sm font-semibold text-yellow-800 mb-2">Safety Tip</h4>
              <p class="text-xs text-yellow-700">
                Never send money before meeting the seller and seeing the pet in person.
              </p>
            </div>

            <!-- Report Button -->
            <button
              @click="showReportModal = true"
              class="w-full mt-4 text-sm text-red-600 hover:text-red-700"
            >
              Report this listing
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Report Modal -->
    <ReportModal
      :is-open="showReportModal"
      :listing-id="listing?.id"
      @close="showReportModal = false"
      @submitted="handleReportSubmitted"
    />
  </div>

  <div v-else class="min-h-screen flex items-center justify-center">
    <div class="text-center">
      <div class="text-6xl mb-4">🐕</div>
      <h2 class="text-2xl font-semibold text-gray-900 mb-2">Listing not found</h2>
      <p class="text-gray-600 mb-6">This listing may have been removed or doesn't exist.</p>
      <NuxtLink to="/" class="inline-block bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 transition">
        Go to Homepage
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const router = useRouter()
const { fetchListing } = useListings()
const { isAuthenticated, user } = useAuth()
const { checkFavorite, toggleFavorite: toggleFav } = useFavorites()

const listing = ref(null)
const loading = ref(true)
const currentImageIndex = ref(0)
const isFavorited = ref(false)
const showMessageModal = ref(false)
const showReportModal = ref(false)

// Fetch listing data
onMounted(async () => {
  try {
    const slug = route.params.slug as string
    listing.value = await fetchListing(slug)

    // Check if favorited
    if (isAuthenticated.value && listing.value?.id) {
      isFavorited.value = await checkFavorite(listing.value.id)
    }
  } catch (error) {
    console.error('Error fetching listing:', error)
  } finally {
    loading.value = false
  }
})

const currentImage = computed(() => {
  if (!listing.value?.images?.length) return listing.value?.primary_image
  return listing.value.images[currentImageIndex.value]
})

const listingTypeLabel = computed(() => {
  const labels = {
    sale: 'For Sale',
    adoption: 'Adoption',
    stud: 'Stud Service',
  }
  return labels[listing.value?.listing_type] || 'For Sale'
})

const listingTypeBadgeClass = computed(() => {
  const classes = {
    sale: 'bg-primary-500 text-white',
    adoption: 'bg-green-500 text-white',
    stud: 'bg-purple-500 text-white',
  }
  return classes[listing.value?.listing_type] || 'bg-primary-500 text-white'
})

const genderIcon = computed(() => {
  const icons = {
    male: '♂',
    female: '♀',
    unknown: '?',
  }
  return icons[listing.value?.gender] || '?'
})

const age = computed(() => {
  if (!listing.value) return 'Age not specified'

  const parts = []
  if (listing.value.age_years) {
    parts.push(`${listing.value.age_years} ${listing.value.age_years === 1 ? 'year' : 'years'}`)
  }
  if (listing.value.age_months) {
    parts.push(`${listing.value.age_months} ${listing.value.age_months === 1 ? 'month' : 'months'}`)
  }

  return parts.length > 0 ? parts.join(' ') : 'Age not specified'
})

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-GB').format(price)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const previousImage = () => {
  if (!listing.value?.images?.length) return
  currentImageIndex.value = (currentImageIndex.value - 1 + listing.value.images.length) % listing.value.images.length
}

const nextImage = () => {
  if (!listing.value?.images?.length) return
  currentImageIndex.value = (currentImageIndex.value + 1) % listing.value.images.length
}

const openLightbox = () => {
  // TODO: Implement lightbox modal for full-screen image viewing
  console.log('Open lightbox')
}

const openMessageModal = () => {
  showMessageModal.value = true
}

const toggleFavorite = async () => {
  if (!isAuthenticated.value) {
    router.push('/auth/login')
    return
  }

  if (!listing.value?.id) return

  const result = await toggleFav(listing.value.id, isFavorited.value)
  if (result.success) {
    isFavorited.value = result.favorited
  }
}

const handleReportSubmitted = () => {
  // Report submitted successfully
  // Could show a toast notification here
  console.log('Report submitted successfully')
}

// SEO Meta tags
useHead({
  title: listing.value?.title || 'Loading...',
  meta: [
    { name: 'description', content: listing.value?.description?.substring(0, 160) || '' },
    { property: 'og:title', content: listing.value?.title || '' },
    { property: 'og:description', content: listing.value?.description?.substring(0, 160) || '' },
    { property: 'og:image', content: listing.value?.primary_image?.image_path || '' },
  ],
})
</script>
