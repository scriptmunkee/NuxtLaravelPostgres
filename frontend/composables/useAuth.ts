export const useAuth = () => {
  const { api } = useApi()
  const authToken = useCookie('auth_token', {
    maxAge: 60 * 60 * 24 * 30, // 30 days
  })
  const user = useState('user', () => null)

  const login = async (email: string, password: string) => {
    try {
      const data = await api('/login', {
        method: 'POST',
        body: { email, password },
      })

      authToken.value = data.token
      user.value = data.user

      return { success: true, user: data.user }
    } catch (error) {
      return { success: false, error }
    }
  }

  const register = async (userData: {
    name: string
    email: string
    password: string
    password_confirmation: string
    phone?: string
    location_id?: number
  }) => {
    try {
      const data = await api('/register', {
        method: 'POST',
        body: userData,
      })

      authToken.value = data.token
      user.value = data.user

      return { success: true, user: data.user }
    } catch (error) {
      return { success: false, error }
    }
  }

  const logout = async () => {
    try {
      if (authToken.value) {
        await api('/logout', { method: 'POST' })
      }
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      authToken.value = null
      user.value = null
      navigateTo('/auth/login')
    }
  }

  const fetchUser = async () => {
    if (!authToken.value) {
      return null
    }

    try {
      const data = await api('/user')
      user.value = data.user
      return data.user
    } catch (error) {
      authToken.value = null
      user.value = null
      return null
    }
  }

  const updateProfile = async (profileData: any) => {
    try {
      const data = await api('/user/profile', {
        method: 'PUT',
        body: profileData,
      })

      user.value = data.user
      return { success: true, user: data.user }
    } catch (error) {
      return { success: false, error }
    }
  }

  const isAuthenticated = computed(() => !!authToken.value)

  return {
    login,
    register,
    logout,
    fetchUser,
    updateProfile,
    user,
    isAuthenticated,
  }
}
