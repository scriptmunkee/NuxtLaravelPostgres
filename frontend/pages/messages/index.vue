<template>
  <div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Messages</h1>
        <p class="text-gray-600">
          {{ conversations.length }} {{ conversations.length === 1 ? 'conversation' : 'conversations' }}
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="text-gray-500">Loading your messages...</div>
      </div>

      <!-- Conversations List -->
      <div v-else-if="conversations.length > 0" class="bg-white rounded-lg shadow-md overflow-hidden">
        <div
          v-for="conversation in conversations"
          :key="conversation.other_user.id + '_' + conversation.listing.id"
          class="border-b border-gray-200 hover:bg-gray-50 transition cursor-pointer"
          @click="openConversation(conversation)"
        >
          <div class="p-6 flex items-start gap-4">
            <!-- User Avatar -->
            <div class="w-12 h-12 rounded-full bg-primary-500 flex items-center justify-center text-white font-semibold flex-shrink-0">
              {{ conversation.other_user.name.charAt(0).toUpperCase() }}
            </div>

            <!-- Conversation Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between mb-1">
                <div class="flex-1">
                  <h3 class="font-semibold text-gray-900">
                    {{ conversation.other_user.name }}
                  </h3>
                  <p class="text-sm text-gray-600 truncate">
                    Re: {{ conversation.listing.title }}
                  </p>
                </div>
                <span class="text-xs text-gray-500 whitespace-nowrap ml-2">
                  {{ formatRelativeTime(conversation.last_message.created_at) }}
                </span>
              </div>

              <p class="text-sm text-gray-700 truncate">
                {{ conversation.last_message.message }}
              </p>

              <div class="flex items-center gap-2 mt-2">
                <span
                  v-if="conversation.unread_count > 0"
                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800"
                >
                  {{ conversation.unread_count }} unread
                </span>
                <NuxtLink
                  :to="'/listings/' + conversation.listing.slug"
                  @click.stop
                  class="text-xs text-primary-600 hover:text-primary-700"
                >
                  View Listing →
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16 bg-white rounded-lg shadow-md">
        <div class="text-6xl mb-4">💬</div>
        <h2 class="text-2xl font-semibold text-gray-900 mb-2">No messages yet</h2>
        <p class="text-gray-600 mb-6">Start a conversation with a seller</p>
        <NuxtLink
          to="/dogs"
          class="inline-block bg-primary-600 text-white px-6 py-3 rounded-md hover:bg-primary-700 transition"
        >
          Browse Listings
        </NuxtLink>
      </div>
    </div>

    <!-- Conversation Modal -->
    <div
      v-if="selectedConversation"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
      @click="closeConversation"
    >
      <div
        class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] flex flex-col"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
          <div>
            <h3 class="font-semibold text-lg text-gray-900">
              {{ selectedConversation.other_user.name }}
            </h3>
            <p class="text-sm text-gray-600">
              Re: {{ selectedConversation.listing.title }}
            </p>
          </div>
          <button
            @click="closeConversation"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Messages List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
          <div
            v-for="message in selectedConversation.messages"
            :key="message.id"
            :class="[
              'flex',
              message.sender_id === user.id ? 'justify-end' : 'justify-start'
            ]"
          >
            <div
              :class="[
                'max-w-[70%] rounded-lg px-4 py-2',
                message.sender_id === user.id
                  ? 'bg-primary-600 text-white'
                  : 'bg-gray-200 text-gray-900'
              ]"
            >
              <p class="text-sm">{{ message.message }}</p>
              <span
                :class="[
                  'text-xs mt-1 block',
                  message.sender_id === user.id ? 'text-primary-100' : 'text-gray-500'
                ]"
              >
                {{ formatTime(message.created_at) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Message Input -->
        <div class="p-4 border-t border-gray-200">
          <form @submit.prevent="sendNewMessage" class="flex gap-2">
            <input
              v-model="newMessage"
              type="text"
              placeholder="Type a message..."
              class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
              required
            />
            <button
              type="submit"
              :disabled="sending || !newMessage.trim()"
              class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ sending ? 'Sending...' : 'Send' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const { fetchConversations, fetchConversation, sendMessage } = useMessages()
const { user } = useAuth()

const conversations = ref([])
const loading = ref(true)
const selectedConversation = ref(null)
const newMessage = ref('')
const sending = ref(false)

onMounted(async () => {
  await loadConversations()
})

const loadConversations = async () => {
  loading.value = true
  try {
    conversations.value = await fetchConversations()
  } catch (error) {
    console.error('Error loading conversations:', error)
  } finally {
    loading.value = false
  }
}

const openConversation = async (conversation: any) => {
  selectedConversation.value = conversation
  const messages = await fetchConversation(
    conversation.listing.id,
    conversation.other_user.id
  )
  selectedConversation.value.messages = messages
}

const closeConversation = () => {
  selectedConversation.value = null
  newMessage.value = ''
}

const sendNewMessage = async () => {
  if (!selectedConversation.value || !newMessage.value.trim()) return

  sending.value = true
  try {
    const result = await sendMessage(
      selectedConversation.value.listing.id,
      selectedConversation.value.other_user.id,
      newMessage.value
    )

    if (result.success) {
      selectedConversation.value.messages.push(result.message)
      newMessage.value = ''
    }
  } catch (error) {
    console.error('Error sending message:', error)
  } finally {
    sending.value = false
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
    month: 'short'
  })
}

const formatTime = (date: string) => {
  return new Date(date).toLocaleTimeString('en-GB', {
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
