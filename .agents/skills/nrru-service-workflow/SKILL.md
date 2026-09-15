---
name: nrru-service-workflow
description: Use when changing or reviewing NRRU Hosting Web service requests, accounts, domains, renewals, staff access, or private files in Laravel 10. Do not use for documentation-only changes.
---

# NRRU Hosting Service Workflow

Read the repository `AGENTS.md` before acting. Treat it as the authoritative source for project-wide rules and commands.

## Preserve these invariants

- The application records and manages service registrations. It does not provision Hosting or virtual machines.
- Active staff have equal back-office access; do not infer roles or multi-stage approval.
- Check each model's custom primary key, timestamp configuration, fillable fields, casts, and foreign keys before changing persistence logic.
- Keep each requester's data isolated per request.
- Keep attachments and signatures outside the public directory and serve them only through an authorized controller using `RequestFiles` path validation.
- Use validated input and a transaction when a change updates multiple tables.
- Do not operate on the live database, import legacy data, expose secrets, or weaken test-database safeguards.

## Inspect the affected workflow

Trace the relevant named routes, request validation, controllers, models, migrations, Blade views, and existing feature tests before editing. Preserve existing Laravel MVC conventions and Thai UI text.

## Select verification by impact

- Requests and service accounts: `ServiceWorkflowTest`
- Renewals and expiration history: `ServiceRenewalTest`
- Attachments, signatures, and private paths: `PrivateFilesTest`
- Installation, seeders, and staff setup: `HandoverInstallationTest`

For changed PHP files, run syntax checks and the focused test suite after clearing the configuration cache. Run the full suite for shared schema, authentication, or cross-workflow changes. For Blade changes, compile and clear the view cache and inspect affected desktop and mobile states when possible. Finish with `git diff --check` and report only checks actually run.
