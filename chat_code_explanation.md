# Penjelasan Kode & Arsitektur: `resources/js/Pages/Chat/Index.vue`

Dokumen ini menjelaskan secara rinci tentang tata letak, pengelolaan state, logika penanganan WebSocket (real-time), dan interaksi antarmuka pengguna (UI) yang ada pada halaman obrolan.

---

## 1. STRUKTUR PROPS DAN STATE REAKTIF

### Props yang Diterima dari Backend (Inertia)
* **`conversations`** (`Array`): Daftar seluruh obrolan/percakapan di mana pengguna masuk sebagai partisipan.
* **`activeConversationId`** (`Number|String|null`): ID dari percakapan yang saat ini sedang aktif dibuka.
* **`messages`** (`Array`): Daftar pesan-pesan yang ada di dalam percakapan aktif yang sedang dibuka.

### State Reaktif Utama (`ref` & `computed`)
* **`localConversations`**: Salinan reaktif dari `props.conversations`. Digunakan untuk memperbarui daftar chat di sidebar secara real-time saat ada pesan baru atau perubahan status online.
* **`localMessages`**: Salinan reaktif dari `props.messages`. Digunakan untuk menampilkan chat bubble secara langsung, termasuk penambahan pesan instan saat pengguna menekan tombol kirim (Optimistic UI).
* **`newMessageText`**: Menyimpan nilai string dari pesan yang sedang diketik di kolom input.
* **`showDocPopup`**: Boolean untuk mengatur buka/tutup popup menu lampiran berkas (`+`).
* **`activeMessageMenuId`**: Menyimpan ID pesan yang dropdown menu 3-titik tindakan pesan (`...`) sedang dibuka.
* **`activeSidebarMenuId`**: Menyimpan ID obrolan yang dropdown menu 3-titik tindakan obrolan di sidebar sedang dibuka.
* **`activeConversation`** (`computed`): Mengembalikan objek detail percakapan yang saat ini sedang dibuka.
* **`filteredConversations`** (`computed`): Menyaring daftar `localConversations` berdasarkan pencarian teks di kolom pencarian sidebar.

---

## 2. INTERAKSI WEB SOCKET & REAL-TIME (Laravel Echo)

Fungsi utama yang mengurus sinkronisasi real-time adalah **`setupWebSocket()`**. Fungsi ini berjalan saat komponen dimuat (*mounted*) atau ketika pengguna berpindah ke ruang obrolan lain.

### A. Channel Obrolan Aktif (`private: conversation.{id}`)
Bila ada obrolan yang sedang terbuka, client akan terhubung ke channel ini:
```javascript
activeChannel = window.Echo.private(`conversation.${props.activeConversationId}`)
```
Sistem akan mendengarkan event berikut:
* **`.message-sent`**: Ketika partner mengirim pesan ke ruangan ini:
  * Pesan baru ditambahkan ke `localMessages`.
  * Halaman otomatis di-scroll ke bawah.
  * Mengirim permintaan API `POST /chat/messages/read` untuk menandai pesan tersebut langsung terbaca.
* **`.message-read`**: Terpicu saat penerima membuka chat Anda. Mengubah status centang pesan dari abu-abu menjadi centang ganda biru (`read`).
* **`.message-delivered`**: Terpicu ketika pesan Anda sampai di perangkat partner. Mengubah status centang dari centang satu menjadi centang dua abu-abu (`delivered`).

### B. Channel Latar Belakang (Pesan Masuk di Sidebar)
Agar pengguna tetap menerima info pesan masuk untuk chat yang *tidak sedang dibuka*:
```javascript
localConversations.value.forEach(convo => {
    const channel = window.Echo.private(`conversation.${convo.id}`)
    ...
})
```
* Mendeteksi `.message-sent` dari latar belakang:
  * Menambah jumlah belum dibaca (`unread_count`) di sidebar.
  * Mengupdate baris chat di sidebar dengan pesan terakhir (`last_message`) dan waktu terbaru (`last_message_time`).
  * Mengirimkan API `POST /chat/messages/delivered` agar pengirim tahu pesan sudah sampai.

