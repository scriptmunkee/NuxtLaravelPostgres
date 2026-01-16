export const useCategories = () => {
  const { api } = useApi()

  const fetchCategories = async () => {
    try {
      const data = await api('/categories')
      return data.categories || []
    } catch (error) {
      console.error('Error fetching categories:', error)
      return []
    }
  }

  const fetchCategory = async (id: number | string) => {
    try {
      const data = await api(`/categories/${id}`)
      return data.category
    } catch (error) {
      console.error('Error fetching category:', error)
      return null
    }
  }

  const fetchBreeds = async (categoryId: number | string) => {
    try {
      const data = await api(`/categories/${categoryId}/breeds`)
      return data.breeds || []
    } catch (error) {
      console.error('Error fetching breeds:', error)
      return []
    }
  }

  return {
    fetchCategories,
    fetchCategory,
    fetchBreeds,
  }
}
