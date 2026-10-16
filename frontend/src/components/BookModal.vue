<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  isOpen: Boolean,
  book: {
    type: Object,
    default: null,
  },
  loading: Boolean,
  serverErrors: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['close', 'save']);

const form = ref({
  title: '',
  author: '',
  isbn: '',
  published_year: new Date().getFullYear(),
  stock: 1,
  description: '',
});

watch(
  () => props.book,
  (newVal) => {
    if (newVal) {
      form.value = {
        title: newVal.title || '',
        author: newVal.author || '',
        isbn: newVal.isbn || '',
        published_year: newVal.published_year || 2024,
        stock: newVal.stock ?? 0,
        description: newVal.description || '',
      };
    } else {
      form.value = {
        title: '',
        author: '',
        isbn: '',
        published_year: new Date().getFullYear(),
        stock: 1,
        description: '',
      };
    }
  },
  { immediate: true }
);

const handleClose = () => {
  emit('close');
};

const handleSave = () => {
  emit('save', { ...form.value });
};
</script>

<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="handleClose">
    <div class="modal-dialog">
      <div class="modal-header">
        <h3>{{ book ? 'Ubah Data Buku' : 'Tambah Buku Baru' }}</h3>
        <button class="close-btn" @click="handleClose" aria-label="Tutup Modal">&times;</button>
      </div>

      <form @submit.prevent="handleSave" class="modal-body">
        <div class="form-group">
          <label for="title">Judul Buku *</label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            required
            placeholder="Contoh: The DevOps Handbook"
          />
          <span v-if="serverErrors.title" class="field-error">{{ serverErrors.title[0] }}</span>
        </div>

        <div class="form-row">
          <div class="form-group col-6">
            <label for="author">Penulis *</label>
            <input
              id="author"
              v-model="form.author"
              type="text"
              required
              placeholder="Contoh: Gene Kim"
            />
            <span v-if="serverErrors.author" class="field-error">{{ serverErrors.author[0] }}</span>
          </div>

          <div class="form-group col-6">
            <label for="isbn">Nomor ISBN *</label>
            <input
              id="isbn"
              v-model="form.isbn"
              type="text"
              required
              placeholder="Contoh: 978-1942788003"
            />
            <span v-if="serverErrors.isbn" class="field-error">{{ serverErrors.isbn[0] }}</span>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-6">
            <label for="year">Tahun Terbit *</label>
            <input
              id="year"
              v-model.number="form.published_year"
              type="number"
              min="1800"
              max="2100"
              required
            />
            <span v-if="serverErrors.published_year" class="field-error">{{ serverErrors.published_year[0] }}</span>
          </div>

          <div class="form-group col-6">
            <label for="stock">Jumlah Stok Unit *</label>
            <input
              id="stock"
              v-model.number="form.stock"
              type="number"
              min="0"
              required
            />
            <span v-if="serverErrors.stock" class="field-error">{{ serverErrors.stock[0] }}</span>
          </div>
        </div>

        <div class="form-group">
          <label for="description">Deskripsi Ringkas (Opsional)</label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            placeholder="Keterangan isi buku atau catatan perpustakaan..."
          ></textarea>
          <span v-if="serverErrors.description" class="field-error">{{ serverErrors.description[0] }}</span>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn-secondary" @click="handleClose" :disabled="loading">
            Batal
          </button>
          <button type="submit" class="btn-primary" :disabled="loading">
            <span v-if="loading">Menyimpan...</span>
            <span v-else>{{ book ? 'Simpan Perubahan' : 'Tambahkan Buku' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(15, 23, 42, 0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
  padding: 1.5rem;
}

.modal-dialog {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  width: 100%;
  max-width: 540px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}

.modal-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--color-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-header h3 {
  font-size: 1.15rem;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.4rem;
  line-height: 1;
  color: var(--color-ink-muted);
  cursor: pointer;
  padding: 0.2rem;
}

.close-btn:hover {
  color: var(--color-ink);
}

.modal-body {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
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

.form-row {
  display: flex;
  gap: 1rem;
}

.col-6 {
  flex: 1;
}

.field-error {
  font-size: 0.78rem;
  color: var(--color-danger);
}

.modal-footer {
  margin-top: 0.75rem;
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
}
</style>
