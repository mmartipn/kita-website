# Kita-App – Organisations-Webanwendung für eine Elterninitiative

Webanwendung zur Organisation des Alltags einer elternverwalteten Kindertagesstätte
(Eltern-Kind-Initiative). Eltern melden Abwesenheiten ihrer Kinder, Erzieher*innen
melden sich krank oder im Urlaub, Aushilfen bewerben sich auf dadurch entstehende
Vertretungsdienste, und die Einrichtung informiert über Termine, Listen und Dokumente.

Der Kern des Problems, das die Anwendung löst: Wenn Personal ausfällt, muss schnell
und fair entschieden werden, welche Kinder an diesem Tag kommen dürfen (Notbetreuung)
und ob eine Aushilfe den Dienst übernehmen kann. Die Anwendung automatisiert diese
Entscheidung über ein Punktesystem und benachrichtigt die Betroffenen.

## Hintergrund

Das Projekt wurde ursprünglich privat und ehrenamtlich für den produktiven Einsatz in
einer konkreten Einrichtung entwickelt und dort über mehrere Jahre genutzt. Es dient nun
als öffentliches Referenzprojekt für die Bewerbung beim **Prototype Fund 2026**.

Der Code wird im Wesentlichen im ursprünglichen Entwicklungsstand gezeigt. Für die
Veröffentlichung wurden ausschließlich Zugangsdaten, personenbezogene Daten und
einrichtungsspezifische Inhalte entfernt bzw. durch Konfiguration und Platzhalter
ersetzt. Es fand kein Refactoring statt.

## Technologien

- **PHP** (prozedural, ohne Framework), getestet mit PHP 8.2
- **MySQL / MariaDB** über `mysqli`
- **HTML/CSS/JavaScript** mit jQuery 1.11.3
- **PHPMailer 5.2.23** (mitgeliefert unter `app_srv/phpmailer/`) für E-Mail-Versand per SMTP
- **easepick 1.2.1** als Datepicker
- **skel.js 3.0.1** und das HTML-Template „Snapshot“ von TEMPLATED (CC BY 3.0) als Layout-Basis
- **Font Awesome 4.6.3** und **Nunito Sans** (Google Fonts) als Icon- und Schriftdateien

## Architektur

Die Anwendung besteht aus zwei Teilen, die in derselben Installation liegen:

| Teil | Verzeichnis | Aufgabe |
|---|---|---|
| Frontend | `/` (`index.php`, `admin.php`, `formular.php`, `content/`) | Session-basierte Weboberfläche, rendert HTML |
| Server-API | `app_srv/` | JSON-Endpunkte, einzige Schicht mit Datenbankzugriff |

Das Frontend ruft die Server-API per HTTP (cURL, JSON) auf. Dadurch war die API
ursprünglich auch für eine Mobile-App vorgesehen (Push-Token-Registrierung in
`app_srv/kid.php`, Funktion `updateToken`).

Die Datenbanktabellen folgen einem Präfix-Schema (`tbl_abs_absence`,
`tbl_usr_user`, …), die Spalten tragen das Tabellenkürzel als Präfix.

## Funktionsumfang

Die Anwendung unterscheidet vier Nutzertypen: **Eltern**, **Erzieher**, **Aushilfe**
und **Admin**. Jeder Typ sieht ein eigenes Menü.

**Anwesenheit / Abwesenheit**

- Eltern melden Abwesenheit ihres Kindes (Krankheit, Urlaub) für einen Datumsbereich
- Erzieher*innen melden eigene Abwesenheiten
- Aushilfen melden ihre Verfügbarkeit
- Übersicht geplanter Abwesenheiten, Löschen eigener Einträge
- Admin kann „Personalmangel“-Tage festlegen

**Notbetreuung und Punktesystem**

- Fällt Personal aus, berechnet die Anwendung anhand des Dienstplans
  (`tbl_ros_roster`), wie viele Kinder betreut werden können
- Vorschlag, welche Kinder kommen dürfen, auf Basis einer Punkte-Rangliste
  (wer zuletzt zu Hause bleiben musste, steht vorne)
- Eltern können Notbetreuungsplätze untereinander tauschen („Tauschen“)
- Statistik/Rangliste für Eltern und Admin

**Vertretung durch Aushilfen**

- Bei Erzieher-Abwesenheit werden automatisch Vertretungsangebote
  (`tbl_job_joboffer`) erzeugt
- Aushilfen sehen Ausschreibungen und geben Zu-/Absagen mit Zeitfenstern ab
- Automatische Auswahl der besten Zusage, Benachrichtigung der Beteiligten

**Informationsbereitstellung**

- Termine mit Kategorie, Text, Bild und PDF-Anhang („Aktuelles“)
- Dokumentenbereich mit Downloads (Admin lädt hoch)
- Kontaktformular an die Admins

**Einfache organisatorische Listen**

- Wäschedienst-Termine, die Eltern sich selbst zuweisen
- Mitbring- bzw. Aufgabenlisten zu Terminen mit Kommentarfunktion

**Benachrichtigungen**

- E-Mail per SMTP (PHPMailer)
- Nachrichtenwarteschlange (`tbl_mes_message`) für einen externen Signal-Bot,
  der `app_srv/signal.php` abfragt (der Bot selbst ist nicht Teil dieses Repositories)
- Nutzer wählen in den Einstellungen, worüber sie benachrichtigt werden

**Verwaltung (Admin)**

