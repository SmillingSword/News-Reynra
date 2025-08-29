#!/bin/bash

# Build script untuk News-Reynra dengan optimasi memory
# Gunakan script ini jika mengalami error "Out of memory"

echo "=== Building News-Reynra Application ==="

# Set memory limit untuk Node.js (disesuaikan dengan kapasitas server)
export NODE_OPTIONS="--max-old-space-size=4096"

echo "Memory limit set to 4GB"

# Clean cache sebelumnya untuk menghindari masalah
echo "Cleaning previous cache..."
rm -rf node_modules/.vite
rm -rf node_modules/.cache

# Install dependencies jika perlu
echo "Checking dependencies..."
npm install

# Build dengan optimasi
echo "Building application..."
npx vite build

# Build SSR jika diperlukan (opsional, bisa di-comment jika masih bermasalah)
# echo "Building SSR..."
# npx vite build --ssr

echo "=== Build completed ==="
echo "If you still encounter memory issues, try:"
echo "1. Increase --max-old-space-size to 8192 or higher"
echo "2. Add swap memory to your server"
echo "3. Use a server with more RAM"
