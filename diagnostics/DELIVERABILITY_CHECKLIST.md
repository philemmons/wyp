# Shared Hosting Email Deliverability Checklist

Use this sequence for cPanel/LiteSpeed troubleshooting in the current repository.

## Prerequisites

- Diagnostics endpoints must be reachable: `/diagnostics/mail_delivery_diagnostic.php` and `/diagnostics/smtp_delivery_test.php`.
- Set `WYP_DIAG_KEY` as a server environment variable.
- These scripts read `getenv('WYP_DIAG_KEY')` directly and do not load `includes/init.php`.

## 1. Run both diagnostics endpoints

1. Open `/diagnostics/mail_delivery_diagnostic.php?key=YOUR_KEY`.
2. Open `/diagnostics/smtp_delivery_test.php?key=YOUR_KEY`.
3. Save output for host support.

## 2. Confirm native mail capability

1. In mail diagnostics output, confirm `mail_function_exists=true` and `mail_disabled=false`.
2. Test `mail()` using `/diagnostics/mail_delivery_diagnostic.php?key=YOUR_KEY&mail_test_to=you@example.com`.
3. If failing, ask host whether `mail()` is disabled, whether outbound caps apply, and whether messages are deferred in Track Delivery.

## 3. Check SMTP connectivity first

1. In diagnostics output, review SMTP port probes (`25`, `465`, `587`, `2525`).
2. If `465/587` time out or refuse, confirm outbound SMTP policy with host.
3. Prefer `587` + STARTTLS when relay supports it.

## 4. Test SMTP authentication and TLS (when PHPMailer is installed)

1. If `smtp_delivery_test.php` reports `phpmailer_not_found`, install PHPMailer before send tests.
2. Run `/diagnostics/smtp_delivery_test.php?key=YOUR_KEY&to=you@example.com&send=1`.
3. Review `debug_log` for auth failures, TLS negotiation failures, and relay restrictions.
4. Keep `WYP_SMTP_DEBUG=0` outside active troubleshooting.

## 5. Validate DNS authentication

1. SPF: publish one SPF record for the sender domain and include all real sending systems.
2. DKIM: publish the selector used by your relay and verify key validity.
3. DMARC: publish `_dmarc.domain`, start with `p=none`, then tighten policy after report review.
4. Ensure `From` domain aligns with SPF or DKIM identity.

## 6. Verify reverse DNS and sender identity

1. Request outbound sending IP from host.
2. Confirm PTR resolves and aligns with HELO/EHLO hostname.
3. Use domain-owned mailbox in `From`.
4. Keep visitor email in `Reply-To`.

## 7. Review mailbox and reputation factors

1. Confirm sender mailbox is not over quota.
2. Confirm credentials work in webmail/IMAP/SMTP.
3. Check recipient spam/junk headers.
4. Avoid sudden template or sender identity changes.

## 8. Escalate with complete evidence

Provide host support:

- UTC timestamp
- sender and recipient
- diagnostic output JSON/text
- any SMTP debug excerpts
- message-id if available

## Cleanup

1. Remove `diagnostics/` scripts from production after troubleshooting.
2. Remove or rotate `WYP_DIAG_KEY`.

