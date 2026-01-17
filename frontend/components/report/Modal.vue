<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
    @click="close"
  >
    <div
      class="bg-white rounded-lg shadow-xl max-w-md w-full p-6"
      @click.stop
    >
      <!-- Header -->
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold text-gray-900">Report Listing</h3>
        <button
          @click="close"
          class="text-gray-400 hover:text-gray-600"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="mb-4 bg-red-50 border border-red-200 rounded-md p-3">
        <p class="text-sm text-red-800">{{ error }}</p>
      </div>

      <!-- Success Message -->
      <div v-if="success" class="mb-4 bg-green-50 border border-green-200 rounded-md p-3">
        <p class="text-sm text-green-800">{{ success }}</p>
      </div>

      <!-- Form -->
      <form v-if="!success" @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Reason for reporting
          </label>
          <select
            v-model="reason"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
            <option value="">Select a reason</option>
            <option value="spam">Spam or misleading content</option>
            <option value="scam">Suspected scam</option>
            <option value="inappropriate">Inappropriate content</option>
            <option value="misleading">Misleading information</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Description
          </label>
          <textarea
            v-model="description"
            rows="4"
            required
            minlength="10"
            maxlength="500"
            placeholder="Please provide details about why you're reporting this listing..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
          ></textarea>
          <p class="text-xs text-gray-500 mt-1">
            {{ description.length }}/500 characters (minimum 10)
          </p>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
          <p class="text-xs text-yellow-800">
            Our team will review this report. False reports may result in account restrictions.
          </p>
        </div>

        <div class="flex gap-3">
          <button
            type="button"
            @click="close"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="submitting || !reason || description.length < 10"
            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ submitting ? 'Submitting...' : 'Submit Report' }}
          </button>
        </div>
      </form>

      <!-- Close button after success -->
      <button
        v-else
        @click="close"
        class="w-full px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition"
      >
        Close
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  isOpen: boolean
  listingId: number | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'submitted'): void
}>()

const { submitReport } = useReports()

const reason = ref('')
const description = ref('')
const submitting = ref(false)
const error = ref('')
const success = ref('')

const handleSubmit = async () => {
  if (!props.listingId || !reason.value || description.value.length < 10) return

  submitting.value = true
  error.value = ''
  success.value = ''

  const result = await submitReport(props.listingId, reason.value, description.value)

  if (result.success) {
    success.value = result.message || 'Report submitted successfully!'
    emit('submitted')
    setTimeout(() => {
      close()
    }, 2000)
  } else {
    error.value = typeof result.error === 'string' ? result.error : 'Failed to submit report. Please try again.'
  }

  submitting.value = false
}

const close = () => {
  reason.value = ''
  description.value = ''
  error.value = ''
  success.value = ''
  emit('close')
}

// Reset form when modal closes
watch(() => props.isOpen, (newVal) => {
  if (!newVal) {
    reason.value = ''
    description.value = ''
    error.value = ''
    success.value = ''
  }
})
</script>