- Nutzer anlegen, bearbeiten, löschen
- Abwesenheiten für alle Nutzertypen eintragen
- Termine, Listen, Wäschedienste, Dokumente verwalten
- Arbeitszeiten (Dienstplan) der Erzieher*innen pflegen
- Saison-Reset („Alles auf null“)

**Login**

- E-Mail/Passwort-Login, Sperre nach zu vielen Fehlversuchen
- Passwort-vergessen-Funktion per E-Mail-Link

## Installation (lokale Testinstallation)

Voraussetzungen: PHP 8.x mit `mysqli` und `curl`, MySQL oder MariaDB, ein Webserver
(Apache/XAMPP) oder der eingebaute PHP-Webserver.

1. Repository in ein Webserver-Verzeichnis klonen.
2. Datenbank anlegen und Schema sowie Demodaten einspielen:

   ```sh
   mysql -u root -p -e "CREATE DATABASE kita_demo CHARACTER SET utf8mb4;"
   mysql -u root -p kita_demo < database/schema.sql
   mysql -u root -p kita_demo < database/demo_data.sql
   ```

3. Konfiguration anlegen:

   ```sh
   cp .env.example .env
   ```

   und die Werte in `.env` anpassen (siehe unten).

4. Anwendung starten, zum Beispiel mit dem eingebauten PHP-Server:

   ```sh
   php -S localhost:8000
   ```

5. `http://localhost:8000/formular.php` im Browser öffnen und mit einem der
   Demo-Logins anmelden (siehe Kopf von `database/demo_data.sql`,
   Passwort jeweils `demo1234`).

Hinweise:

- Das Frontend ruft die API unter `APP_SERVER_URL` per HTTP auf. Beide müssen
  vom PHP-Prozess aus erreichbar sein; bei `php -S` ist das dieselbe Adresse.
- Der Ordner `uploads/` muss für den Webserver beschreibbar sein.
- Ohne gültige SMTP-Daten funktioniert „Passwort vergessen“ nicht; alle anderen
  Funktionen laufen auch ohne Mailversand.

## Konfiguration

Alle Einstellungen werden aus der Datei `.env` im Projektverzeichnis gelesen
(`config.php`). Fehlt ein Wert, greift ein Standardwert für die lokale Installation.

| Variable | Bedeutung | Beispiel |
|---|---|---|
| `DB_HOST` | Datenbank-Host | `localhost` |
| `DB_NAME` | Datenbankname | `kita_demo` |
| `DB_USER` | Datenbank-Benutzer | `example_user` |
| `DB_PASSWORD` | Datenbank-Passwort | `CHANGE_ME` |
| `APP_NAME` | Anzeigename der Installation | `kita-app` |
| `APP_CLIENT_URL` | Basis-URL des Frontends | `http://localhost:8000/` |
| `APP_SERVER_URL` | Basis-URL der Server-API | `http://localhost:8000/app_srv/` |
| `APP_PHONE`, `ADMIN_PHONE` | Telefonnummern für Anzeige | `+49000000000` |
| `ADMIN_USER_IDS` | Logins (`lin_index`), die Admin-Mails erhalten | `7` |
| `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASSWORD`, `SMTP_FROM` | Mailversand | siehe `.env.example` |
| `SIGNAL_API_KEY` | Shared Secret des externen Signal-Bots | zufälliger String |

## Projektstruktur

```
.
├── index.php, index_save.php   Hauptseite und Formularverarbeitung (Nutzer)
├── admin.php, admin_save.php   Verwaltungsoberfläche
├── formular.php, login.php     Login, Passwort vergessen, Impressum
├── token.php, tokenupdate.php  Push-Token-Übergabe (für Mobile-App-Wrapper)
├── content/                    Seiteninhalte je Menüpunkt (per m_id eingebunden)
├── app_srv/                    JSON-API mit Datenbankzugriff
│   ├── login.php, kid.php, educator.php, tempstaff.php, signal.php
│   ├── functions.php           Geschäftslogik (Vertretung, Notbetreuung, Mail)
│   └── phpmailer/              PHPMailer 5.2.23
├── assets/                     CSS, JS, Fonts
├── database/                   schema.sql, demo_data.sql
└── uploads/                    Upload-Ziel (leer, nur .gitkeep)
```

## Projektstatus

Älteres Referenzprojekt (Entwicklung ca. 2023 bis 2025), ursprünglich für einen
konkreten Einsatzzweck entwickelt und nicht als generisches Produkt gedacht.
Bekannte Eigenheiten des Altbestands, die bewusst nicht verändert wurden:

- Passwörter werden als MD5-Hash gespeichert.
- SQL-Abfragen werden per String-Verkettung gebaut (kein Prepared Statement).
- Die API ist nicht per Token abgesichert, sondern verlässt sich auf Session-Prüfung
  und darauf, dass sie nur vom eigenen Frontend aufgerufen wird.
- Eine Reihe von Debug-Ausgaben in `app_srv/functions.php` schreibt bei
  aktiviertem `$debug` in eine lokale `log.txt`.

Das Projekt wird nicht aktiv weiterentwickelt.

## Lizenz

Der eigene Quellcode dieses Projekts steht unter der **GNU General Public License v3.0 oder später**
(siehe [LICENSE](LICENSE)).

Enthaltene Drittkomponenten behalten ihre jeweiligen Lizenzen: PHPMailer (LGPL 2.1),
jQuery (MIT), skel.js (MIT), easepick (GPL 2.0 oder später), Font Awesome (SIL OFL 1.1 / MIT),
Nunito Sans (SIL OFL 1.1), Template „Snapshot“ von TEMPLATED (CC BY 3.0).
