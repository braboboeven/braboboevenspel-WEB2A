# Brabo Boevenspel – SQL Game Engine

Deze repo bevat een lichte PHP-backend voor het SQL-"brabomaffia" spel.

## Wat is nieuw
- Speltafels `CaseFile`, `Hint`, `Submission` + seedcases (verdachte, misdaad, Big Boss).
- Game-engine (`php/services/SqlGameEngine.php`) die hints uitdeelt en SQL-inzendingen beoordeelt met 1000/500-puntenlogica.
- JSON API (`php/api.php`) voor cases, hints, inzendingen en leaderboard.

## Configuratie
1. Kopieer `.env.example` naar `.env` en vul databasegegevens (`Host`, `Username`, `Password`, `Db`).
2. Installeer dependencies:
   ```bash
   composer install
   ```
3. Initialiseert de database eenmalig via `boevenDatabaseSetup` (bijv. via een klein PHP-script of tijdelijk in `index.php`). Dit script zet de volledige KW1C-boevendataset klaar (600 verdachten + 80 misdaden) en voert de twee updates uit uit het oorspronkelijke SQL-bestand (gezichtsbeharing op vrouw=0, grijze haarkleur voor leeftijd >70):
   ```php
   <?php
   require_once './php/config/config.php';
   $setup = new boevenDatabaseSetup();
   $setup->setup(); // createTables + insertData + updateData
   ```

## API-overzicht
Base: `php/api.php?action=...`

- **Cases**: `GET ?action=cases`
- **Volgende hint**: `GET/POST ?action=next_hint&case_code=CASE-V001&session_code=SESSION123&group_name=Groep1`
- **SQL insturen**: `POST ?action=submit_sql` met `hint_id`, `session_code`, `group_name`, `sql`
- **Leaderboard**: `GET ?action=leaderboard&session_code=SESSION123`

Responses zijn JSON. Puntenlogica:
- Canoniek dezelfde SQL als verwacht → correct.
- Als de whitespace/caps exact overeenkomt: volledige beloning (default 1000, Big Boss 1200/1500).
- Zelfde canonical maar slordig format (andere spacing/hoofdletters) → halve beloning (default 500 / bonuswaarden).
- Anders: 0 punten.
- Re-submits verhogen alleen als het nieuwe aantal punten hoger is; score wordt met het delta-bedrag opgehoogd.

## Spelregels die zijn gemodelleerd
- 5 queries per verdachte/zaak; 1000/500 beloning per goede SQL; max 5000 per case.
- Big Boss-hints zijn gemarkeerd als bonus en gebruiken hogere beloningen.
- Cases zijn per groep/sessie onafhankelijk via `session_code` en `group_name`.

## Uitbreiden
- Voeg eigen cases/hints toe in `CaseFile` en `Hint` (gebruik `case_code` voor referentie).
- Breid `insertGameCases()` in `php/config/config.php` uit voor nieuwe zaakinserts.
- UI/front-end kan de JSON API aanspreken voor real-time spelbegeleiding.

## Quick test
Gebruik bijvoorbeeld een REST-client:
1. Haal cases: `/php/api.php?action=cases`.
2. Vraag hint: `/php/api.php?action=next_hint&case_code=CASE-V001&session_code=S1&group_name=TeamA`.
3. Dien SQL in met het ontvangen `hint_id` via `submit_sql`.
4. Check leaderboard: `/php/api.php?action=leaderboard&session_code=S1`.
