# Changelog

Tutti i cambiamenti rilevanti di **VerificaICT** vengono documentati in questo file.

Il progetto segue (in modo leggero) il versioning semantico:
- MAJOR: cambiamenti incompatibili
- MINOR: nuove funzionalità compatibili
- PATCH: fix e miglioramenti minori

---
## v0.9.0 - 2026-02-XX

### Added
- Custom Filament admin theme (Tailwind build)
- Refactoring Admin Dashboard with KPI + Entities summary
- Bulk Assignment ported to Filament Page (sync mode, preview modal, live counters)
- 2FA flow fully integrated with custom layout
- Proper logout option during 2FA challenge

### Fixed
- Livewire serialization issues with QR code
- Redirect after 2FA to custom AdminDashboard
- Route handling for custom dashboard (no default Filament dashboard)
- Middleware route safety checks

### Improved
- Panel navigation grouping (Operatività / Configurazione)
- Authentication flow consistency

## v0.8.0

### Improved
- Bulk assignment migrated to Filament native page
- Sync mode support
- Live task and entity counters
- Modal preview with impact summary


## v0.7.0 - 2026-02-13

### Added
- UserResource per gestione admin/operatori
- EntityResource per gestione enti
- Middleware admin e operator
- Login unificata con redirect dinamico per ruolo
- Separazione navigation groups (Operatività / Configurazione)

### Changed
- Redirect post-login basato su is_admin
- Aggiornato flusso 2FA con redirect condizionale
- Ristrutturazione routing web.php

### Security
- Blocco accesso panel Filament per operatori
- Blocco accesso area operatore per admin

## v0.6.0 - Operator UX Improvements

### Added
- Quick action buttons (OK / KO / NA) directly in operator dashboard
- Tag display inside task cards
- Responsible user display inside task cards

### Improved
- Operator dashboard filtering (entity, tag, status)
- Default view shows only critical and warning tasks
- Accordion layout by status
- Reduced visual overload for large task lists

## v0.5.0 - Admin Audit Log

### Added
- Administrative audit log table
- Logging of bulk and sync assignments
- Audit log view with pagination
- Filtering by action and user

### Improved
- Governance traceability
- Administrative accountability

## v0.4.0 - Admin Global Dashboard

### Added
- Global administrative dashboard aggregated by entity
- Compliance percentage calculation per entity
- Visual status badges (Criticità / Attenzione / OK)
- Compliance progress bar per entity
- Sorting by number of critical tasks

### Improved
- Managerial visibility of overall compliance status
- Governance-level overview of all active entities

## v0.3.0 - Admin Bulk Assignment

### Added
- Bulk assignment by specific tasks
- Bulk assignment by tags
- Base package assignment
- Preview before applying assignments
- Dynamic UI with JS show/hide logic

### Improved
- Professionalized admin configuration workflow

## v0.2.0 - Operator Area

### Added
- Custom operator dashboard
- Grouped controls by status (critical / warning / ok)
- Execution of security checks with esito ok/ko/na
- Check history with operator and timestamp

### Improved
- Priority-based dashboard ordering
- Visual status sections

## [0.1.0] – 2026-02-04

### Added
- Architettura base del progetto VerificaICT
- Gestione multi-ente (Entities)
- Catalogo attività di sicurezza (Security Tasks)
- Assegnazione attività per ente
- Sistema di TAG multipli per classificazione trasversale
- Storico verifiche con esito e data
- Calcolo dinamico dello stato RAG (Verde / Arancione / Rosso)
- Dashboard operativa con priorità visive
- Gestione utenti associati agli enti con ruoli (admin / operatore / audit)

### Technical
- Laravel 12
- Filament v4
- MySQL / MariaDB
- Stato di sicurezza calcolato (non persistito)
- Modello dati audit-friendly

### Notes
- Versione MVP per uso interno
- Notifiche email e report PDF non ancora inclusi

