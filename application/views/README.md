# application/views — struktur folder

View dipisah per alur supaya gampang dirawat. Pemetaan action -> grup ada di
**satu tempat**: `application/config/routes.php`.

| Folder     | Isi                                                                 |
|------------|---------------------------------------------------------------------|
| `fg/`       | Alur **FG (Finished Goods)**: input nopol/ID shipment, cek truck, dan form KIR (input/edit nopol). |
| `material/` | Alur **Material**: input kode kirim + cek truck.                    |
| `common/`   | View bersama / lainnya: home, pilih gate, registrasi driver & helper, vaksin, checklist gate 1/2, simpan, KPI, user. |
| `_archive/` | File lama/tidak terpakai (backup, varian tanggal, versi lama). Jangan dipakai di alur aktif. |

Controller ada di `application/controllers/`:
- `fg.php`, `material.php`, `common.php` — controller per grup (URL: `fg?action=...`, `material?action=...`, `common?action=...`).
- `main.php` — kompatibilitas URL lama `main?action=...` (dispatch ke controller grup, POST tetap aman).

Untuk menambah halaman baru: taruh view di folder grup yang sesuai, lalu daftarkan
action-nya di `application/config/routes.php` dan di daftar template controller grup.

> Catatan: upload foto (gate 1 & vaksin) menulis ke `application/views/common/capture/`.
