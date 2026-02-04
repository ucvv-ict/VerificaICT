# VerificaICT

VerificaICT è un gestionale interno per il monitoraggio periodico delle misure
di sicurezza ICT, pensato per enti pubblici e organizzazioni multi-ente.

Il sistema consente di censire attività di controllo, assegnarle agli enti,
registrare verifiche periodiche e visualizzare lo stato di rischio nel tempo
(secondo logica RAG: Verde / Arancione / Rosso).

---

## Obiettivi
- Superare checklist statiche (Excel, Word)
- Garantire controllo continuo nel tempo
- Supportare RTD, IT e audit interni
- Storico verificabile delle attività svolte

---

## Concetti chiave

- **Ente**  
  Perimetro logico (es. Comune, Azienda, Unione)

- **Attività di sicurezza**  
  Controlli astratti e riutilizzabili (es. backup, firewall, password)

- **Tag**  
  Classificazione trasversale (Firewall, Backup, GDPR, Misure Minime, ecc.)

- **Verifica**  
  Ogni controllo viene registrato con esito e data

- **Stato RAG**
  Lo stato NON è manuale:
  - Verde: controllo recente
  - Arancione: in scadenza
  - Rosso: controllo scaduto o mancante

---

## Stack tecnico

- Laravel 12
- Filament v4
- MySQL / MariaDB
- Autenticazione con supporto 2FA (TOTP)

---

## Architettura logica

- entities
- security_tasks
- entity_security_tasks
- security_checks
- tags
- entity_user

Lo stato viene calcolato dinamicamente in base al tempo
trascorso dall’ultimo controllo.

---

## Stato del progetto

- [x] Architettura definita
- [x] Modello dati completo
- [x] CRUD Filament
- [x] Stato RAG dinamico
- [x] Dashboard operativa
- [ ] Notifiche email
- [ ] Seed misure minime
- [ ] Export report PDF

---

## Uso previsto

Strumento **interno**, non destinato (al momento) a distribuzione pubblica
né a certificazioni ISO / AgID.

---

## Licenza

Uso interno – non distribuito.
  
