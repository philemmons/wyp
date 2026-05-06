# Shared Hosting Email Deliverability Checklist

Use this runbook for cPanel/LiteSpeed troubleshooting with the current repository.

## Prerequisites

- Diagnostics routes must be reachable:
  - `/diagnostics/mail_delivery_diagnostic.php`
  - `/diagnostics/smtp_delivery_test.php`
- Set `WYP_DIAG_KEY` and pass it as `?key=...`.
- Diagnostics scripts load `includes/init.php` and therefore use the same environment model as the app.
- Current bootstrap requires a root `.env` file; if `.env` is missing, diagnostics will fail before checks run.

## 1. Capture baseline reports

1. Open `/diagnostics/mail_delivery_diagnostic.php?key=YOUR_KEY`.
2. Open `/diagnostics/smtp_delivery_test.php?key=YOUR_KEY`.
3. Save outputs for comparison and host escalation.

## 2. Confirm native mail path (`mail()`)

1. In mail diagnostics output, verify:
   - `mail_function_exists = true`
   - `mail_disabled = false`
2. Run test send:
   - `/diagnostics/mail_delivery_diagnostic.php?key=YOUR_KEY&mail_test_to=you@example.com`
3. If it fails, confirm with host:
   - Whether `mail()` is disabled.
   - Outbound hourly/domain limits.
   - Exim or transport deferrals in server logs.

## 3. Check SMTP network reachability

1. In mail diagnostics output, review probe results for ports `25`, `465`, `587`, `2525`.
2. If `465`/`587` fail, confirm outbound SMTP policy with host.
3. Prefer `587` + STARTTLS when supported by relay.

## 4. Validate SMTP auth/TLS (PHPMailer required)

1. If `smtp_delivery_test.php` reports `phpmailer_not_found`, install PHPMailer first.
2. Run:
   - `/diagnostics/smtp_delivery_test.php?key=YOUR_KEY&to=you@example.com&send=1`
3. Review:
   - `smtp_connect`
   - `send_attempt`
   - `debug_log`
4. Keep debug-level troubleshooting temporary.

## 5. Validate sender identity and DNS

1. SPF: publish one valid SPF policy for your sender domain.
2. DKIM: publish selector record used by your SMTP relay.
3. DMARC: publish `_dmarc.<domain>` and start with monitoring policy.
4. Confirm `From` domain alignment with authenticated sender identity.

## 6. Validate mailbox and account state

1. Verify sender mailbox quota and account health.
2. Verify SMTP credentials outside app (webmail/client test).
3. Inspect recipient spam/junk folders and message headers.
4. Avoid abrupt sender/template/domain changes during troubleshooting.

## 7. Escalate with complete evidence

Provide host support with:

- UTC timestamp
- sender and recipient addresses
- diagnostics output (JSON/text)
- SMTP debug excerpts
- message-id (if available)

## Cleanup

1. Remove diagnostics scripts from production after troubleshooting.
2. Remove or rotate `WYP_DIAG_KEY`.
3. Remove temporary relaxed debugging settings.
