#!/bin/bash

# Script untuk menambah swap memory di server
# Jalankan dengan: sudo bash add_swap.sh

echo "=== Adding Swap Memory ==="

# Cek swap yang tersedia
echo "Current memory status:"
free -h

# Tentukan ukuran swap (4GB)
SWAP_SIZE="4G"

echo "Creating swap file of size $SWAP_SIZE..."

# Buat swap file
sudo fallocate -l $SWAP_SIZE /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile

# Atur swappiness untuk optimasi
echo 'vm.swappiness=10' | sudo tee -a /etc/sysctl.conf
sudo sysctl -p

# Jadikan permanen
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab

echo "Swap memory added successfully!"
echo "New memory status:"
free -h

echo ""
echo "=== Next Steps ==="
echo "1. Jalankan build.sh lagi: ./build.sh"
echo "2. Jika masih error, coba tingkatkan memory limit di build.sh menjadi 16GB"
echo "3. Atau pertimbangkan upgrade RAM server"
