import axios from 'axios';

// Alamat API diambil dari konfigurasi environment variable Vite (TIDAK DITULIS MATI)
const baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

const apiClient = axios.create({
  baseURL,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Request Interceptor: Menyematkan Bearer token otomatis jika tersimpan di localStorage
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response Interceptor: Menangani respons 401 Unauthorized secara global
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      // Hapus token yang tidak valid/kedaluwarsa
      localStorage.removeItem('auth_token');
      localStorage.removeItem('auth_user');

      // Beritahu aplikasi untuk kembali ke state login
      window.dispatchEvent(new CustomEvent('auth:unauthorized'));
    }
    return Promise.reject(error);
  }
);

export default apiClient;
