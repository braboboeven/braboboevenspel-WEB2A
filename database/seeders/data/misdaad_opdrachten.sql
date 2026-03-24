-------------------------------------------
--	     ** KW1C Boeven-Database **      --
--  									 --
--  KONING WILLEM 1 COLLEGE - DEN BOSCH  --
--  2023 								 --
--  MS SQL Database	                     --
--  Use with: https://sqliteonline.com/  --
--  Created with: https://mockaroo.com/  --
--  SQL by: Rob JM Wessels				 --
--										 --
--  VERSIE 202504.1						 --
-------------------------------------------


-- /////////////////////////////////////////////////////////////////////////////////////////
-- ////		BRABOMAFFIA VERDACHTENSPEL       --      VANUIT TABEL MISDAAD		////////////
-- /////////////////////////////////////////////////////////////////////////////////////////

-- We zijn op zoek naar een boef. Ze is een soldaat in de Brabo-maffia. Zij is de eerste van een netwerk die we gaan ontmaskeren.
-- We moeten de kroongetuige vinden. Dit is één van de boeven in dienst van Johan "De Hakkelaar" Verhoek. De Hakkelaar praat regelmatig met Willem "De Neus" Holleeder aan de telefoon. Deze verbinding is beveiligd en afgeschermd, maar jij en je team hebben tóch een afluistertool kunnene plaatsen in beide toestellen. 
-- Om de kroongetuige te kunnen identificeren moeten we eerst heel veel van zijn geheime handlangers, vaak soldaten of luitenanten, uit de brabomaffia-organisatie identificeren.
-- Alles moet snel, want time is ticking....
-- Tijdens de telefoongesprekken ga je al op zoek naar hints om de boef in een database te vinden.

-- $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ --
-- /////////////// VERDACHTE NR. 5 /////////////////// --

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 1
-- scene schets: 2 criminelen spreken elkaar aan de telefoon. Dit gesprek wordt afgeluisterd. Ze hebben het over hun luitenants en soldaten. Ze vertellen / vragen regelmatig naar de criminele verledens van deze mensen.

-- ## ACHTERKANT KAARTJE 1
-- 5.1
-- We moeten een verdachte vinden die een misdaad heeft gepleegd. Zijn/haar gedrag in de gevangenis was toen slecht
-- 34 rows
-----------------------------------------------------------------------

-- ## VOORKANT KAARTJE 2
-- Juiste antwoord van 5.1:
-- 34 rows

SELECT * 
FROM Misdaad 
WHERE gedrag = 'slecht';

-- ## ACHTERKANT KAARTJE 2
-- 5.2
-- Deze verdachte heeft een verdachte_id dat begint met 3...
-- 8 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 3
-- Juiste antwoord van 5.2:
-- 8 rows

SELECT *
FROM Misdaad 
WHERE gedrag = 'slecht'
	AND verdachte_id LIKE '3%';

-- ## ACHTERKANT KAARTJE 3
-- 5.3 
-- Sorteer het resultaat van 5.2 alfabetisch op gevangenisnaam
-- 8 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 4
-- Juiste antwoord van 5.3:
-- 8 rows

SELECT *
FROM Misdaad 
WHERE gedrag = 'slecht'
	AND verdachte_id LIKE '3%'
ORDER BY gevangenis;

-- ## ACHTERKANT KAARTJE 4
-- 5.4
-- De misdaad is gepleegd op 22 oktober 2014 
-- ( 2014-10-22 )
-- 1 row (verdachte_id: 322)


-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 5
-- Juiste antwoord van 5.4:
-- 1 rows

SELECT verdachte_id		
FROM Misdaad 
WHERE gedrag = 'slecht'
	AND verdachte_id LIKE '3%'
	AND datum_gepleegd = '2014-10-22';

-- ## ACHTERKANT KAARTJE 5
-- 5.5
-- Zoek in de tabel Verdachte op wat de naam is van de gevonden verdachte_id van 5.4
-- 1 row

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 6
-- Juiste antwoord van 5.5:
-- 1 row, verdachte_id: 322

SELECT naam
FROM Verdachte
WHERE verdachte_id = 322;


-- ## ACHTERKANT KAARTJE 6
-- Geef de naam van de verdachte STILLETJES door aan de docent. 

