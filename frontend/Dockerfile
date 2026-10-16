# ==============================================================================
# Dockerfile Frontend Vue.js 3 Multi-stage Build (Soal 5b)
# Stage 1: Builder (Node.js) -> Membangun aset produksi SPA
# Stage 2: Runner (Nginx Alpine) -> Menyajikan web statis tanpa node_modules
# ==============================================================================

# --- TAHAP 1: BUILDER ---
FROM node:20-alpine AS builder

WORKDIR /app

# Salin package manifest terlebih dahulu untuk memanfaatkan caching layer Docker
COPY package.json package-lock.json ./

# Pasang dependensi secara clean dan konsisten
RUN npm ci

# Salin seluruh kode sumber frontend
COPY . .

# Argument build untuk URL API (default jika tidak disetel saat docker build)
ARG VITE_API_BASE_URL=/api
ENV VITE_API_BASE_URL=${VITE_API_BASE_URL}

# Kompilasi aset Vite ke folder /app/dist
RUN npm run build

# --- TAHAP 2: RUNNER ---
FROM nginx:1.27-alpine AS runner

# Salin berkas konfigurasi Nginx khusus untuk routing SPA
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Salin HANYA hasil build statis dari tahap builder (node_modules TIDAK IKUT)
COPY --from=builder /app/dist /usr/share/nginx/html

# Expose port HTTP default Nginx
EXPOSE 80

# Jalankan Nginx di foreground
CMD ["nginx", "-g", "daemon off;"]
