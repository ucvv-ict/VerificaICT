# Changelog

Tutti i cambiamenti rilevanti di **VerificaICT** vengono documentati in questo file.

Il progetto segue (in modo leggero) il versioning semantico:
- MAJOR: cambiamenti incompatibili
- MINOR: nuove funzionalità compatibili
- PATCH: fix e miglioramenti minori

---
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

