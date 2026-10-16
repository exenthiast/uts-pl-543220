<script setup>
import { ref, onMounted, computed } from 'vue';
import apiClient from './api/axios';
import Navbar from './components/Navbar.vue';
import LoginForm from './components/LoginForm.vue';
import BookTable from './components/BookTable.vue';
import BookModal from './components/BookModal.vue';

// State Autentikasi
const token = ref(localStorage.getItem('auth_token') || null);
const user = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'));
const isAuthLoading = ref(false);
const authError = ref('');

// State Koleksi Buku
const books = ref([]);
const isBooksLoading = ref(false);
const isModalOpen = ref(false);
const selectedBook = ref(null);
const isSaving = ref(false);
const serverValidationErrors = ref({});

// Feedback Notification
const toastMessage = ref('');
const toastType = ref('success');

const showToast = (message, type = 'success') => {
  toastMessage.value = message;
  toastType.value = type;
  setTimeout(() => {
    toastMessage.value = '';
  }, 4000);
};

// Statistik Inventaris
const totalTitles = computed(() => books.value.length);
const totalCopies = computed(() =>
  books.value.reduce((acc, curr) => acc + (Number(curr.stock) || 0), 0)
);

// Fetch Daftar Buku
const fetchBooks = async () => {
  if (!token.value) return;
  isBooksLoading.value = true;
  try {
    const response = await apiClient.get('/books');
    books.value = response.data.data || [];
  } catch (error) {
    if (error.response?.status !== 401) {
      showToast('Gagal memuat data buku dari server.', 'danger');
    }
  } finally {
    isBooksLoading.value = false;
  }
};

// Autentikasi: Login
const handleLogin = async ({ email, password }) => {
  isAuthLoading.value = true;
  authError.value = '';
  try {
    const response = await apiClient.post('/login', { email, password });
    const { access_token, user: userData } = response.data;

    token.value = access_token;
    user.value = userData;

    localStorage.setItem('auth_token', access_token);
    localStorage.setItem('auth_user', JSON.stringify(userData));

    showToast(`Selamat datang kembali, ${userData.name}!`);
    await fetchBooks();
  } catch (error) {
    if (error.response && error.response.status === 422) {
      authError.value = error.response.data.message || 'Kredensial login tidak valid.';
    } else {
      authError.value = 'Terjadi gangguan jaringan atau server sedang offline.';
    }
  } finally {
    isAuthLoading.value = false;
  }
};

// Autentikasi: Logout
const handleLogout = async () => {
  try {
    await apiClient.post('/logout');
  } catch (error) {
    console.error('Logout error:', error);
  } finally {
    token.value = null;
    user.value = null;
    books.value = [];
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    showToast('Sesi telah diakhiri. Sampai jumpa!');
  }
};

// Modal Handlers
const openCreateModal = () => {
  selectedBook.value = null;
  serverValidationErrors.value = {};
  isModalOpen.value = true;
};

const openEditModal = (book) => {
  selectedBook.value = book;
  serverValidationErrors.value = {};
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  selectedBook.value = null;
  serverValidationErrors.value = {};
};

// CRUD: Simpan (Create / Update)
const handleSaveBook = async (formData) => {
  isSaving.value = true;
  serverValidationErrors.value = {};
  try {
    if (selectedBook.value) {
      // Update
      const response = await apiClient.put(`/books/${selectedBook.value.id}`, formData);
      showToast(response.data.message || 'Data buku berhasil diperbarui.');
    } else {
      // Create (201)
      const response = await apiClient.post('/books', formData);
      showToast(response.data.message || 'Buku baru berhasil ditambahkan.');
    }
    closeModal();
    await fetchBooks();
  } catch (error) {
    if (error.response && error.response.status === 422) {
      serverValidationErrors.value = error.response.data.errors || {};
      showToast('Terdapat kesalahan validasi pada formulir.', 'danger');
    } else {
      showToast('Gagal menyimpan data ke basis data.', 'danger');
    }
  } finally {
    isSaving.value = false;
  }
};

