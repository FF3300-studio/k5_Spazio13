# Documentazione Analitica: Boilerplate "Liquido" Kirby 5

## 1. Un sistema "non deterministico" per la gestione dei contenuti digitali

Questo è un meta-strumento, possiamo immaginarlo come un **sistema di gestione dei contenuti** con 3 caratteristiche **critiche**:

- **non-determistico**: Le pagine non hanno template o forme pre-determinate. Quasi ogni pagina utilizza il template `default` e può agire sia come contenuto singolo che come **collection**. Se una pagina ha dei figli, può trasformarsi dinamicamente in una collezione, attivando viste specializzate (mappa, calendario, blog), categorie e filtri, lasciando che sia la struttura dei dati a determinare la forma finale: i paramteri del `parent` influenzano quelli dei `child`.
- **liquido e adattivo**: Riferito al layout della pagina, che non è pre-impostato. Grazie al sistema a blocchi e layout, la composizione della pagina può essere determinata volta per volta in modo differente, adattandosi strategicamente ai casi d'uso e alla natura dei contenuti inseriti.
- **parametrico**: L'intera boilerplate è costruita in modo modulare su più livelli. Dall'architettura del backend (blueprint e logic) al PHP (snippet e model) fino al frontend (SASS/SCSS), tutto sfrutta variabili e parametri interconnessi che permettono una personalizzazione profonda e centralizzata.

---

## 2. Struttura cartelle
Il progetto fonde la struttura classica di Kirby con una toolchain moderna basata su Vite.

```text
.
├── assets/             # Asset sorgenti (SASS/JS) e compilati (build/)
├── content/            # Contenuti gestiti dal Panel (esclusi da Git)
├── kirby/              # Core di Kirby CMS
├── site/               # Logica applicativa e configurazione
│   ├── blueprints/     # Definizione interfacce del Panel (YAML)
│   ├── config/         # Configurazioni, Hooks, Routes
│   ├── controllers/    # Elaborazione dati prima dei template
│   ├── helpers/        # Funzioni di utilità globali (PHP)
│   ├── models/         # Estensioni delle pagine (logiche avanzate)
│   ├── plugins/        # Estensioni core e di terze parti
│   ├── snippets/       # Componenti HTML/PHP riutilizzabili
│   └── templates/      # Pagine finali (HTML/JSON/CSV)
├── vendor/             # Dipendenze PHP (Composer)
└── vite.config.js      # Configurazione della build frontend
```

---

## 3. Struttura file chiave
Alcuni file definiscono il comportamento critico del sistema:

- `site/config/config.php`: Carica i moduli di configurazione (`hooks`, `options`, `routes`).
- `site/models/spreadsheet.php`: Il cuore dell'importazione CSV dinamica.
- `site/controllers/default.php`: Gestisce la logica di filtraggio e categorizzazione per quasi tutti i template.
- `assets/src/sass/theme/settings/_tokens.scss`: Contiene i "geni" del design (colori, font, spacing).
- `site/snippets/layouts.php`: Gestisce il rendering ricorsivo dei blocchi e dei layout.
- `site/plugins/collection-helpers/`: Raccolta di funzioni core per la gestione delle collezioni.

---

## 3bis. Analisi dei Plugin
Il sistema integra plugin custom e di terze parti che ne estendono le capacità core:

- **`block-factory`**: Il motore dei contenuti modulari. Registra blueprint e snippet per blocchi complessi come mappe, slider, CTA, fisarmoniche e il calendario basato su CSV.
- **`collection-helpers`**: (Plugin interno) Centralizza tutte le logiche di filtraggio, gestione dei marker geografici, normalizzazione delle categorie e calcolo dei dati dei form.
- **`locator`**: Fornisce il campo geografico avanzato che permette di selezionare coordinate e indirizzi direttamente su una mappa nel Panel.
- **`kirby-form-block-suite`**: Una suite completa per la creazione di form complessi direttamente all'interno dei blocchi di layout.
- **`k3-whenquery`**: Estende le capacità native di Kirby permettendo di mostrare o nascondere campi e blocchi nel Panel in base a query dinamiche (logica condizionale avanzata).
- **`kirby3-video-master`**: Gestisce l'integrazione di video (YouTube, Vimeo o locali) fornendo opzioni avanzate di autoplay, loop e controlli personalizzati.
- **`cleantext`**: Aggiunge un metodo (`cleanText`) per ripulire il contenuto da tag HTML e formattazioni MD, utile per SEO e anteprime testuali.
- **`kirby-bettersearch`**: Ottimizza l'algoritmo di ricerca interno per fornire risultati più pertinenti e veloci.
- **`kirby3-cookie-banner`**: Gestisce la conformità GDPR e il consenso ai cookie con un'interfaccia integrata.
- **`kirby-code-editor`**: Abilita un editor di codice con sintassi evidenziata nel Panel per inserimenti tecnici.
- **`utility-kirby`**: Set di micro-utility per la manipolazione di stringhe e file.

## 4. Analisi logiche

### A. Ereditarietà Parametrica (Hooks)
Il sistema usa gli **Hooks** (`site/config/hooks.php`) per garantire che i figli "sappiano" cosa fa il genitore. Quando una pagina viene creata o aggiornata, i parametri di visualizzazione (es. "mostra mappa", "abilita categorie") vengono propagati automaticamente. Questo permette di avere blueprint condizionali (`when`) che funzionano in modo coerente anche su gerarchie profonde.

