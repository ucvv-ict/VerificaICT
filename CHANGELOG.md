# Changelog

Tutti i cambiamenti rilevanti di **VerificaICT** vengono documentati in questo file.

Il progetto segue (in modo leggero) il versioning semantico:
- MAJOR: cambiamenti incompatibili
- MINOR: nuove funzionalità compatibili
- PATCH: fix e miglioramenti minori

---
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

