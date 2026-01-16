<template>
  <div class="flex flex-col min-h-screen">
    <LayoutHeader />
    <main class="flex-grow">
      <NuxtPage />
    </main>
    <LayoutFooter />
  </div>
</template>

<script setup lang="ts">
// Initialize auth user if token exists
const { fetchUser, isAuthenticated } = useAuth()

onMounted(async () => {
  if (isAuthenticated.value) {
    await fetchUser()
  }
})
</script>
