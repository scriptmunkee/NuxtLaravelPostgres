// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },

  // Application configuration
  app: {
    head: {
      title: 'Pet Listing - Find Your Perfect Pet',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Browse and find your perfect pet companion' }
      ]
    }
  },

  // Runtime configuration for API URL
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api'
    }
  },

  // CSS configuration
  css: [],

  // Modules
  modules: []
})
