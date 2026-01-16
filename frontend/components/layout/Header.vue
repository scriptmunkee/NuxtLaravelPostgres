<template>
  <header class="bg-white shadow-sm border-b sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <NuxtLink to="/" class="flex items-center space-x-2">
          <span class="text-2xl">🐾</span>
          <span class="text-xl font-bold text-primary-600">Pet Listing</span>
        </NuxtLink>

        <!-- Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <NuxtLink to="/dogs" class="text-gray-700 hover:text-primary-600 transition">
            Browse Dogs
          </NuxtLink>
          <NuxtLink to="/about" class="text-gray-700 hover:text-primary-600 transition">
            About
          </NuxtLink>
        </nav>

        <!-- Auth & Actions -->
        <div class="flex items-center space-x-4">
          <template v-if="isAuthenticated">
            <NuxtLink
              to="/listings/create"
              class="hidden sm:inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition"
            >
              Post Listing
            </NuxtLink>
            <NuxtLink to="/profile" class="text-gray-700 hover:text-primary-600 transition">
              Profile
            </NuxtLink>
            <button
              @click="handleLogout"
              class="text-gray-700 hover:text-red-600 transition"
            >
              Logout
            </button>
          </template>
          <template v-else>
            <NuxtLink
              to="/auth/login"
              class="text-gray-700 hover:text-primary-600 transition"
            >
              Login
            </NuxtLink>
            <NuxtLink
              to="/auth/register"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition"
            >
              Sign Up
            </NuxtLink>
          </template>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
const { isAuthenticated, logout } = useAuth()

const handleLogout = async () => {
  await logout()
}
</script>
