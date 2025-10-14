export const useAuth = () => {
  const api = useApi()
  const user = useState<any>('user', () => null)
  const authToken = useCookie('auth_token')

  const login = async (email: string, password: string) => {
    const { data, error } = await api.post('/login', { email, password })
    
    if (data) {
      authToken.value = data.token
      user.value = data.user
      return { success: true, error: null }
    }
    
    return { success: false, error: error?.message || 'Login failed' }
  }

  const register = async (name: string, email: string, password: string, password_confirmation: string, role: string) => {
    const { data, error } = await api.post('/register', { 
      name, 
      email, 
      password, 
      password_confirmation,
      role 
    })
    
    if (data) {
      authToken.value = data.token
      user.value = data.user
      return { success: true, error: null }
    }
    
    return { success: false, error: error?.message || 'Registration failed' }
  }

  const logout = async () => {
    await api.post('/logout', {})
    authToken.value = null
    user.value = null
    navigateTo('/auth/login')
  }

  const fetchUser = async () => {
    if (!authToken.value) return
    
    const { data } = await api.get('/user')
    if (data) {
      user.value = data
    }
  }

  const isAuthenticated = computed(() => !!authToken.value)
  const isSeller = computed(() => user.value?.role === 'seller')
  const isBuyer = computed(() => user.value?.role === 'buyer')

  return {
    user,
    login,
    register,
    logout,
    fetchUser,
    isAuthenticated,
    isSeller,
    isBuyer,
  }
}
