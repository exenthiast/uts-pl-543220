<script setup>
import { ref } from 'vue';

const emit = defineEmits(['login-success']);
const props = defineProps({
  loading: Boolean,
  errorMessage: String,
});

const email = ref('admin@example.com');
const password = ref('password123');

const handleSubmit = () => {
  emit('login-success', {
    email: email.value,
    password: password.value,
  });
};
</script>

<template>
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-header">
        <h2>Autentikasi Sistem</h2>
        <p>Masuk menggunakan kredensial akun untuk mengakses RESTful API.</p>
      </div>

      <div v-if="errorMessage" class="error-banner">
        <span>{{ errorMessage }}</span>
      </div>

      <form @submit.prevent="handleSubmit" class="auth-form">
        <div class="form-group">
          <label for="email">Alamat Email</label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            placeholder="nama@domain.com"
            autocomplete="email"
          />
        </div>

        <div class="form-group">
          <label for="password">Kata Sandi</label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            placeholder="••••••••"
            autocomplete="current-password"
          />
        </div>

        <button type="submit" class="btn-primary auth-submit" :disabled="loading">
          <span v-if="loading">Memproses Autentikasi...</span>
          <span v-else>Masuk ke Aplikasi</span>
        </button>
      </form>

      <div class="auth-hint">
        <span class="hint-label">Akun Bawaan Seeder:</span>
        <code>admin@example.com</code> / <code>password123</code>
      </div>
    </div>
  </div>
</template>

<style scoped>
.auth-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 120px);
  padding: 2rem 1rem;
}

.auth-card {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: 2.25rem 2.5rem;
  width: 100%;
  max-width: 440px;
}

.auth-header {
  margin-bottom: 1.75rem;
}

.auth-header h2 {
  font-size: 1.45rem;
  margin-bottom: 0.4rem;
}

.auth-header p {
  font-size: 0.9rem;
}

.error-banner {
  background-color: var(--color-danger-light);
  border: 1px solid var(--color-danger-border);
  color: var(--color-danger);
  padding: 0.65rem 0.85rem;
  border-radius: var(--radius-md);
  font-size: 0.85rem;
  margin-bottom: 1.25rem;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.form-group label {
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--color-ink);
}

.auth-submit {
  margin-top: 0.5rem;
  width: 100%;
  padding: 0.75rem;
}

.auth-hint {
  margin-top: 1.75rem;
  padding-top: 1.25rem;
  border-top: 1px solid var(--color-border);
  font-size: 0.8rem;
  color: var(--color-ink-muted);
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.hint-label {
  font-weight: 600;
  color: var(--color-ink);
}

.auth-hint code {
  font-family: var(--font-mono);
  background-color: var(--color-surface-hover);
  padding: 0.2rem 0.4rem;
  border-radius: var(--radius-sm);
  width: fit-content;
  color: var(--color-ink);
}
</style>
