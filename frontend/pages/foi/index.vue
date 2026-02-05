<template>
  <div class="foi-dashboard">
    <header class="dashboard-header">
      <h1>FOI Request Tracker</h1>
      <p class="subtitle">WhatDoTheyKnow Status Monitor</p>
    </header>

    <!-- Import Section -->
    <section class="import-section">
      <h2>Track a Request</h2>
      <form @submit.prevent="handleImport" class="import-form">
        <input
          v-model="importUrl"
          type="text"
          placeholder="Paste a WhatDoTheyKnow request URL or slug..."
          class="import-input"
          :disabled="importing"
        />
        <button type="submit" class="btn btn-primary" :disabled="!importUrl.trim() || importing">
          {{ importing ? 'Importing...' : 'Import & Track' }}
        </button>
      </form>
      <p v-if="importError" class="error-msg">{{ importError }}</p>
      <p v-if="importSuccess" class="success-msg">{{ importSuccess }}</p>
    </section>

    <!-- Dashboard Summary -->
    <section v-if="dashboard" class="summary-section">
      <div class="summary-cards">
        <div class="card summary-card">
          <div class="card-value">{{ dashboard.total_tracked }}</div>
          <div class="card-label">Tracked Requests</div>
        </div>
        <div class="card summary-card alert" v-if="dashboard.unacknowledged_updates > 0">
          <div class="card-value">{{ dashboard.unacknowledged_updates }}</div>
          <div class="card-label">New Updates</div>
          <button class="btn btn-small" @click="handleAcknowledgeAll">Acknowledge All</button>
        </div>
        <div class="card summary-card warning" v-if="dashboard.overdue_requests.length > 0">
          <div class="card-value">{{ dashboard.overdue_requests.length }}</div>
          <div class="card-label">Overdue</div>
        </div>
        <div class="card summary-card info" v-if="dashboard.awaiting_action.length > 0">
          <div class="card-value">{{ dashboard.awaiting_action.length }}</div>
          <div class="card-label">Awaiting Action</div>
        </div>
      </div>

      <!-- Status Breakdown -->
      <div v-if="Object.keys(dashboard.status_summary).length > 0" class="status-breakdown">
        <h3>Status Breakdown</h3>
        <div class="status-bars">
          <div
            v-for="(count, status) in dashboard.status_summary"
            :key="status"
            class="status-bar-row"
          >
            <span class="status-badge" :style="{ backgroundColor: foi.statusColor(String(status)) }">
              {{ foi.statusLabel(String(status)) }}
            </span>
            <span class="status-count">{{ count }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Recent Updates -->
    <section v-if="dashboard?.recent_updates?.length" class="updates-section">
      <h2>Recent Status Changes</h2>
      <div class="updates-list">
        <div
          v-for="update in dashboard.recent_updates"
          :key="update.id"
          class="update-item"
          :class="{ unacknowledged: !update.is_acknowledged }"
        >
          <div class="update-header">
            <strong>{{ update.foi_request?.title || 'Unknown Request' }}</strong>
            <span class="update-time">{{ formatDate(update.detected_at) }}</span>
          </div>
          <div class="update-body">
            <span class="status-badge" :style="{ backgroundColor: foi.statusColor(update.old_status || '') }">
              {{ foi.statusLabel(update.old_status || 'Unknown') }}
            </span>
            <span class="arrow">-></span>
            <span class="status-badge" :style="{ backgroundColor: foi.statusColor(update.new_status) }">
              {{ foi.statusLabel(update.new_status) }}
            </span>
            <button
              v-if="!update.is_acknowledged"
              class="btn btn-tiny"
              @click="handleAcknowledge(update.id)"
            >
              Ack
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Overdue Requests -->
    <section v-if="dashboard?.overdue_requests?.length" class="overdue-section">
      <h2>Overdue Requests</h2>
      <div class="request-list">
        <div v-for="req in dashboard.overdue_requests" :key="req.id" class="request-item overdue">
          <div class="request-title">
            <a :href="`https://www.whatdotheyknow.com/request/${req.wdtk_url_title}`" target="_blank" rel="noopener">
              {{ req.title }}
            </a>
          </div>
          <div class="request-meta">
            <span>{{ req.authority_name }}</span>
            <span class="status-badge" :style="{ backgroundColor: foi.statusColor(req.status) }">
              {{ foi.statusLabel(req.status) }}
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- All Tracked Requests -->
    <section class="requests-section">
      <h2>All Tracked Requests</h2>
      <div class="filter-bar">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search requests..."
          class="search-input"
          @input="debouncedFetchRequests"
        />
        <select v-model="statusFilter" @change="loadRequests" class="filter-select">
          <option value="">All Statuses</option>
          <option value="waiting_response">Waiting Response</option>
          <option value="waiting_response_overdue">Overdue</option>
          <option value="waiting_response_very_overdue">Very Overdue</option>
          <option value="successful">Successful</option>
          <option value="partially_successful">Partially Successful</option>
          <option value="rejected">Rejected</option>
          <option value="waiting_clarification">Waiting Clarification</option>
          <option value="internal_review">Internal Review</option>
          <option value="not_held">Not Held</option>
          <option value="error_message">Error</option>
        </select>
      </div>

      <div v-if="requests.length === 0 && !loadingRequests" class="empty-state">
        No tracked requests found. Import one above to get started.
      </div>

      <div v-if="loadingRequests" class="loading">Loading...</div>

      <div v-else class="request-list">
        <div v-for="req in requests" :key="req.id" class="request-item">
          <div class="request-header">
            <div class="request-title">
              <a :href="`https://www.whatdotheyknow.com/request/${req.wdtk_url_title}`" target="_blank" rel="noopener">
                {{ req.title }}
              </a>
            </div>
            <div class="request-actions">
              <button class="btn btn-tiny" @click="handlePoll(req.id)" :disabled="polling === req.id">
                {{ polling === req.id ? '...' : 'Refresh' }}
              </button>
              <button class="btn btn-tiny btn-danger" @click="handleDelete(req.id)">Remove</button>
            </div>
          </div>
          <div class="request-meta">
            <span v-if="req.authority_name" class="authority">{{ req.authority_name }}</span>
            <span class="status-badge" :style="{ backgroundColor: foi.statusColor(req.status) }">
              {{ req.display_status || foi.statusLabel(req.status) }}
            </span>
            <span v-if="req.last_polled_at" class="poll-time">
              Polled: {{ formatDate(req.last_polled_at) }}
            </span>
          </div>
          <div v-if="req.internal_notes" class="request-notes">
            {{ req.internal_notes }}
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
const foi = useFoi()

const dashboard = ref<any>(null)
const requests = ref<any[]>([])
const loadingRequests = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const importUrl = ref('')
const importing = ref(false)
const importError = ref('')
const importSuccess = ref('')
const polling = ref<number | null>(null)

let debounceTimer: ReturnType<typeof setTimeout> | null = null

function formatDate(dateStr: string | null): string {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

async function loadDashboard() {
  try {
    dashboard.value = await foi.fetchDashboard()
  } catch (e) {
    console.error('Failed to load dashboard:', e)
  }
}

async function loadRequests() {
  loadingRequests.value = true
  try {
    const params: Record<string, string | number | boolean> = {}
    if (searchQuery.value) params.search = searchQuery.value
    if (statusFilter.value) params.status = statusFilter.value
    const res = await foi.fetchRequests(params)
    requests.value = res.data
  } catch (e) {
    console.error('Failed to load requests:', e)
  } finally {
    loadingRequests.value = false
  }
}

function debouncedFetchRequests() {
  if (debounceTimer) clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => loadRequests(), 300)
}

async function handleImport() {
  if (!importUrl.value.trim()) return
  importing.value = true
  importError.value = ''
  importSuccess.value = ''
  try {
    const req = await foi.importRequest(importUrl.value.trim())
    importSuccess.value = `Imported: ${req.title}`
    importUrl.value = ''
    await Promise.all([loadDashboard(), loadRequests()])
  } catch (e: any) {
    importError.value = e?.data?.message || 'Failed to import request'
  } finally {
    importing.value = false
  }
}

async function handlePoll(id: number) {
  polling.value = id
  try {
    await foi.pollRequest(id)
    await Promise.all([loadDashboard(), loadRequests()])
  } catch (e) {
    console.error('Poll failed:', e)
  } finally {
    polling.value = null
  }
}

async function handleDelete(id: number) {
  if (!confirm('Stop tracking this request?')) return
  try {
    await foi.deleteRequest(id)
    await Promise.all([loadDashboard(), loadRequests()])
  } catch (e) {
    console.error('Delete failed:', e)
  }
}

async function handleAcknowledge(id: number) {
  try {
    await foi.acknowledgeUpdate(id)
    await loadDashboard()
  } catch (e) {
    console.error('Acknowledge failed:', e)
  }
}

async function handleAcknowledgeAll() {
  try {
    await foi.acknowledgeAll()
    await loadDashboard()
  } catch (e) {
    console.error('Acknowledge all failed:', e)
  }
}

onMounted(() => {
  loadDashboard()
  loadRequests()
})
</script>

<style scoped>
.foi-dashboard {
  max-width: 1000px;
  margin: 0 auto;
  padding: 2rem;
  font-family: system-ui, -apple-system, sans-serif;
  color: #2c3e50;
}

.dashboard-header {
  margin-bottom: 2rem;
}

.dashboard-header h1 {
  font-size: 2rem;
  margin: 0;
}

.subtitle {
  color: #7f8c8d;
  margin: 0.25rem 0 0;
}

/* Import Section */
.import-section {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 2rem;
}

.import-section h2 {
  font-size: 1.1rem;
  margin: 0 0 0.75rem;
}

.import-form {
  display: flex;
  gap: 0.5rem;
}

.import-input {
  flex: 1;
  padding: 0.5rem 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.95rem;
}

.error-msg { color: #e74c3c; margin: 0.5rem 0 0; font-size: 0.9rem; }
.success-msg { color: #27ae60; margin: 0.5rem 0 0; font-size: 0.9rem; }

/* Summary Cards */
.summary-section {
  margin-bottom: 2rem;
}

.summary-cards {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: 1.5rem;
}

.summary-card {
  flex: 1;
  min-width: 140px;
  text-align: center;
}

.summary-card .card-value {
  font-size: 2rem;
  font-weight: 700;
}

.summary-card .card-label {
  font-size: 0.85rem;
  color: #7f8c8d;
  margin-bottom: 0.5rem;
}

.summary-card.alert { border-left: 4px solid #e74c3c; }
.summary-card.warning { border-left: 4px solid #e67e22; }
.summary-card.info { border-left: 4px solid #3498db; }

.status-breakdown h3 {
  font-size: 1rem;
  margin: 0 0 0.75rem;
}

.status-bar-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.4rem;
}

.status-count {
  font-weight: 600;
  font-size: 0.9rem;
}

/* Status Badges */
.status-badge {
  display: inline-block;
  padding: 0.2rem 0.6rem;
  border-radius: 12px;
  color: white;
  font-size: 0.8rem;
  font-weight: 500;
  white-space: nowrap;
}

/* Updates */
.updates-section, .overdue-section, .requests-section {
  margin-bottom: 2rem;
}

.updates-section h2, .overdue-section h2, .requests-section h2 {
  font-size: 1.2rem;
  margin: 0 0 1rem;
}

.update-item {
  padding: 0.75rem;
  border-left: 3px solid #ddd;
  margin-bottom: 0.5rem;
  background: #fafafa;
  border-radius: 0 4px 4px 0;
}

.update-item.unacknowledged {
  border-left-color: #e74c3c;
  background: #fdf2f2;
}

.update-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.25rem;
}

.update-time {
  font-size: 0.8rem;
  color: #95a5a6;
}

.update-body {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.arrow {
  color: #7f8c8d;
  font-weight: 600;
}

/* Request List */
.filter-bar {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.search-input {
  flex: 1;
  padding: 0.5rem 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.filter-select {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.request-item {
  padding: 1rem;
  border: 1px solid #e8e8e8;
  border-radius: 6px;
  margin-bottom: 0.75rem;
  background: white;
}

.request-item.overdue {
  border-left: 4px solid #e67e22;
}

.request-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.request-title a {
  color: #2c3e50;
  text-decoration: none;
  font-weight: 600;
}

.request-title a:hover {
  color: #3498db;
  text-decoration: underline;
}

.request-actions {
  display: flex;
  gap: 0.25rem;
  flex-shrink: 0;
}

.request-meta {
  display: flex;
  gap: 0.75rem;
  align-items: center;
  margin-top: 0.5rem;
  font-size: 0.9rem;
  flex-wrap: wrap;
}

.authority {
  color: #7f8c8d;
}

.poll-time {
  color: #bdc3c7;
  font-size: 0.8rem;
}

.request-notes {
  margin-top: 0.5rem;
  padding: 0.5rem;
  background: #f8f9fa;
  border-radius: 4px;
  font-size: 0.85rem;
  color: #555;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #95a5a6;
  font-size: 1.1rem;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #7f8c8d;
}

/* Buttons */
.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-primary {
  background: #3498db;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #2980b9;
}

.btn-small {
  padding: 0.25rem 0.75rem;
  font-size: 0.8rem;
  background: #ecf0f1;
  border-radius: 4px;
  margin-top: 0.5rem;
}

.btn-tiny {
  padding: 0.2rem 0.5rem;
  font-size: 0.75rem;
  background: #ecf0f1;
  border-radius: 3px;
}

.btn-tiny:hover:not(:disabled) {
  background: #ddd;
}

.btn-danger {
  color: #e74c3c;
}

.btn-danger:hover:not(:disabled) {
  background: #fdf2f2;
}

/* Card */
.card {
  background: white;
  padding: 1.25rem;
  border-radius: 8px;
  border: 1px solid #e8e8e8;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
</style>
