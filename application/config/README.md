# application/config — koneksi database

Semua koneksi database ada di sini. **Satu file untuk satu koneksi.** Tidak ada lagi
definisi koneksi yang tersebar di root atau di dalam view/controller.

| File | Variabel | Target | Dipakai oleh |
|------|----------|--------|--------------|
| `db_dbtruck.php` | `$con` | 10.203.121.109 / uap_smartlogistic / **dbtruck** | config.php (app-wide) |
| `db_smartlogistic.php` | `$con2`, `$conSL` | 10.203.121.109 / uap_smartlogistic / **smartlogistic** | config.php, concloud.php |
| `db_aquan_central.php` | `$con_140` | 10.203.121.140 / user_safety / **aquan_central** | config.php |
| `db_evisitor.php` | `$con_3` | 10.203.121.73 / webuser / **evisitor** | config.php |
| `db_cloud_asn.php` | `$concloud`, `$conASN` | 103.153.61.243 / usersmartlog / **dbasnho** | concloud.php, connectionASN.php |
| `db_smartlogistic_local.php` | `$conSL`, `$conASNPDO` | localhost / root / **smartlogistic** (override lokal/dev) | connectionSL.php, connectionASN.php |
| `db_product_release.php` | `$con73` | 10.203.121.73 / uapp_productcode / **db_product_release** | views/fg (form KIR) |
| `db_dbtruck_local.php` | `$con` | 127.0.0.1 / afandiach / **dbtruck** (lokal/dev) | report.php, lihat_foto.php |
| `db_dbtruck_gate73.php` | `$con73` | 10.203.121.73 / webuser / **dbtruck** | ttat_bwg_api.php, ttat_gate2_bwg_api.php |

## Loader / kompatibilitas

- `/config.php` — konstanta app + memuat koneksi utama (`$con`, `$con2`, `$con_140`, `$con_3`).
- `/concloud.php` — memuat `$concloud`/`$conASN` dan `$con2`/`$conSL`.
- `connectionASN.php`, `connectionSL.php` — loader lama (dipakai `application/assets/TableASN.php`),
  isinya hanya `require` ke file di atas supaya pemanggil lama tetap jalan.

## Catatan

- `routes.php` bukan koneksi: itu peta action → grup view.
- File lama `config_0.php` (tidak terpakai) dipindah ke `/_archive/`.
- `application/assets/TableASN.php` (`seedSupplier()`) masih membuat `new mysqli()` dari
  variabel global; perlu dirapikan terpisah kalau mau 100% eksplisit.
