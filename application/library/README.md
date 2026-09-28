# AquaLibrary

Shared PHP library for the SmartLogistic-family apps (smartlogistic, hptiga,
finance, truck, asn2.0, ...).

## Layout

```
autoload.php        boot wiring: loads core helpers, model/Table, model/ApiModel,
                    Auth/User, registers CSRF session + Debuger error handlers
                    (plus an App\ namespace PSR-4-style loader)
Auth/               User - session/profile/claims + login via ApiModel
core/               Cache, CSRF, Debug, utility_db, utility_function_withoutJS
model/              Table     - mysqli query-builder / table wrapper
                    TableASN  - PDO-based ASN (supplier/head-office) wrapper
                    ApiModel  - REST client (static get/post/ApiCall/RefreshToken)
                                with bearer-token handling
```

Notes:
- `model/Table.php` is the merged generation: hptiga's `DB_DEFAULT` support,
  tightened `encaseValue`/empty-string handling and `assignVariable()`, plus the
  smartlogistic transaction/raw-query helper set (`transBegin` / `transCommit` /
  `transRollback`, `query`, `resultArray`, `affectedRows`, `error`) used by local
  model ports such as `GRASN::execTrans()`. `getdbName()` honours `DB_DEFAULT`
  when the app defines it and falls back to the global `$db_default` otherwise.
- `ApiClient.php` was dropped: its constants/functions are provided by each
  app's root `ApiClient.inc`, its token flow by `model/ApiModel.php`, and
  login by `Auth/User.php`. Nothing loaded it.
- Runtime artifacts (`/cache/`, `/core/logs/`, `*.log`) are gitignored; they are
  written by the consuming apps inside the submodule checkout.

## How apps consume it (git submodule)

Mount the whole repo at each app's `application/library`:

```sh
git submodule add https://github.com/AgungDwiW/AquaLibrary.git application/library
```

The PHP files resolve app-relative config includes
(`application/config/connection*.php`) from the app's document root, exactly
as they did when the folder lived inside each app, so mounting at
`application/library` keeps every include working unchanged. The app root must
define `APP_DIR` and (recommended) `DB_DEFAULT`; boot files that previously
depended on `ApiClient.php` should include the app's root `ApiClient.inc`
first (as `index.php` already does in hptiga).

## Contributing

Edit inside the submodule checkout of one app (or this repo directly), commit
here, push, then bump the pointer in every consuming app:

```sh
cd application/library
git commit -am "..."
git push
cd ..
git add application/library && git commit -m "bump AquaLibrary"
```

Never edit a consumed copy outside this repo: divergence between apps is
exactly what this repo exists to end.
