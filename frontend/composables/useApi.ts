export const useApi = () => {
  const config = useRuntimeConfig()
  const authToken = useCookie('auth_token')

  const apiBase = config.public.apiBase

  const request = async (endpoint: string, options: any = {}) => {
    const headers: any = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...options.headers,
    }

    if (authToken.value) {
      headers['Authorization'] = `Bearer ${authToken.value}`
    }

    try {
      const response = await $fetch(`${apiBase}${endpoint}`, {
        ...options,
        headers,
      })
      return { data: response, error: null }
    } catch (error: any) {
      return { data: null, error: error.data || error }
    }
  }

  return {
    get: (endpoint: string, options = {}) => request(endpoint, { method: 'GET', ...options }),
    post: (endpoint: string, body: any, options = {}) => request(endpoint, { method: 'POST', body, ...options }),
    put: (endpoint: string, body: any, options = {}) => request(endpoint, { method: 'PUT', body, ...options }),
    delete: (endpoint: string, options = {}) => request(endpoint, { method: 'DELETE', ...options }),
  }
}