-----------------------------------------------------------------------
-- info voor docent:
-- >> juiste naam verdachte 5: Mufi Barnicott, id 322


-- $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ --
-- /////////////// VERDACHTE NR. 6 /////////////////// --

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 1
-- scene schets: 2 criminelen spreken elkaar aan de telefoon. Dit gesprek wordt afgeluisterd. Ze hebben het over hun luitenants en soldaten. Ze vertellen / vragen regelmatig naar de criminele verledens van deze mensen.

-- ## ACHTERKANT KAARTJE 1
-- 6.1
-- Verdachte heeft in Alcatraz gezeten.
-- 22 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 2
-- Juiste antwoord van 6.1:
-- 22 rows

SELECT *
FROM Misdaad
WHERE gevangenis = 'Alcatraz';

-- ## ACHTERKANT KAARTJE 2
-- 6.2
-- alle misdaden waarvan de verdachte heeft gezeten in Alcatraz voor een misdaad gepleegd begin september 2014 (tussen 2014-09-01 en 2014-09-15 ). Gebruik BETWEEN.
-- 2 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 3
-- Juiste antwoord van 6.2:
-- 2 rows

SELECT *
FROM Misdaad
WHERE gevangenis = 'Alcatraz'
	AND datum_gepleegd BETWEEN '2014-09-01' AND '2014-09-15';

-- ## ACHTERKANT KAARTJE 3
-- 6.3
-- Zelfde als de vorige opdracht, maar dan < en > gebruiken.
-- 2 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 4
-- Juiste antwoord van 6.3:
-- 2 rows

SELECT *
FROM Misdaad
WHERE gevangenis = 'Alcatraz'
	AND datum_gepleegd > '2014-09-01' 
	AND datum_gepleegd < '2014-09-15';

-- ## ACHTERKANT KAARTJE 4
-- 6.4
-- Hij/zij gedroeg zich slecht in de gevangenis. 
-- 1 row

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 5
-- Juiste antwoord van 6.4:
-- 1 rows, verdachte_id: 75

SELECT *
FROM Misdaad
WHERE gevangenis = 'Alcatraz'
	AND datum_gepleegd BETWEEN '2014-09-01' AND '2014-09-15'
	AND gedrag = 'slecht';

-- De tekens < en > gebruiken ipv BETWEEN mag ook

-- ## ACHTERKANT KAARTJE 1
-- 6.5
-- Wie is deze man of vrouw? Naam en geslacht opzoeken.

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 6
-- Juiste antwoord van 6.5:
-- 1 rows

SELECT naam, geslacht
FROM Verdachte
WHERE verdachte_id = 75;

-- ## ACHTERKANT KAARTJE 6
-- Geef de naam en geslacht van de verdachte STILLETJES door aan de docent. 

-----------------------------------------------------------------------
-- info voor docent:
-- >> juiste naam verdachte 6: Analiese Bengefield, vrouw, id 75


-- $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ --
-- /////////////// VERDACHTE NR. 7 /////////////////// --

-- ## VOORKANT KAARTJE 1
-- scene schets: 2 criminelen spreken elkaar aan de telefoon. Dit gesprek wordt afgeluisterd. Ze hebben het over hun luitenants en soldaten. Ze vertellen / vragen regelmatig naar de criminele verledens van deze mensen.

-- ## ACHTERKANT KAARTJE 1
-- 7.1
-- De misdaad is bankfraude of openbare dronkenschap. 
-- 26 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 2
-- Juiste antwoord van 7.1:
-- 26 rows
SELECT *
FROM Misdaad
WHERE misdaad_type = 'bankfraude'
	OR misdaad_type = 'openbare dronkenschap';

-- ## ACHTERKANT KAARTJE 2
-- 7.2
-- Sorteer van de vorige zoekopdracht het misdaad_type omgekeerd alfabetisch (dus z-a) 
-- 26 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 3
-- Juiste antwoord van 7.2:
-- 26 rows
SELECT *
FROM Misdaad
WHERE misdaad_type = 'bankfraude'
	OR misdaad_type = 'openbare dronkenschap'
ORDER BY misdaad_type DESC;