// CRUD: Hapus (Delete)
const handleDeleteBook = async (id) => {
  const confirmed = window.confirm('Apakah Anda yakin ingin menghapus buku ini dari basis data?');
  if (!confirmed) return;

  try {
    const response = await apiClient.delete(`/books/${id}`);
    showToast(response.data.message || 'Buku berhasil dihapus.');
    await fetchBooks();
  } catch (error) {
    showToast('Gagal menghapus data buku.', 'danger');
  }
};

onMounted(() => {
  window.addEventListener('auth:unauthorized', () => {
    token.value = null;
    user.value = null;
    books.value = [];
    authError.value = 'Sesi Anda telah kedaluwarsa. Silakan masuk kembali.';
  });

  if (token.value) {
    fetchBooks();
  }
});
</script>

<template>
  <div class="app-layout">
    <!-- Navbar -->
    <Navbar :user="user" @logout="handleLogout" />

    <!-- Toast Notification -->
    <div v-if="toastMessage" class="toast-wrapper">
      <div class="toast" :class="`toast-${toastType}`">
        <span>{{ toastMessage }}</span>
      </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Jika Belum Terautentikasi -->
      <LoginForm
        v-if="!token"
        :loading="isAuthLoading"
        :error-message="authError"
        @login-success="handleLogin"
      />

      <!-- Jika Terautentikasi: Dashboard Katalog Buku -->
      <div v-else class="dashboard">
        <!-- Editorial Section Header -->
        <div class="dashboard-header">
          <div class="title-area">
            <h1>Inventaris Pustaka</h1>
            <p>Kelola data katalog terpusat melalui RESTful API backend Laravel.</p>
          </div>
          <button class="btn-primary" @click="openCreateModal">
            <span>+ Tambah Buku Baru</span>
          </button>
        </div>

        <!-- Tone Step Metrics Summary -->
        <div class="metrics-grid">
          <div class="metric-box">
            <span class="metric-label">Total Judul Buku</span>
            <span class="metric-value">{{ totalTitles }}</span>
          </div>
          <div class="metric-box">
            <span class="metric-label">Total Eksemplar Fisik</span>
            <span class="metric-value">{{ totalCopies }}</span>
          </div>
          <div class="metric-box">
            <span class="metric-label">Status Sinkronisasi API</span>
            <span class="metric-badge">Tersambung (Sanctum)</span>
          </div>
        </div>

        <!-- Table View -->
        <BookTable
          :books="books"
          :loading="isBooksLoading"
          @edit="openEditModal"
          @delete="handleDeleteBook"
        />
      </div>
    </main>

    <!-- Create/Edit Modal Dialog -->
    <BookModal
      :is-open="isModalOpen"
      :book="selectedBook"
      :loading="isSaving"
      :server-errors="serverValidationErrors"
      @close="closeModal"
      @save="handleSaveBook"
    />
  </div>
</template>

<style scoped>
.app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
  padding: 2rem 1.5rem 4rem;
}

.toast-wrapper {
  position: fixed;
  top: 1.5rem;
  right: 1.5rem;
  z-index: 100;
}

.toast {
  padding: 0.75rem 1.25rem;
  border-radius: var(--radius-md);
  font-size: 0.88rem;
  font-weight: 500;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.toast-success {
  background-color: var(--color-ink);
  color: #ffffff;
}

.toast-danger {
  background-color: var(--color-danger);
  color: #ffffff;
}

.dashboard {
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
}

.dashboard-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1.5rem;
}

.title-area h1 {
  margin-bottom: 0.25rem;
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
}

.metric-box {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: 1.15rem 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.metric-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--color-ink-muted);
}

.metric-value {
  font-size: 1.85rem;
  font-weight: 700;
  color: var(--color-ink);
  letter-spacing: -0.03em;
  line-height: 1.1;
}

.metric-badge {
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--color-success);
  margin-top: 0.25rem;
}

@media (max-width: 640px) {
  .dashboard-header {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
