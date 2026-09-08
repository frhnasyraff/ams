# Asset maintenance settings repair

The web asset form reads maintenance settings from Asset Types. Some deployed databases lack `asset_types.maintenance` and `asset_type_items`, causing the lookup to fail and legacy JavaScript to hide the date fields. The mobile detail screen displays the asset date directly and does not use this gate.

## Deployment

Deploy the matching PHP/views/JavaScript and take a database backup, then from `/var/www/html/ams` run:

```sh
mysqldump -u ams_user -p --no-tablespaces rams > /var/tmp/ams-before-maintenance-$(date +%Y%m%d-%H%M%S).sql
mysql -u ams_user -p rams < rams_DB/patch_asset_type_maintenance.sql
```

Proceed with the migration only after the backup command succeeds. No reset or reseed is needed. The repair is also included in `ams_deploy_schema_patch.sql` for future deployments; use the **standalone repair** for this issue, not the entire deployment patch.

The repair adds only missing schema. It does not enable maintenance on all types, change existing dates, or grant permissions. New maintenance flags default to NULL (unconfigured). Existing configured flags remain unchanged.

## Configure and check

1. In **Masters → Asset Types**, edit a type, review **Check For Maintenance**, and save. Optionally set the default frequency and reminder. Frequency means services per year: 1 = yearly, 2 = every six months, 4 = every three months.
2. Open an asset using that type. **Maintenance Date**, **Maintenance Frequency**, and **Reminder In Days** appear in Details. The same fields are available when adding an asset.
3. Defaults fill only empty asset fields. Saved dates and entered frequency/reminder values are not overwritten when changing the type or retrying a lookup.
4. Save and reopen the asset to confirm the schedule. An unconfigured type shows a notice; a saved date stays visible even if maintenance is disabled on its type. Lookup failures show an inline retry instead of silently clearing the form.

The **Maintenance** tab contains history. Adding a maintenance record (for example preventive or corrective) is a separate action requiring `add_maintenance_log_asset`; this repair does not grant that permission.

## Regression tests

```sh
php tests/asset_type_maintenance_test.php
node tests/asset_maintenance_fields_test.js
```

Open `tests/asset_maintenance_browser_test.html` in a browser for local DOM/retry tests. It uses bundled JavaScript and simulated responses, with no live API calls.

`rams_DB` is ignored by default. Include the standalone SQL explicitly when preparing a release; do not stage other database dumps.

## Missing Asset Types menu / Admin access

The menu and controller require `list_assettypes`. Asset Type Colors is a different master. A denied direct link now shows an access setup page (HTTP 403) rather than redirecting to Summary. Accounts allowed to assign permissions also see an Access setup link under Masters.

For the user-requested **Admin-only grant**, back up the database and run:

```sh
mysql -u ams_user -p rams < rams_DB/patch_assettypes_admin_access.sql
```

This separate patch resolves exactly one active role named **Admin** or **Administrator**, adds missing permission definitions and grants list/add/edit Asset Types plus the two parent menu permissions to that role only. It does not grant generic delete access, change user-role membership, or remove/change other roles' existing grants. It aborts if the Admin role cannot be identified unambiguously. No role IDs are hard-coded. This grant is deliberately not included in the general schema migration.

Refresh after applying the patch. If the logged-in user still lacks access, check that the account is actually assigned to that Admin role in User Roles; an account display name of Admin is not proof of its assigned role. Do not grant all permissions as a workaround.
