export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  
  modules: ['@nuxtjs/tailwindcss'],
  
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || '/api'
    }
  },

  nitro: {
    devProxy: {
      '/api': {
        target: 'http://localhost:8000/api',
        changeOrigin: true
      }
    },
    routeRules: {
      '/api/**': { 
        proxy: 'http://localhost:8000/api/**'
      }
    }
  },

  vite: {
    server: {
      host: '0.0.0.0',
      port: 5000,
      hmr: {
        clientPort: 5000
      }
    }
  }
})
