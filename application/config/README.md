# application/config — koneksi database

Semua koneksi ada di sini. **Satu file untuk satu koneksi**, dan setiap koneksi memakai
helper `db_open()` (connect timeout **5s**) supaya server yang mati/tidak terjangkau
gagal cepat — bukan menggantung 60s lalu jadi 504 Gateway Timeout.

> Status saat ini: **semua koneksi diarahkan ke lokal** `127.0.0.1 / root / root`.
> Nilai produksi disimpan sebagai komentar di masing-masing file.

| File | Variabel | Database | Dipakai oleh |
|------|----------|----------|--------------|
| `db_dbtruck.php` | `$con` | dbtruck | config.php (app-wide) |
| `db_smartlogistic.php` | `$con2`, `$conSL` | smartlogistic | config.php, index.php |
| `db_aquan_central.php` | `$con_140` | aquan_central | config.php |
| `db_evisitor.php` | `$con_3` | evisitor | config.php |
| `db_cloud_asn.php` | `$concloud`, `$conASN` | dbasnho | index.php, Table.php, connectionASN.php |
| `db_product_release.php` | `$con73` | db_product_release | views/fg (form KIR) |
| `db_dbtruck_local.php` | `$con` | dbtruck | report.php, lihat_foto.php |
| `db_dbtruck_gate73.php` | `$con73` | dbtruck | ttat_bwg_api.php, ttat_gate2_bwg_api.php |
| `db_connect.php` | `db_open()` | — | helper untuk semua file di atas |

## Loader kompatibilitas

- `connectionASN.php` → `$conASN` (cloud/asn) + `$conASNPDO` (PDO) — dipakai assets/TableASN.php
- `connectionSL.php` → `$conSL`

## Catatan

- `routes.php` bukan koneksi: itu peta action → grup view.
- Cara ganti target: edit nilai di `db_open(...)` pada file terkait (baris produksi
  tersedia sebagai komentar di atasnya).
- `application/assets/TableASN.php` (`seedSupplier()`) masih `new mysqli()` dari variabel
  global — perlu dirapikan terpisah kalau mau 100% eksplisit.

## Catatan prod: dbevisitor & truck di DB berbeda

Di prod, tabel truck dan tabel evisitor ada di **database berbeda** (dan bisa
beda server). Karena itu:

- **Jangan** memakai nama ber-titik dalam satu query (`dbtruck.tb_ceklist`,
  `evisitor.tbl_visit`, `smartlogistic.tbm_tenants`). Kalau DB-nya beda,
  referensi itu gagal.
- Setiap query diambil dari **koneksi yang punya skema default sesuai**:
  `$con` / `$conSL` (truck & smartlogistic), `$con_3` (evisitor),
  `$con_140` (aquan_central).
- Pindah server/DB = cukup ubah nilai `db_open(...)` di file config terkait,
  tanpa menyentuh view.
- `common/db_waiting.php` sudah dipisah: header dari `$con` (truck),
  pengecekan `tbl_visit` dari `$con_3` (evisitor).
- Alamat form eVisitor bisa dioverride: definisikan konstanta `EVISITOR_URL`
  (default `https://adop.danet/evisitor/tamu?ac=regtamu2`).
