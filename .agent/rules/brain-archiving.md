---
trigger: always_on
---

# Brain Logs Archiving Rules

Per questo progetto, ogni volta che vengono generati o aggiornati i file di architettura (`task.md`, `implementation_plan.md`, `walkthrough.md`), devono essere copiati nella cartella del progetto dedicata ai log (non copiare `rules.md` e `task_cleanup.md`.

## Regola di Archiviazione
- **Percorso**: `/_brain_logs/[YYYY-MM-DD_HH-MM]/`
- **Naming**: `[YYYY-MM-DD_HH-MM]_[filename].md`
- **Trigger**: Al completamento di ogni Task o quando richiesto esplicitamente.

---
*Ultimo aggiornamento: 2026-01-22 14:51*
