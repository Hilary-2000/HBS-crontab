# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Running Scripts

Scripts are PHP files executed either via cron (web requests to LAMPP/Apache) or from the CLI:

```bash
# Run via CLI
php /opt/lampp/htdocs/crontab/<script>.php

# Run via web (LAMPP must be running)
curl http://localhost/crontab/<script>.php
```

Start/stop LAMPP:
```bash
sudo /opt/lampp/lampp start
sudo /opt/lampp/lampp stop
```

## Architecture Overview

This is an automated cron job system for a multi-tenant ISP billing platform (Hypbits). It manages MikroTik router clients across multiple ISP organizations.

### Two-tier database model

- **Central DB** (`mikrotik_cloud_manager`): holds all `organizations` rows, each with an `organization_database` field pointing to its own MySQL database.
- **Per-org DB** (e.g. `mikrotik_cloud`, `HBS106`): contains `client_tables`, `sms_tables`, `settings`, `remote_routers`, etc.

All multi-org scripts loop: connect to `mikrotik_cloud_manager` → fetch active orgs → reconnect to each org's DB → perform work.

### Shared infrastructure

| File | Purpose |
|------|---------|
| `db_credential.php` | Raw DB credentials (`$hostname`, `$dbusername`, `$dbpassword`) |
| `db_connect.php` | Opens `$conn1` to `mikrotik_cloud_manager` using credentials above |
| `shared_functions.php` | `activate_user()`, `deactivate_client()`, `send_sms()`, `get_sms()`, `message_content()`, `getSMSKeys()`, `isJson()` |
| `allowed_ip.php` | IP allowlist check (currently commented out) + `formatKenyanPhone()` |
| `routeros_api.php` / `routeros_api2.php` | RouterOS API client classes for communicating with MikroTik routers |

### Cron scripts

| Script | Schedule intent | What it does |
|--------|----------------|--------------|
| `activate_deactivate_clients.php` | Every minute | Activates/deactivates clients on router based on `client_status` flag |
| `check_all_clients.php` | Every minute | Handles expiry: auto-renews from wallet, calculates partial-pay days, deactivates if insufficient funds |
| `check_client_status.php` | Hourly | Re-deactivates clients whose expiry fell in the last 24 hours |
| `remindpayment.php` | Daily | Sends SMS reminders to clients expiring yesterday/today/tomorrow (skips those with sufficient wallet) |
| `freeze_clients.php` | Every minute | Unfreeze clients whose freeze period ended; freeze those whose freeze date arrived |
| `import_config_router.php` | On demand | Connects via SSTP VPN to each org's routers and imports a RouterOS config script |
| `organization/activate_organizations.php` | Hourly | Auto-renews or deactivates org subscriptions based on wallet balance |
| `organization/reminder_message.php` | Daily | Sends payment reminders to orgs expiring yesterday/today/tomorrow |
| `organization/sms_misuse.php` | Hourly | Alerts admins via SMS + email when any client exceeds 5 SMS/day |

### Key data conventions

- **Date format**: `YmdHis` (e.g. `20240115143000`) — used for all comparisons and storage.
- **SMS templates**: stored as JSON in `settings` table under `keyword='Messages'`. Template variables: `[client_name]`, `[client_f_name]`, `[exp_date]`, `[monthly_fees]`, `[acc_no]`, `[client_wallet]`, `[trans_amnt]`, `[days_frozen]`, `[frozen_date]`, `[unfreeze_date]`, etc. Organization templates use `[org_name]`, `[org_wallet]`, `[this_month_payment]`, etc.
- **SMS providers**: configured per-org in `settings` table (`sms_api_key`, `sms_partner_id`, `sms_shortcode`, `sms_sender`). Supported senders: `celcom`, `afrokatt`, `hostpinnacle`, `talksasa`, `blessedtexts`.
- **Activate/deactivate API**: calls `https://billing.hypbits.com/activate/{client_id}/{db_name}` and `/deactivate/...` via cURL.
- **Organization billing**: minimum Ksh 1000/month; above 50 clients it's Ksh 20/client. Active clients = those with `next_expiration_date >= 3 months ago` and registered before expiry-5 days.

### `shared_functions.php` vs per-file functions

`shared_functions.php` is the canonical version for client-level operations. The `organization/` scripts define their own local versions of `send_sms()`, `get_sms()`, `message_content()`, and `getSMSKeys()` — these variants operate on the `mikrotik_cloud_manager` DB and use organization-scoped template variables rather than client-scoped ones.
