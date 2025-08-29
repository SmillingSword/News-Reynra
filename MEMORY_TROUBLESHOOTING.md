# Troubleshooting Memory Issues pada Build News-Reynra

## Masalah
Error: `RangeError: WebAssembly.instantiate(): Out of memory: Cannot allocate Wasm memory for new instance`

## Solusi

### 1. Meningkatkan Memory Limit Node.js
```bash
# Set environment variable sebelum build
export NODE_OPTIONS="--max-old-space-size=4096"  # 4GB
# atau
export NODE_OPTIONS="--max-old-space-size=8192"  # 8GB

# Kemudian jalankan build
npm run build
```

### 2. Menggunakan Build Script
Gunakan file `build.sh` yang telah disediakan:
```bash
# Berikan izin eksekusi
chmod +x build.sh

# Jalankan build script
./build.sh
```

### 3. Menambah Swap Memory di Server
Jika server memiliki RAM terbatas, tambahkan swap memory:

```bash
# Cek swap yang tersedia
free -h

# Buat swap file (contoh: 2GB)
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile

# Jadikan permanen
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

### 4. Build Bertahap (Jika Masih Bermasalah)
Coba build tanpa SSR terlebih dahulu:
```bash
# Hanya build client-side
npx vite build

# Jika berhasil, coba build SSR terpisah
npx vite build --ssr
```

### 5. Clean Cache
Kadang cache bisa menyebabkan masalah:
```bash
rm -rf node_modules/.vite
rm -rf node_modules/.cache
npm install
```

### 6. Upgrade Server
Jika semua solusi di atas tidak bekerja, pertimbangkan untuk:
- Upgrade RAM server
- Gunakan server dengan spesifikasi lebih tinggi
- Gunakan layanan cloud dengan auto-scaling

## Konfigurasi Optimal

File `vite.config.js` sudah dioptimalkan dengan:
- Chunk splitting untuk mengurangi beban memory
- Optimized dependencies
- Increased chunk size warning limit

## Monitoring Memory Usage
```bash
# Monitor memory usage selama build
npm run build --verbose

# atau
npx vite build --debug
```

## Support
Jika masih mengalami masalah, silakan buat issue di GitHub repository dengan detail:
- Spesifikasi server (RAM, CPU)
- Versi Node.js
- Output error lengkap
- Langkah-langkah yang sudah dicoba