### B. Importazione Dati "Liquida" (`SpreadsheetPage` & `SediPage`)
I modelli in `site/models/` trasformano dati grezzi in oggetti Kirby:
- **`SpreadsheetPage`**: Implementa un parser robusto capace di gestire date in italiano, range orari e mapping di alias. Utilizza un sistema di cache con *Conditional GET* per non rallentare il sito durante il fetch di CSV remoti.
- **`SediPage`**: Esegue lo split di file CSV o Google Sheets per generare **pagine virtuali** (children virtuali). Queste pagine non esistono fisicamente su disco ma sono navigabili come pagine standard, ottimizzando lo storage.

### C. Sistema di Prenotazione e Contatori (Bollini Dinamici)
Una funzione avanzata è situata in `site/helpers/collection.php` (`formDataFor`):
- Calcola in tempo reale la disponibilità di posti per un evento analizzando il numero di sottopagine `formrequest` (iscrizioni) create.
- Fornisce percentuali di riempimento e flag di "esaurito" utilizzati dagli snippet `form-request-counter.php` e `card-info-alt.php` per generare **bollini dinamici** e messaggi di stato.

### D. Temporalizzazione e Filtri Calendario
Lo snippet `collection-calendar-view.php` implementa una logica di temporalizzazione automatica:
- **Default**: Mostra solo gli eventi con data maggiore o uguale a oggi (`strtotime('today')`).
- **Filtro Mese**: Se l'utente seleziona un mese specifico tramite i "chip" filtri, la temporalizzazione di default viene bypassata per mostrare tutti gli eventi di quel periodo.
- Gli appuntamenti vengono estratti tramite l'helper `getOccurrences`, che normalizza dati provenienti sia da pagine Kirby che da righe Spreadsheet.

### E. Scadenza Dinamica del Layout
Nello snippet `layouts.php`, ogni riga di layout può essere parametrizzata con il flag `scadenza`. Quando attivo, l'oggetto del layout (es. un form di iscrizione o un banner promozionale) **scompare automaticamente** se:
1.  La `deadline` impostata nella pagina è trascorsa.
2.  I posti disponibili (`available`) calcolati dal `formData` sono esauriti.

---

## 5. Analisi componenti

### Snippet di Layout e Orchestrazione
- **`layouts.php`**: Gestisce il rendering ricorsivo dei blocchi, calcolando sticky behavior, ancore e scadenze.
- **`check_collection.php`**: È il "direttore d'orchestra" delle viste. Per ogni pagina collection, controlla il campo `collection_options` e decide quale vista caricare (`grid`, `map`, `calendar`, `blog`). Per implementare una nuova vista, è fondamentale aggiornare questo file oltre allo snippet specifico.

### Blocchi Slide Modulari
I file `site/snippets/block-slide-*` implementano uno slider (Swiper.js) che accetta tipi di contenuto misti (immagini, testi, video) mantenendo la stessa struttura logica di navigazione.

---

## 6. Istruzioni per l'uso

1.  **Attivazione Collezione**: Nel Panel, ogni pagina ha un tab "Options". Attivando "Collection Utility", la pagina diventa un contenitore capace di filtrare i propri figli.
2.  **Mapping CSV**: Per le pagine `spreadsheet`, usa il campo "Alias Map" per dire al sistema quale colonna del tuo CSV corrisponde al "Titolo", alla "Data" o ai "Filtri".
3.  **Gestione Categorie**: Le categorie definite nel genitore popolano automaticamente i menu a tendina dei figli tramite query dinamiche.
4.  **Gestione Scadenze**: Per far sparire un blocco automaticamente, attiva "Scadenza" nel layout e imposta una "Deadline" nel tab Content della pagina.

---

## 7. Istruzioni per l'installazione

1.  **Requisiti**: PHP 8.2+, Node 18+, Composer.
2.  **Setup Backend**: `composer install`.
3.  **Setup Frontend**: `npm install`.
4.  **Workflow Sviluppo**: `npm run dev`. Vite gestirà il live reload anche quando modifichi i file PHP di Kirby.
5.  **Build Produzione**: `npm run build`. Trasferire le cartelle `kirby`, `site`, `assets`, `vendor` e il file `index.php`.

---

## 8. Consigli per la personalizzazione

-   **Colori e Font**: Modifica solo `assets/src/sass/theme/settings/_tokens.scss`. Il sistema rigenererà tutte le utility Bootstrap-like.
-   **Nuove Viste**:
    1.  Crea lo snippet `collection-timeline.php`.
    2.  Aggiungi l'opzione `timeline` nel blueprint `site/blueprints/tabs/collection_options.yml` (o dove definito).
    3.  **Fondamentale**: Aggiungi la condizione `elseif($page->collection_options() == 'timeline')` in `site/snippets/check_collection.php` per richiamare il nuovo snippet.
-   **Logica API**: Se hai bisogno di esportare dati per app esterne, usa i template `.json.php` o `.csv.php` già presenti, che serializzano i modelli liquidi in formati standard.
