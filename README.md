/dashboard
│
├── index.php             ← halaman login
├── dashboard.php         ← halaman utama setelah login
├── jual.php              ← proses jual
├── beli.php              ← proses beli
├── logout.php            ← logout session
│
├── /data
│   ├── users.json        ← data akun login
│   ├── products.json     ← data produk
│   └── transactions.json ← riwayat jual/beli
│
├── /lib
│   ├── auth.php          ← fungsi login
│   ├── data.php          ← fungsi load & save json
│   └── utils.php         ← helper (misalnya generate id)