### C. Channel Kehadiran (`presence: tenant.{tenantId}.presence`)
Digunakan untuk melacak status online dan offline secara global dalam satu tenant:
* **`.here(users)`**: Menampilkan siapa saja anggota tim yang sedang aktif ketika halaman baru dibuka.
* **`.joining(user)`**: Mengubah bulatan indikator di samping nama pengguna menjadi hijau cerah ketika pengguna tersebut masuk ke aplikasi.
* **`.leaving(user)`**: Mengubah status menjadi offline dan memperbarui *last seen* menjadi "Baru saja" ketika pengguna tersebut menutup aplikasi atau logout.

---

## 3. ALUR PENGIRIMAN PESAN (Optimistic UI)

Fungsi **`sendMessage()`** menggunakan teknik *Optimistic UI* untuk memberikan pengalaman pengguna yang sangat cepat tanpa jeda *loading*:
1. **Membuat Pesan Sementara (Temporary Message)**: Begitu tombol kirim ditekan, aplikasi membuat objek pesan baru secara lokal dengan status `'sending'` dan memasukkannya ke dalam `localMessages`.
2. **Kirim Request API**: Mengirimkan data menggunakan `axios.post('/chat/messages')`.
3. **Penanganan Respon Sukses**: Ketika server mengembalikan respon sukses, pesan sementara dengan status `'sending'` tadi diganti dengan pesan ber-ID resmi dari database dengan status `'sent'` (centang satu abu-abu).
4. **Penanganan Respon Gagal**: Jika koneksi terputus atau terjadi kesalahan, status pesan berubah menjadi `'failed'` dengan tanda peringatan merah (`!`).

---

## 4. DESAIN KODE TATA LETAK & ELEMEN INTERAKTIF BARU

### A. Tombol Tambah (`+`) & Popup Menu Dokumen
Terletak tepat di sebelah kiri dari kolom input pesan:
```html
<div class="relative shrink-0">
    <button @click="showDocPopup = !showDocPopup" ...>
        <svg :class="showDocPopup ? 'rotate-45 text-indigo-500' : ''" ...>
            <!-- Ikon Plus (+) -->
        </svg>
    </button>
    
    <!-- Popup Menu Dokumen -->
    <div v-if="showDocPopup" class="absolute bottom-12 left-0 z-50 ...">
        <!-- Opsi Dokumen, Gambar, Excel -->
    </div>
</div>
```
* **Efek Transisi**: Ikon `+` akan berputar halus sebesar 45 derajat (membentuk tanda silang pembatalan) saat menu sedang terbuka.
* **Metode Aksi**: Setiap tombol opsi memiliki `@click="handleDocAction('Nama Berkas')"`.

### B. Tindakan Tiga Titik (`...`) pada Pesan (Hover Trigger)
* Terbungkus di dalam baris pesan dengan kelas `group/bubble`.
* Tombol titik tiga memiliki kelas `opacity-0 group-hover/bubble:opacity-100` sehingga tombol hanya akan muncul saat pengguna mengarahkan kursor (*hover*) di area balon obrolan tersebut.
* Klik pada tombol memanggil `toggleMessageMenu(message.id)` untuk memunculkan pilihan tindakan: **Salin Pesan** (menyalin langsung ke clipboard perangkat), **Balas Pesan**, atau **Hapus Pesan**.

### C. Tindakan Tiga Titik (`...`) pada Sidebar
* Terletak secara absolut di kanan bawah baris percakapan sidebar.
* Menggunakan `@click="toggleSidebarMenu($event, convo.id)"` dengan modifikasi pencegahan propagasi (`e.stopPropagation()`) agar ketika tombol menu diklik, pengguna tidak otomatis terlempar masuk ke ruang obrolan terkait.
* Menyediakan aksi cepat untuk menandai chat sebagai dibaca atau menghapus riwayat percakapan.
