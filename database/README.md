# Bloomify Database

The live database is MySQL database `bloomify`, managed by XAMPP. MySQL stores its active data under `C:\xampp\mysql\data\bloomify`; it should not be copied into the project folder while MySQL is running.

## Create a backup

Run `backup.bat` after making database changes. It exports the current database to `database/backups` with a timestamp:

```text
database/backups/bloomify-YYYYMMDD-HHMMSS.sql
```

The backup includes tables and current data. The safe project schema and demo seed data are in `database/bloomify.sql`.

The schema file is suitable for Git because it contains only table definitions and demo data. Do not place real users, orders, payment details, or production passwords in it.

## Restore a backup

From the project folder, use the XAMPP MySQL client or phpMyAdmin to import a selected backup file. Import it into the `bloomify` database.

The local XAMPP credentials are `root` with a blank password unless you have changed them.
