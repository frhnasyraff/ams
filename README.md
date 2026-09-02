# AMS

AMS is a web-based asset management system built with PHP and CodeIgniter. It includes asset registration, inventory and location summaries, maintenance workflows, depreciation, disposal, reporting, user roles, and permission management.

## Local setup

1. Install the PHP dependencies:

   ```bash
   composer install
   ```

2. Copy `application/config/database.example.php` to `application/config/database.php` and set the local database connection.

3. If e-mail is required, copy `application/config/email.example.php` to `application/config/email.php` and set the local SMTP credentials.

4. If maps are required, copy `design/js/mapbox-config.example.js` to `design/js/mapbox-config.local.js` and set a restricted public Mapbox token.

5. Configure the application base URL in `application/config/config.php` for the local environment.

6. Serve the project through Apache, Laragon, or another PHP-compatible web server.

## Public repository safety

Private keys, live credentials, database dumps, generated logs, user uploads, dependencies, and local recovery files are intentionally excluded from this repository. Keep those files in the deployment environment and never commit them.
