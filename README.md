# Aplikasi Voting Kakang Senduk Ponorogo

Aplikasi pembelajaran berbasis PHP native + MySQL untuk XAMPP. Folder proyek harus tetap bernama `voting-kakang-senduk` karena alamat dasar aplikasi diatur di `config/bootstrap.php`.

## Fitur yang tersedia

### Pengunjung/pemilih
- Beranda, daftar kontestan, pencarian/filter kategori, dan detail profil.
- Registrasi dan login pemilih dengan `password_hash()` dan `password_verify()`.
- Pembayaran voting simulasi sebesar **Rp3.000 per akun per periode**.
- Pembayaran menggunakan **Mock Gateway**. Tidak ada koneksi ke bank, QRIS, e-wallet, atau payment gateway sungguhan dan tidak ada uang yang dipotong.
- Status transaksi (`pending` menjadi `paid`), referensi transaksi, nominal, dan waktu pembayaran disimpan pada tabel `payments`.
- Setelah pembayaran simulasi tercatat, pemilih dapat memilih satu kontestan Kakang dan satu kontestan Senduk.
- Pemilih tidak dapat memilih dua kali dalam kategori yang sama pada periode yang sama.
- Kartu bukti voting digital per pilihan, menampilkan kategori, kontestan terpilih, nomor bukti, nama pemilih, serta tanggal/waktu voting.
- Bukti voting dapat dicetak atau disimpan sebagai PDF melalui fitur Print browser.
- Klasemen Kakang dan Senduk dihitung langsung dari tabel `voting`.

### Administrator
- Dashboard statistik.
- CRUD kontestan dan upload foto (JPG/PNG/WEBP, maksimal 3 MB).
- Melihat data pemilih.
- Mengatur nama, jadwal, serta status periode voting.
- Melihat hasil/klasemen dan grafik suara.

## Cara menjalankan di XAMPP

1. Ekstrak folder `voting-kakang-senduk` ke `C:\xampp\htdocs\`.
2. Buka XAMPP Control Panel, lalu jalankan **Apache** dan **MySQL**.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih menu **Import**, pilih file `database.sql`, lalu klik **Import/Go**. File SQL akan membuat database `db_voting_kakang_senduk` beserta tabel dan data demo.
5. Pastikan pengaturan koneksi di `config/koneksi.php` sesuai XAMPP kamu. Bawaan file ini: host `127.0.0.1`, user `root`, password kosong.
6. Buka `http://localhost/voting-kakang-senduk/` untuk beranda.
7. Halaman login tersedia melalui `http://localhost/voting-kakang-senduk/login.php` atau `http://localhost/voting-kakang-senduk/auth/login.php`.

## Halaman login

Halaman login menggunakan tampilan layar penuh dengan latar gambar Ponorogo dari URL yang diberikan, lapisan gelap, dan aksen merah, kuning, hijau tua, serta dark turquoise. Tersedia tombol tampil/sembunyikan password dan pintasan `login.php` di folder utama proyek. Setelah login, administrator diarahkan ke dashboard admin dan pemilih diarahkan ke beranda pemilih.

## Akun demo

| Peran | Email | Password |
|---|---|---|
| Administrator | `admin@ponorogo.local` | `admin123` |
| Pemilih demo | `pemilih@ponorogo.local` | `pemilih123` |

Sebaiknya ganti password demo sebelum aplikasi dipakai di luar komputer latihan.

## Alur simulasi pembayaran dan voting

1. Daftar sebagai pemilih atau masuk memakai akun pemilih demo.
2. Buka menu **Voting Saya**. Jika periode aktif tersedia, aplikasi mengarahkan pemilih ke halaman pembayaran.
3. Klik **Konfirmasi Bayar Rp3.000 (Simulasi)**. Aplikasi hanya memperbarui data transaksi demo di database menjadi `paid`.
4. Setelah berhasil, pilih satu kontestan pada kategori Kakang dan satu kontestan pada kategori Senduk. Masing-masing hanya dapat dipilih satu kali.
5. Setelah suara tersimpan, aplikasi membuka halaman **Bukti Voting Saya**. Kartu bukti dapat dicetak atau disimpan menjadi PDF.
6. Lihat hasil di menu **Klasemen**.

Menu **Bukti Voting** juga tersedia di navigasi pemilih untuk melihat kembali seluruh pilihan yang pernah tersimpan.

**Catatan nominal:** Rp3.000 diterapkan satu kali untuk satu akun pada satu periode voting, bukan Rp3.000 untuk setiap kategori. Satu pembayaran membuka akses memilih Kakang dan Senduk.

## Struktur database

- `users`: akun administrator dan pemilih.
- `kontestan`: profil Kakang dan Senduk.
- `periode_voting`: nama, jadwal, dan status periode.
- `payments`: transaksi pembayaran simulasi.
- `voting`: suara pemilih dan sumber data untuk kartu bukti digital.

Tabel `voting` memiliki unique key pada kombinasi `user_id`, `periode_id`, dan `kategori` sebagai perlindungan tambahan agar satu pemilih tidak bisa memberi suara dua kali pada kategori yang sama. Validasi juga dilakukan di sisi server.

## Latar halaman login dan dashboard

URL gambar yang diberikan dipasang di variabel CSS `--hero-bg` pada `assets/css/style.css`. Karena thumbnail Google tidak dapat diunduh saat paket ini dibuat, gambar dimuat langsung melalui URL tersebut dari browser. Jika tautan gambar sudah tidak aktif, simpan gambar yang sama ke `assets/img/` (misalnya `ponorogo-bg.jpg`), lalu ubah `--hero-bg` menjadi `url('../img/ponorogo-bg.jpg')`.

## Catatan teknis

- Dibuat untuk PHP 8.1+ dan MySQL/MariaDB di XAMPP.
- Bootstrap 5.3 dan Chart.js dimuat dari CDN, sehingga koneksi internet diperlukan untuk komponen library tersebut.
- Folder `uploads/` digunakan untuk foto kontestan.
- Database demo berisi 20 peserta dengan tautan ke postingan Instagram yang diberikan. Karena Instagram meminta login untuk mengakses foto, kartu peserta memakai placeholder sampai file foto lokal diunggah; tautan Instagram tetap tersedia dari halaman peserta.
- Pembayaran adalah simulasi lokal untuk pembelajaran, bukan sistem pembayaran sungguhan dan jangan dipakai untuk menarik uang dari pengguna.
