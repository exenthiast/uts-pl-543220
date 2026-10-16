<script setup>
defineProps({
  user: {
    type: Object,
    default: null,
  },
});

defineEmits(['logout']);
</script>

<template>
  <header class="navbar">
    <div class="navbar-container">
      <div class="brand">
        <span class="brand-badge">KEPL 2026</span>
        <span class="brand-title">Katalog Pustaka</span>
        <span class="brand-nim">NIM 543220</span>
      </div>

      <div v-if="user" class="user-menu">
        <div class="user-info">
          <span class="user-name">{{ user.name }}</span>
          <span class="user-email">{{ user.email }}</span>
        </div>
        <button class="btn-secondary btn-sm" @click="$emit('logout')">
          Keluar
        </button>
      </div>
    </div>
  </header>
</template>

<style scoped>
.navbar {
  background-color: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
  padding: 0.9rem 1.5rem;
  position: sticky;
  top: 0;
  z-index: 20;
}

.navbar-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.brand-badge {
  background-color: var(--color-primary);
  color: #ffffff;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  padding: 0.2rem 0.5rem;
  border-radius: var(--radius-sm);
}

.brand-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-ink);
  letter-spacing: -0.02em;
}

.brand-nim {
  font-size: 0.8rem;
  color: var(--color-ink-subtle);
  border-left: 1px solid var(--color-border);
  padding-left: 0.75rem;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 1.25rem;
}

.user-info {
  display: flex;
  flex-direction: column;
  text-align: right;
  line-height: 1.2;
}

.user-name {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--color-ink);
}

.user-email {
  font-size: 0.75rem;
  color: var(--color-ink-muted);
}

.btn-sm {
  padding: 0.4rem 0.75rem;
  font-size: 0.8rem;
}
</style>
