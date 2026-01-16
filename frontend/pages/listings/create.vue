<template>
  <div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Create a Listing</h1>
        <p class="text-gray-600">Fill out the form below to list your pet for sale, adoption, or stud service</p>
      </div>

      <!-- Progress Steps -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div
            v-for="(stepName, index) in steps"
            :key="index"
            class="flex items-center flex-1"
          >
            <div class="flex items-center">
              <div
                :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center font-semibold',
                  currentStep >= index
                    ? 'bg-primary-600 text-white'
                    : 'bg-gray-300 text-gray-600'
                ]"
              >
                {{ index + 1 }}
              </div>
              <span
                :class="[
                  'ml-2 text-sm font-medium',
                  currentStep >= index ? 'text-gray-900' : 'text-gray-500'
                ]"
              >
                {{ stepName }}
              </span>
            </div>
            <div
              v-if="index < steps.length - 1"
              :class="[
                'flex-1 h-1 mx-4',
                currentStep > index ? 'bg-primary-600' : 'bg-gray-300'
              ]"
            ></div>
          </div>
        </div>
      </div>

      <!-- Error Messages -->
      <div v-if="errors.length > 0" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <h3 class="text-sm font-medium text-red-800 mb-2">Please correct the following errors:</h3>
        <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
          <li v-for="error in errors" :key="error">{{ error }}</li>
        </ul>
      </div>

      <!-- Form Steps -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Step 1: Basic Information -->
        <div v-show="currentStep === 0" class="space-y-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select
              v-model="formData.category_id"
              @change="onCategoryChange"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="">Select a category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div v-if="formData.category_id">
            <label class="block text-sm font-medium text-gray-700 mb-2">Breed *</label>
            <select
              v-model="formData.breed_id"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="">Select a breed</option>
              <option v-for="breed in breeds" :key="breed.id" :value="breed.id">
                {{ breed.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Listing Type *</label>
            <div class="grid grid-cols-3 gap-4">
              <label
                v-for="type in listingTypes"
                :key="type.value"
                :class="[
                  'border-2 rounded-lg p-4 cursor-pointer transition text-center',
                  formData.listing_type === type.value
                    ? 'border-primary-500 bg-primary-50'
                    : 'border-gray-300 hover:border-gray-400'
                ]"
              >
                <input
                  v-model="formData.listing_type"
                  type="radio"
                  :value="type.value"
                  class="sr-only"
                />
                <div class="text-2xl mb-2">{{ type.icon }}</div>
                <div class="font-medium text-gray-900">{{ type.label }}</div>
                <div class="text-xs text-gray-600 mt-1">{{ type.description }}</div>
              </label>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
            <input
              v-model="formData.title"
              type="text"
              required
              placeholder="e.g., Beautiful Labrador Puppy for Sale"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
            />
            <p class="mt-1 text-xs text-gray-500">Create a catchy title that describes your pet</p>
          </div>
        </div>

        <!-- Step 2: Details -->
        <div v-show="currentStep === 1" class="space-y-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Pet Details</h2>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
            <textarea
              v-model="formData.description"
              rows="6"
              required
              placeholder="Provide detailed information about your pet including temperament, health, training, etc."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500">Minimum 50 characters</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Age (Years)</label>
              <input
                v-model.number="formData.age_years"
                type="number"
                min="0"
                max="30"
                placeholder="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Age (Months)</label>
              <input
                v-model.number="formData.age_months"
                type="number"
                min="0"
                max="11"
                placeholder="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
            <div class="flex gap-4">
              <label class="flex items-center">
                <input
                  v-model="formData.gender"
                  type="radio"
                  value="male"
                  class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                />
                <span class="ml-2 text-gray-700">♂ Male</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="formData.gender"
                  type="radio"
                  value="female"
                  class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                />
                <span class="ml-2 text-gray-700">♀ Female</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="formData.gender"
                  type="radio"
                  value="unknown"
                  class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                />
                <span class="ml-2 text-gray-700">? Unknown</span>
              </label>
            </div>
          </div>

          <div v-if="formData.listing_type !== 'adoption'">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Price (£) {{ formData.listing_type === 'sale' ? '*' : '' }}
            </label>
            <input
              v-model.number="formData.price"
              type="number"
              min="0"
              step="0.01"
              placeholder="0.00"
              :required="formData.listing_type === 'sale'"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
            />
            <p class="mt-1 text-xs text-gray-500">Leave blank if you prefer buyers to contact you</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
            <select
              v-model="formData.location_id"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="">Select a location</option>
              <option v-for="location in locations" :key="location.id" :value="location.id">
                {{ location.city }}, {{ location.county }}
              </option>
            </select>
          </div>
        </div>

        <!-- Step 3: Images -->
        <div v-show="currentStep === 2" class="space-y-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload Images</h2>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Pet Photos * (At least 1 image required)
            </label>
            <div
              class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-primary-500 transition cursor-pointer"
              @click="triggerFileInput"
              @dragover.prevent
              @drop.prevent="handleDrop"
            >
              <input
                ref="fileInput"
                type="file"
                multiple
                accept="image/*"
                class="hidden"
                @change="handleFileSelect"
              />
              <div class="text-4xl text-gray-400 mb-2">📷</div>
              <p class="text-gray-600 mb-1">Click to upload or drag and drop</p>
              <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB each</p>
            </div>
          </div>

          <!-- Image Previews -->
          <div v-if="imagePreviews.length > 0" class="grid grid-cols-3 gap-4">
            <div
              v-for="(preview, index) in imagePreviews"
              :key="index"
              class="relative group"
            >
              <img
                :src="preview"
                alt="Preview"
                class="w-full h-32 object-cover rounded-lg border-2"
                :class="index === 0 ? 'border-primary-500' : 'border-gray-300'"
              />
              <div
                v-if="index === 0"
                class="absolute top-2 left-2 bg-primary-500 text-white text-xs px-2 py-1 rounded"
              >
                Primary
              </div>
              <button
                @click="removeImage(index)"
                class="absolute top-2 right-2 bg-red-500 text-white w-6 h-6 rounded-full opacity-0 group-hover:opacity-100 transition"
              >
                ×
              </button>
            </div>
          </div>

          <p class="text-sm text-gray-600">
            💡 Tip: The first image will be used as the primary image. You can upload up to 10 images.
          </p>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
          <button
            v-if="currentStep > 0"
            @click="previousStep"
            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
          >
            Previous
          </button>
          <div v-else></div>

          <button
            v-if="currentStep < steps.length - 1"
            @click="nextStep"
            class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition"
          >
            Next
          </button>
          <button
            v-else
            @click="submitListing"
            :disabled="submitting"
            class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ submitting ? 'Creating...' : 'Create Listing' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const { fetchCategories, fetchBreeds } = useCategories()
const { fetchLocations } = useLocations()
const { createListing, uploadImages } = useListings()
const router = useRouter()

const steps = ['Basic Info', 'Details', 'Images']
const currentStep = ref(0)
const errors = ref<string[]>([])
const submitting = ref(false)

const listingTypes = [
  { value: 'sale', label: 'For Sale', icon: '💰', description: 'Sell your pet' },
  { value: 'adoption', label: 'Adoption', icon: '❤️', description: 'Find a new home' },
  { value: 'stud', label: 'Stud', icon: '🐕', description: 'Breeding service' },
]

const categories = ref([])
const breeds = ref([])
const locations = ref([])

const formData = ref({
  category_id: '',
  breed_id: '',
  listing_type: 'sale',
  title: '',
  description: '',
  age_years: 0,
  age_months: 0,
  gender: 'male',
  price: null,
  location_id: '',
})

const selectedFiles = ref<File[]>([])
const imagePreviews = ref<string[]>([])
const fileInput = ref<HTMLInputElement | null>(null)

// Load initial data
onMounted(async () => {
  categories.value = await fetchCategories()
  locations.value = await fetchLocations()
})

const onCategoryChange = async () => {
  if (formData.value.category_id) {
    breeds.value = await fetchBreeds(Number(formData.value.category_id))
    formData.value.breed_id = ''
  }
}

const nextStep = () => {
  errors.value = []

  // Validate current step
  if (currentStep.value === 0) {
    if (!formData.value.category_id) errors.value.push('Please select a category')
    if (!formData.value.breed_id) errors.value.push('Please select a breed')
    if (!formData.value.listing_type) errors.value.push('Please select a listing type')
    if (!formData.value.title) errors.value.push('Please enter a title')
  } else if (currentStep.value === 1) {
    if (!formData.value.description || formData.value.description.length < 50) {
      errors.value.push('Description must be at least 50 characters')
    }
    if (!formData.value.gender) errors.value.push('Please select a gender')
    if (!formData.value.location_id) errors.value.push('Please select a location')
    if (formData.value.listing_type === 'sale' && !formData.value.price) {
      errors.value.push('Price is required for sale listings')
    }
  } else if (currentStep.value === 2) {
    if (selectedFiles.value.length === 0) {
      errors.value.push('Please upload at least one image')
    }
  }

  if (errors.value.length === 0) {
    currentStep.value++
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const previousStep = () => {
  currentStep.value--
  errors.value = []
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files) {
    addFiles(Array.from(target.files))
  }
}

const handleDrop = (event: DragEvent) => {
  if (event.dataTransfer?.files) {
    addFiles(Array.from(event.dataTransfer.files))
  }
}

const addFiles = (files: File[]) => {
  files.forEach(file => {
    if (file.type.startsWith('image/') && selectedFiles.value.length < 10) {
      selectedFiles.value.push(file)

      const reader = new FileReader()
      reader.onload = (e) => {
        imagePreviews.value.push(e.target?.result as string)
      }
      reader.readAsDataURL(file)
    }
  })
}

const removeImage = (index: number) => {
  selectedFiles.value.splice(index, 1)
  imagePreviews.value.splice(index, 1)
}

const submitListing = async () => {
  errors.value = []
  submitting.value = true

  try {
    // Create listing
    const listingResult = await createListing({
      ...formData.value,
      category_id: Number(formData.value.category_id),
      breed_id: Number(formData.value.breed_id),
      location_id: Number(formData.value.location_id),
      status: 'active',
      published_at: new Date().toISOString(),
    })

    if (!listingResult.success) {
      errors.value.push('Failed to create listing. Please try again.')
      submitting.value = false
      return
    }

    // Upload images
    if (selectedFiles.value.length > 0) {
      const uploadResult = await uploadImages(listingResult.listing.id, selectedFiles.value)
      if (!uploadResult.success) {
        errors.value.push('Listing created but failed to upload images. You can add them later.')
      }
    }

    // Redirect to the new listing
    router.push(`/listings/${listingResult.listing.slug}`)
  } catch (error: any) {
    errors.value.push(error.message || 'An error occurred. Please try again.')
  } finally {
    submitting.value = false
  }
}
</script>
