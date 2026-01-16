<template>
  <div>
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary-500 to-primary-700 text-white py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-5xl font-bold mb-4">
          Find Your Perfect Pet Companion
        </h1>
        <p class="text-xl mb-8 text-primary-100">
          Browse thousands of dogs for sale, adoption, and stud services across the UK
        </p>
        <NuxtLink
          to="/dogs"
          class="inline-block bg-white text-primary-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition"
        >
          Browse Dogs
        </NuxtLink>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">
          Browse by Category
        </h2>
        <div v-if="categories.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <NuxtLink
            v-for="category in categories"
            :key="category.id"
            :to="`/${category.slug}`"
            class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition text-center"
          >
            <div class="text-5xl mb-4">{{ category.icon }}</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">
              {{ category.name }}
            </h3>
            <p class="text-gray-600 text-sm mb-4">
              {{ category.description }}
            </p>
            <p class="text-primary-600 font-medium">
              {{ category.listings_count }} listings
            </p>
          </NuxtLink>
        </div>
        <div v-else class="text-center text-gray-500">
          Loading categories...
        </div>
      </div>
    </section>

    <!-- Popular Breeds Section -->
    <section v-if="popularBreeds.length" class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">
          Popular Dog Breeds
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <NuxtLink
            v-for="breed in popularBreeds"
            :key="breed.id"
            :to="`/dogs?breed_id=${breed.id}`"
            class="p-4 border border-gray-200 rounded-lg hover:border-primary-500 hover:shadow-md transition text-center"
          >
            <p class="font-medium text-gray-900">{{ breed.name }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ breed.listings_count || 0 }} listings</p>
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Recent Listings -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
          <h2 class="text-3xl font-bold text-gray-900">
            Recent Listings
          </h2>
          <NuxtLink to="/dogs" class="text-primary-600 hover:text-primary-700 font-medium">
            View All →
          </NuxtLink>
        </div>
        <div v-if="recentListings.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <ListingCard
            v-for="listing in recentListings"
            :key="listing.id"
            :listing="listing"
          />
        </div>
        <div v-else class="text-center text-gray-500 py-8">
          No listings yet. Be the first to post!
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-primary-600 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">
          Have a pet to list?
        </h2>
        <p class="text-xl mb-8 text-primary-100">
          Create a listing in minutes and reach thousands of potential buyers
        </p>
        <NuxtLink
          v-if="isAuthenticated"
          to="/listings/create"
          class="inline-block bg-white text-primary-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition"
        >
          Post a Listing
        </NuxtLink>
        <NuxtLink
          v-else
          to="/auth/register"
          class="inline-block bg-white text-primary-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition"
        >
          Sign Up to Post
        </NuxtLink>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
const { fetchCategories, fetchBreeds } = useCategories()
const { fetchListings } = useListings()
const { isAuthenticated } = useAuth()

const categories = ref([])
const popularBreeds = ref([])
const recentListings = ref([])

onMounted(async () => {
  // Fetch categories
  categories.value = await fetchCategories()

  // Fetch popular dog breeds (assuming Dogs is category ID 1)
  if (categories.value.length > 0) {
    const dogsCategory = categories.value.find(c => c.slug === 'dogs')
    if (dogsCategory) {
      const breeds = await fetchBreeds(dogsCategory.id)
      popularBreeds.value = breeds.slice(0, 12) // Show first 12 breeds
    }
  }

  // Fetch recent listings
  const result = await fetchListings({ per_page: 8, sort_by: 'recent' })
  recentListings.value = result.data || []
})
</script>
