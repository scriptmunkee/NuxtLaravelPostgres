<template>
  <NuxtLink
    :to="`/listings/${listing.slug}`"
    class="block bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow overflow-hidden"
  >
    <!-- Image -->
    <div class="relative h-48 bg-gray-200">
      <img
        v-if="listing.primary_image"
        :src="`/api/storage/${listing.primary_image.image_path}`"
        :alt="listing.title"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
        <span class="text-4xl">🐕</span>
      </div>

      <!-- Listing Type Badge -->
      <span
        class="absolute top-2 right-2 px-2 py-1 text-xs font-semibold rounded"
        :class="listingTypeBadgeClass"
      >
        {{ listingTypeLabel }}
      </span>
    </div>

    <!-- Content -->
    <div class="p-4">
      <!-- Title -->
      <h3 class="font-semibold text-lg text-gray-900 mb-1 line-clamp-1">
        {{ listing.title }}
      </h3>

      <!-- Breed -->
      <p v-if="listing.breed" class="text-sm text-gray-600 mb-2">
        {{ listing.breed.name }}
      </p>

      <!-- Details -->
      <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
        <span v-if="listing.location">
          📍 {{ listing.location.city }}
        </span>
        <span v-if="listing.gender">
          {{ genderIcon }} {{ listing.gender }}
        </span>
      </div>

      <!-- Price -->
      <div class="flex items-center justify-between">
        <span v-if="listing.price" class="text-2xl font-bold text-primary-600">
          £{{ formatPrice(listing.price) }}
        </span>
        <span v-else class="text-lg font-semibold text-gray-600">
          Contact for price
        </span>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
const props = defineProps<{
  listing: any
}>()

const listingTypeLabel = computed(() => {
  const labels = {
    sale: 'For Sale',
    adoption: 'Adoption',
    stud: 'Stud',
  }
  return labels[props.listing.listing_type] || 'For Sale'
})

const listingTypeBadgeClass = computed(() => {
  const classes = {
    sale: 'bg-primary-500 text-white',
    adoption: 'bg-green-500 text-white',
    stud: 'bg-purple-500 text-white',
  }
  return classes[props.listing.listing_type] || 'bg-primary-500 text-white'
})

const genderIcon = computed(() => {
  const icons = {
    male: '♂',
    female: '♀',
    unknown: '?',
  }
  return icons[props.listing.gender] || '?'
})

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-GB').format(price)
}
</script>
