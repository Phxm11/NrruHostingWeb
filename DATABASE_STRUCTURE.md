# Database structure

## Goal

The database keeps separate tables only for information that can have multiple
rows or is shared by multiple records. Fixed, one-per-request form details are
stored directly on `service_requests`.

## Main application tables

| Table | Purpose |
| --- | --- |
| `applicants` | A requester who may submit multiple requests. |
| `service_requests` | The request and its fixed details: resource selection, services, technical information, two upload paths, fee certification, and acceptance/signature. |
| `developers` | Multiple developers may be listed for one request. |
| `domains` | A request may own multiple domains, including legacy imports. |
| `resource_plans` | Reusable resource-plan master data. |
| `department_codes` | Reusable department-code master data. |
| `approvals` | An auditable, multi-step approval history. |
| `service_accounts` | Multiple hosting/service accounts may be created from one request. |

Authentication and framework tables (`users`, `roles`, `permissions`, pivot
tables, `password_reset_tokens`, `failed_jobs`, and `migrations`) remain
separate because they serve system-wide responsibilities.

## Consolidated fields

The migration `2026_08_24_000002_compact_service_request_details` moves these
tables into `service_requests`:

| Retired table | Destination columns |
| --- | --- |
| `request_resources` | `service_type`, `plan_id`, and `custom_*` fields |
| `request_enabled_services` | `enabled_services` JSON array and `enabled_services_other_detail` |
| `tech_details` | `language_framework`, `database_used`, `port_service_needed`, `needs_external_connection` |
| `attachments` | `system_detail_doc_path`, `screenshot_evidence_path` |
| `fee_certifications` | `agree_to_pay`, `request_fee_waiver`, `waiver_reason` |
| `policy_acceptances` | `accepted`, `signature_image_path`, `accepted_date` |

`service_accounts.applicant_id` stays in place because account-management
screens query and manage accounts directly by applicant. This small duplication
keeps that frequently used relationship simple and explicit.

## Design boundary

This compact design intentionally supports exactly one resource set, one
technical-detail set, one fee certification, one acceptance, and the two fixed
upload slots provided by the public form. If a future form needs repeated
resource sets, arbitrary attachments, or versioned acceptances, introduce a
dedicated child table for that new repeating data.

## Migration safety

The migration copies existing detail rows into their replacement columns before
dropping old tables. It is suitable for the current empty operational dataset
and keeps existing single-value request data when present. As with every schema
migration, back up production data before deployment.
