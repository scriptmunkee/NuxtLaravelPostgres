export const useMessages = () => {
  const { api } = useApi()

  const fetchConversations = async () => {
    try {
      const data = await api('/messages')
      return data.conversations || []
    } catch (error) {
      console.error('Error fetching conversations:', error)
      return []
    }
  }

  const fetchConversation = async (listingId: number | string, userId: number | string) => {
    try {
      const data = await api(`/messages/listing/${listingId}/user/${userId}`)
      return data.messages || []
    } catch (error) {
      console.error('Error fetching conversation:', error)
      return []
    }
  }

  const sendMessage = async (listingId: number | string, recipientId: number | string, message: string) => {
    try {
      const data = await api('/messages', {
        method: 'POST',
        body: {
          listing_id: listingId,
          recipient_id: recipientId,
          message,
        },
      })
      return { success: true, message: data.message }
    } catch (error: any) {
      console.error('Error sending message:', error)
      return { success: false, error: error.data?.error || 'Failed to send message' }
    }
  }

  const markAsRead = async (messageId: number | string) => {
    try {
      await api(`/messages/${messageId}/read`, {
        method: 'PUT',
      })
      return { success: true }
    } catch (error) {
      console.error('Error marking message as read:', error)
      return { success: false, error }
    }
  }

  return {
    fetchConversations,
    fetchConversation,
    sendMessage,
    markAsRead,
  }
}
