export const useApi = () => {
  const config = useRuntimeConfig()
  const authToken = useCookie('auth_token')

  const api = $fetch.create({
    baseURL: config.public.apiBase as string,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
    onRequest({ options }) {
      // Add authorization header if token exists
      if (authToken.value) {
        options.headers = {
          ...options.headers,
          Authorization: `Bearer ${authToken.value}`,
        }
      }
    },
    onResponseError({ response }) {
      // Handle errors globally
      console.error('API Error:', response.status, response._data)

      // If unauthorized, clear token and redirect to login
      if (response.status === 401) {
        authToken.value = null
        navigateTo('/auth/login')
      }
    },
  })

  return {
    api,
  }
}