-- ## ACHTERKANT KAARTJE 3
-- 7.3
-- De verdachte zit nog steeds voor deze misdaad in de gevangenis (eind_datum IS NULL). Denk aan haakjes om de twee OR-voorwaarden!
-- 2 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 4
-- Juiste antwoord van 7.3:
-- 2 rows
SELECT *
FROM Misdaad
WHERE ( misdaad_type = 'bankfraude'
	OR misdaad_type = 'openbare dronkenschap')
	AND eind_datum IS NULL
ORDER BY misdaad_type DESC;

-- ## ACHTERKANT KAARTJE 4
-- 7.4
-- De verdachte is erg lief in de gevangenis
-- 1 row

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 5
-- Juiste antwoord van 7.4:
-- 1 row. verdachte_id = 162
SELECT *
FROM Misdaad
WHERE ( misdaad_type = 'bankfraude'
	OR misdaad_type = 'openbare dronkenschap')
	AND eind_datum IS NULL
	AND gedrag = 'goed'
ORDER BY misdaad_type DESC;

-- ## ACHTERKANT KAARTJE 5
-- 7.5
-- Wat is de naam van deze verdachte? Wat is de leeftijd?
-- 1 row

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 6
-- Juiste antwoord van 7.5:
-- 1 row. 

SELECT naam, leeftijd
FROM Verdachte
WHERE verdachte_id = 162;

-- ## ACHTERKANT KAARTJE 6
-- Geef de naam en leeftijd van de verdachte STILLETJES door aan de docent. 

-----------------------------------------------------------------------
-- info voor docent:
-- >> juiste naam verdachte 7: Flory Grimmert, vrouw, 65 jaar


-- $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ --
-- /////////////// VERDACHTE NR. 8 /////////////////// --

-- ## VOORKANT KAARTJE 1
-- scene schets: 2 criminelen spreken elkaar aan de telefoon. Dit gesprek wordt afgeluisterd. Ze hebben het over hun luitenants en soldaten. Ze vertellen / vragen regelmatig naar de criminele verledens van deze mensen.

-- ## ACHTERKANT KAARTJE 1
-- 8.1
-- De verdachte heeft voor deze misdaad in gevangenis Vught gezeten.
-- 23 rows

-----------------------------------------------------------------------

-- ## VOORKANT KAARTJE 2
-- Juiste antwoord van 8.1:
-- 23 rows

SELECT *
FROM Misdaad
WHERE gevangenis = 'Vught';

-- ## ACHTERKANT KAARTJE 2
-- 8.2
-- Sorteer de vorige lijst op verdachte_id van laag naar hoog
-- 23 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 3
-- Juiste antwoord van 8.2:
-- 23 rows

SELECT *
FROM Misdaad
WHERE gevangenis = 'Vught'
ORDER BY verdachte_id;

-- ## ACHTERKANT KAARTJE 3
-- 8.3
-- Het misdaad_id is hoger dan het verdachte_id
-- 1 row

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 4
-- Juiste antwoord van 8.3:
-- 1 row

SELECT *
FROM Misdaad
WHERE gevangenis = 'Vught'
	AND misdaad_id > verdachte_id
ORDER BY verdachte_id;

-- ## ACHTERKANT KAARTJE 4
-- 8.4
-- Selecteer het verdachte_id, gevangenis, misdaad_type en misdaad_id van de vorige lijst (in deze volgorde)
-- $$ Voor 500 bonusdollars: voeg toe bij de select: het verschil tussen de twee id's met titel 'verschil')
-- 1 row

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 5
-- Juiste antwoord van 8.4:
-- 1 row
SELECT verdachte_id, gevangenis, misdaad_type, misdaad_id, (misdaad_id - verdachte_id) AS verschil
FROM Misdaad
WHERE gevangenis = 'Vught'
	AND misdaad_id > verdachte_id
ORDER BY verdachte_id;

-- ## ACHTERKANT KAARTJE 5
-- 8.5
-- Wat is de naam van deze verdachte? Wat voor een schoenmaat heeft de verdachte?
-- 1 row
-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 6
-- Juiste antwoord van 8.5:
-- 1 row

SELECT naam, schoenmaat
FROM Verdachte
WHERE verdachte_id = 50;

-- ## ACHTERKANT KAARTJE 6
-- Geef de naam en schoenmaat van de verdachte STILLETJES door aan de docent. 

-----------------------------------------------------------------------
-- info voor docent:
-- >> juiste naam verdachte 8: Ingeberg Brightman, schoenmaat: gemiddeld