# 📋 Todo App - Gestione Attività Giornaliere

**App per la gestione delle attività giornaliere, sviluppata in PHP.**

Un'applicazione web moderna e responsive per la gestione delle attività quotidiane con funzionalità CRUD complete, interfaccia intuitiva e aggiornamenti in tempo reale senza ricaricamento della pagina.

## Importante!
- Questo progetto é stato creato come dimostrazione a future aziende con possibilitá di assunzione lavorativa per me medesimo come dimostrazione delle proprie capacitá,chi visualizzerá e scaricherá il contenuto di questo progetto,avrá accesso tramite licenza MIT

## ✨ Caratteristiche Principali

- **CRUD Completo**: Crea, leggi, aggiorna ed elimina task
- **Interfaccia Moderna**: Design responsive e user-friendly
- **Aggiornamenti Real-time**: Utilizzo di AJAX per aggiornamenti senza ricaricamento
- **Database Auto-Setup**: Creazione automatica del database e delle tabelle
- **Statistiche**: Dashboard con statistiche sui task
- **Priorità**: Sistema di priorità (Alta, Media, Bassa)
- **Date di Scadenza**: Gestione delle scadenze con evidenziazione dei task in ritardo
- **Status Management**: Segna i task come completati o in sospeso

## 🛠️ Tecnologie Utilizzate

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Architettura**: REST API
- **Design**: CSS Grid, Flexbox, Responsive Design

## 📁 Struttura del Progetto

```
todo-app/
├── api/
│   └── tasks.php              # API REST per operazioni CRUD
├── assets/
│   ├── css/
│   │   └── style.css          # Stili CSS moderni e responsive
│   └── js/
│       └── app.js             # Logica JavaScript e AJAX
├── classes/
│   └── Task.php               # Classe per gestione task
├── config/
│   └── database.php           # Configurazione e setup database
├── index.php                  # Pagina principale
├── index.html                 # File DEMO (da eliminare in produzione)
└── README.md                  # Documentazione
```

## 🚀 Installazione e Setup

### Prerequisiti
- Server web (Apache/Nginx)
- PHP 7.4 o superiore
- MySQL 5.7 o superiore
- Estensioni PHP: PDO, PDO_MySQL

### Installazione

1. **Clona o scarica il progetto**
   ```bash
   git clone [repository-url]
   cd todo-app
   ```

2. **Configura il database**
   - Apri `config/database.php`
   - Modifica le credenziali del database se necessario:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'sito_2');
   ```

3. **Avvia il server web**
   - Posiziona i file nella directory del server web
   - Accedi all'applicazione tramite browser

4. **Setup Automatico**
   - Al primo accesso, l'applicazione creerà automaticamente:
     - Il database `sito_2` (se non esiste)
     - Le tabelle necessarie
     - Un utente di default (admin/admin123)

## 📊 Struttura Database

### Tabella `users`
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabella `tasks`
```sql
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 1,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    due_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 🔌 API Endpoints

### GET `/api/tasks.php`
- **Descrizione**: Ottieni tutti i task
- **Parametri**: 
  - `action=stats` - Ottieni statistiche
  - `action=single&id={id}` - Ottieni un task specifico
- **Risposta**: JSON con lista task o statistiche

### POST `/api/tasks.php`
- **Descrizione**: Crea nuovo task
- **Body**: JSON con dati del task
- **Campi**: `title`, `description`, `priority`, `due_date`

### PUT `/api/tasks.php?id={id}`
- **Descrizione**: Aggiorna task esistente
- **Parametri**: 
  - `action=toggle` - Cambia status del task
- **Body**: JSON con dati aggiornati

### DELETE `/api/tasks.php?id={id}`
- **Descrizione**: Elimina task
- **Parametri**: `id` del task da eliminare

## 💡 Funzionalità Dettagliate

### Dashboard Statistiche
- **Totale Task**: Numero totale di task creati
- **Completati**: Task marcati come completati
- **In Sospeso**: Task ancora da completare
- **In Ritardo**: Task scaduti e non completati

### Gestione Task
- **Creazione**: Form intuitivo per creare nuovi task
- **Modifica**: Click sul pulsante modifica per editare
- **Completamento**: Toggle rapido dello status
- **Eliminazione**: Rimozione con conferma

### Sistema Priorità
- 🔴 **Alta**: Per task urgenti e importanti
- 🟡 **Media**: Per task di routine
- 🟢 **Bassa**: Per task opzionali

### Responsive Design
- **Desktop**: Layout a due colonne
- **Tablet**: Layout adattivo
- **Mobile**: Layout a colonna singola

## 🎨 Personalizzazione

### Modifica Stili
Il file `assets/css/style.css` contiene tutti gli stili. Puoi personalizzare:
- Colori del tema
- Font e dimensioni
- Layout e spaziature
- Animazioni

### Estensioni Possibili
- Sistema di autenticazione completo
- Categorie per i task
- Condivisione task tra utenti
- Notifiche push
- Export/Import dati
- API per app mobile

## 🔧 Risoluzione Problemi

### Errori Comuni

1. **Errore di connessione database**
   - Verifica le credenziali in `config/database.php`
   - Assicurati che MySQL sia in esecuzione

2. **Errori JavaScript**
   - Controlla la console del browser
   - Verifica che i percorsi dei file siano corretti

3. **Problemi di permessi**
   - Assicurati che PHP abbia i permessi per creare il database

## 🎭 File Demo

Il progetto include un file `index.html` che serve come **demo statica** dell'interfaccia utente. Questo file:

- **Scopo**: Mostrare il design e l'interfaccia senza necessità di server PHP/MySQL
- **Contenuto**: HTML, CSS e JavaScript in un unico file
- **Dati**: Utilizza dati mock/fittizi per la dimostrazione
- **Utilizzo**: Apribile direttamente nel browser senza server

⚠️ **IMPORTANTE**: Eliminare il file `index.html` quando si mette l'applicazione in produzione, poiché è solo un file dimostrativo.

## 👨‍💻 Autore

**Ciro Casoria**
- 📧 Email: [ciro062012@icloud.com](mailto:ciro062012@icloud.com)
- 🐙 GitHub: [https://github.com/Dreessy](https://github.com/Dreessy)
- 💼 LinkedIn: [https://www.linkedin.com/in/ciro-casoria-01b93b201](https://www.linkedin.com/in/ciro-casoria-01b93b201)

## 📄 Licenza

Questo progetto è stato creato per **opportunità lavorative** e come dimostrazione delle competenze di sviluppo web. È disponibile per la valutazione da parte di potenziali datori di lavoro e collaboratori.

## 🤝 Contributi

I contributi sono benvenuti! Per contribuire:
1. Fai un fork del progetto
2. Crea un branch per la tua feature
3. Commit le tue modifiche
4. Push al branch
5. Apri una Pull Request

## 📞 Supporto

Per supporto o domande, apri un issue nel repository del progetto.

---

**Sviluppato con ❤️ per la gestione efficace delle attività quotidiane**
