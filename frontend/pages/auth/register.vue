<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
      <h1 class="text-3xl font-bold text-center mb-6">Register</h1>
      
      <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleRegister">
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
            Name
          </label>
          <input
            v-model="name"
            type="text"
            id="name"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
            Email
          </label>
          <input
            v-model="email"
            type="email"
            id="email"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
            I am a
          </label>
          <select
            v-model="role"
            id="role"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
          </select>
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
            Password
          </label>
          <input
            v-model="password"
            type="password"
            id="password"
            required
            minlength="8"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div class="mb-6">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
            Confirm Password
          </label>
          <input
            v-model="passwordConfirmation"
            type="password"
            id="password_confirmation"
            required
            minlength="8"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600 disabled:opacity-50"
        >
          {{ loading ? 'Registering...' : 'Register' }}
        </button>
      </form>

      <p class="text-center mt-4 text-gray-600">
        Already have an account? 
        <NuxtLink to="/auth/login" class="text-blue-500 hover:underline">Login</NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
const { register } = useAuth()
const router = useRouter()

const name = ref('')
const email = ref('')
const role = ref('buyer')
const password = ref('')
const passwordConfirmation = ref('')
const errorMessage = ref('')
const loading = ref(false)

const handleRegister = async () => {
  loading.value = true
  errorMessage.value = ''
  
  const { success, error } = await register(
    name.value,
    email.value,
    password.value,
    passwordConfirmation.value,
    role.value
  )
  
  if (success) {
    router.push('/')
  } else {
    errorMessage.value = error || 'Registration failed'
  }
  
  loading.value = false
}
</script>
