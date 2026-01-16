<template>
  <div class="container">
    <header>
      <h1>🐾 Pet Listing Platform</h1>
      <p>Find your perfect pet companion</p>
    </header>

    <main>
      <section class="welcome">
        <h2>Welcome to Pet Listing</h2>
        <p>This is a work in progress. Database planning and features coming soon!</p>
      </section>

      <section class="tech-stack">
        <h3>Tech Stack</h3>
        <ul>
          <li>Frontend: Nuxt 4 (Vue 3)</li>
          <li>Backend: Laravel 12</li>
          <li>Database: PostgreSQL</li>
        </ul>
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
// Index page - main landing page for the pet listing platform
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  font-family: system-ui, -apple-system, sans-serif;
}

header {
  text-align: center;
  margin-bottom: 3rem;
}

header h1 {
  font-size: 3rem;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

header p {
  font-size: 1.25rem;
  color: #7f8c8d;
}

main {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.welcome, .tech-stack {
  background: #f8f9fa;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

h2, h3 {
  color: #2c3e50;
  margin-bottom: 1rem;
}

ul {
  list-style: none;
  padding: 0;
}

ul li {
  padding: 0.5rem 0;
  font-size: 1.1rem;
}

ul li:before {
  content: "✓ ";
  color: #27ae60;
  font-weight: bold;
  margin-right: 0.5rem;
}
</style>
