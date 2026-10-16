<script setup>
defineProps({
  books: {
    type: Array,
    default: () => [],
  },
  loading: Boolean,
});

defineEmits(['edit', 'delete']);
</script>

<template>
  <div class="table-card">
    <div v-if="loading" class="table-loading">
      Memuat data buku dari RESTful API...
    </div>

    <div v-else-if="books.length === 0" class="table-empty">
      <p class="empty-title">Belum ada koleksi buku yang terdaftar</p>
      <p class="empty-desc">Klik tombol "Tambah Buku Baru" di atas untuk menambahkan data buku.</p>
    </div>

    <div v-else class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th style="width: 32%;">Judul & Penulis</th>
            <th style="width: 20%;">ISBN</th>
            <th style="width: 12%; text-align: center;">Tahun</th>
            <th style="width: 16%; text-align: center;">Stok</th>
            <th style="width: 20%; text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="book in books" :key="book.id">
            <td>
              <div class="book-main">
                <span class="book-title">{{ book.title }}</span>
                <span class="book-author">oleh {{ book.author }}</span>
                <p v-if="book.description" class="book-desc">{{ book.description }}</p>
              </div>
            </td>
            <td>
              <span class="isbn-tag">{{ book.isbn }}</span>
            </td>
            <td style="text-align: center;">
              <span class="year-text">{{ book.published_year }}</span>
            </td>
            <td style="text-align: center;">
              <span
                class="badge"
                :class="book.stock > 0 ? 'badge-success' : 'badge-danger'"
              >
                {{ book.stock > 0 ? `${book.stock} Unit` : 'Habis' }}
              </span>
            </td>
            <td style="text-align: right;">
              <div class="action-buttons">
                <button
                  class="btn-secondary btn-xs"
                  @click="$emit('edit', book)"
                  title="Ubah Data Buku"
                >
                  Edit
                </button>
                <button
                  class="btn-danger-outline btn-xs"
                  @click="$emit('delete', book.id)"
                  title="Hapus Data Buku"
                >
                  Hapus
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.table-card {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.table-loading, .table-empty {
  padding: 3.5rem 1.5rem;
  text-align: center;
  color: var(--color-ink-muted);
}

.empty-title {
  font-size: 1rem;
  font-weight: 600;
  color: var(--color-ink);
  margin-bottom: 0.25rem;
}

.empty-desc {
  font-size: 0.85rem;
}

.table-responsive {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.data-table thead {
  background-color: var(--color-surface-hover);
  border-bottom: 1px solid var(--color-border);
}

.data-table th {
  padding: 0.8rem 1.25rem;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--color-ink-muted);
}

.data-table td {
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--color-border);
  vertical-align: middle;
}

.data-table tbody tr:last-child td {
  border-bottom: none;
}

.data-table tbody tr:hover {
  background-color: #fafbfc;
}

.book-main {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.book-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--color-ink);
}

.book-author {
  font-size: 0.82rem;
  color: var(--color-ink-muted);
}

.book-desc {
  font-size: 0.8rem;
  color: var(--color-ink-subtle);
  margin-top: 0.2rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.isbn-tag {
  font-family: var(--font-mono);
  font-size: 0.82rem;
  color: var(--color-ink);
  background-color: var(--color-surface-hover);
  padding: 0.25rem 0.5rem;
  border-radius: var(--radius-sm);
  border: 1px solid var(--color-border);
}

.year-text {
  font-size: 0.9rem;
  font-weight: 500;
}

.action-buttons {
  display: inline-flex;
  gap: 0.5rem;
}

.btn-xs {
  padding: 0.3rem 0.65rem;
  font-size: 0.78rem;
}
</style>
