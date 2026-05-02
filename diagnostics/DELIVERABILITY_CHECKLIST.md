# Shared Hosting Email Deliverability Checklist (cPanel/LiteSpeed)

Use this checklist in order. It is tuned for PHP 8.x + cPanel shared hosting.
Use canonical diagnostic endpoints (`mail_delivery_diagnostic.php`, `smtp_delivery_test.php`) after the naming convention refactor.

## 1) Run diagnostics endpoints

1. Set a temporary server env var: `WYP_DIAG_KEY`.
2. Open:
   - `/diagnostics/mail_delivery_diagnostic.php?key=YOUR_KEY`
   - `/diagnostics/smtp_delivery_test.php?key=YOUR_KEY`
3. Save output for support tickets.

## 2) Outbound SMTP ports and connectivity

1. In `mail_delivery_diagnostic.php`, confirm SMTP host port probes for `25`, `465`, `587`.
2. If `465/587` fail with timeout/refused, ask host if outbound SMTP is blocked.
3. Prefer `587 + STARTTLS` for authenticated relay.

## 3) PHP mail() availability and throttling

1. Confirm `mail_function_exists=true` and `mail_disabled=false`.
2. Run mail test:
   - `/diagnostics/mail_delivery_diagnostic.php?key=YOUR_KEY&mail_test_to=you@example.com`
3. If `mail()` returns false or warnings:
   - ask host if `mail()` is disabled
   - ask for hourly/domain throttling limits
   - check Exim deferrals in cPanel Track Delivery

## 4) SMTP authentication and TLS

1. Use SMTP route:
   - `/diagnostics/smtp_delivery_test.php?key=YOUR_KEY&send=1&to=you@example.com`
2. Review `debug_log`:
   - auth errors: bad username/password, account restrictions
   - TLS errors: cert mismatch, protocol issues
3. Keep `WYP_SMTP_DEBUG=0` in production.

## 5) DNS authentication (SPF, DKIM, DMARC)

1. SPF:
   - publish `v=spf1 ...` on the sender domain.
   - include all actual outbound senders (host relay, third-party SMTP).
2. DKIM:
   - enable DKIM in cPanel Email Deliverability.
   - verify selector exists and key length is current best practice.
3. DMARC:
   - publish `_dmarc.domain`.
   - start with `p=none`, collect reports, then raise policy.
4. Alignment:
   - `From:` domain should align with SPF or DKIM domain.

## 6) Reverse DNS (PTR)

1. Get outbound sender IP from host support (shared hosting often hides this).
2. Test PTR mapping.
3. Ask host to ensure PTR and HELO hostname alignment for outbound mail IP.

## 7) From/Reply-To/header alignment

1. `From:` must be a real mailbox on your domain (for example `noreply@domain`).
2. Avoid using visitor email in `From:`.
3. Put visitor address in `Reply-To`.
4. Keep envelope sender (`Return-Path`) on the same domain when possible.

## 8) Mailbox quota and account health

1. cPanel -> Email Accounts -> verify sender mailbox not over quota.
2. Ensure mailbox can authenticate via webmail/IMAP/SMTP.
3. Check suspended/locked email accounts.

## 9) Spam filtering and reputation checks

1. Check recipient spam/junk folder and full headers.
2. Verify Gmail/Yahoo acceptance using test inboxes.
3. Avoid link-heavy/keyword-heavy templates.
4. Keep consistent sender identity and low complaint rates.

## 10) cPanel-specific logs and escalation

1. cPanel Track Delivery: identify reject/deferral reason codes.
2. If shared host blocks access to deeper logs, open a ticket with:
   - timestamp
   - sender, recipient
   - message id (if available)
   - outputs from both diagnostics scripts

## Cleanup after testing

1. Remove `diagnostics/` scripts from production.
2. Remove `WYP_DIAG_KEY`.

