-------------------------------------------
--	     ** KW1C Boeven-Database **      --
--  									 --
--  KONING WILLEM 1 COLLEGE - DEN BOSCH  --
--  2023 versie 2.0						 --
--  MS SQL Database	                     --
--  Use with: https://sqliteonline.com/  --
--  Created with: https://mockaroo.com/  --
--  SQL by: Rob JM Wessels				 --
--										 --
--  VERSIE 202504.1						 --
-------------------------------------------


-- /////////////////////////////////////////////////////////////////////////////////////////
-- ////		BRABOMAFFIA VERDACHTENSPEL       --      VANUIT TABEL VERDACHTE		////////////
-- /////////////////////////////////////////////////////////////////////////////////////////




-- We zijn op zoek naar een boef. Ze is een soldaat in de Brabo-maffia. Zij is de eerste van een netwerk die we gaan ontmaskeren.
-- We moeten de kroongetuige vinden. Dit is één van de boeven in dienst van Johan "De Hakkelaar" Verhoek. De Hakkelaar praat regelmatig met Willem "De Neus" Holleeder aan de telefoon. Deze verbinding is beveiligd en afgeschermd, maar jij en je team hebben tóch een afluistertool kunnene plaatsen in beide toestellen. 
-- Om de kroongetuige te kunnen identificeren moeten we eerst heel veel van zijn geheime handlangers, vaak soldaten of luitenanten, uit de brabomaffia-organisatie identificeren.
-- Alles moet snel, want time is ticking....
-- Tijdens de telefoongesprekken ga je al op zoek naar hints om de boef in een database te vinden.


-- LET OP: VERDACHTE 1 T/M 4 WORDEN DOOR EEN SURVEILLANCE TEAM GESPOT MET DE TELELENS

-- $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ --
-- /////////////// VERDACHTE NR. 1 /////////////////// --

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 1
-- scene schets: 2 cops met Nikon en zonnebrillen schaduwen een verdachte....

-- ## ACHTERKANT KAARTJE 1
-- 1.1
-- De verdachte komt een gebouw uit. Het is een vrouw.
-- 294 rows
-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 2
-- Juiste antwoord van 1.1:
-- 294 rows

SELECT *
FROM Verdachte
WHERE geslacht = 'vrouw';

-- ## ACHTERKANT KAARTJE 2
-- 1.2
-- Van deze verdachte willen we de naam, leeftijd, geslacht en of ze een bril heeft. De kolom 'geslacht' moet het kopje hebben: 'Is man of vrouw'
-- 294 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 3
-- Juiste antwoord van 1.2:
-- 294 rows

SELECT naam, leeftijd, bril, geslacht AS 'Is man of vrouw'
FROM Verdachte
WHERE geslacht = 'vrouw';

-- ## ACHTERKANT KAARTJE 3
-- 1.3
-- Ze zien dat de verdachte geen bril draagt.
-- 144 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 4
-- Juiste antwoord van 1.3:
-- 144 rows

SELECT naam, leeftijd, bril, geslacht AS 'Is man of vrouw'
FROM Verdachte
WHERE geslacht = 'vrouw'
	AND bril = 'nee';

-- ## ACHTERKANT KAARTJE 4
-- 1.4
-- De verdachte wordt geschat tussen de 28 en 32 jaar (dus ouder dan 28 en jonger dan 32)
-- 7 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 5
-- Juiste antwoord van 1.4:
-- 7 rows

SELECT naam, leeftijd, bril, geslacht AS 'Is man of vrouw'
FROM Verdachte
WHERE geslacht = 'vrouw'
	AND bril = 'nee'
	AND leeftijd > 28
	AND leeftijd < 32;

-- ## ACHTERKANT KAARTJE 5
-- 1.5
-- Door de wind zien de cops haar blonde haren dansen in de wind.
-- 1 row

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 6
-- Juiste antwoord van 1.5:
-- 1 row

SELECT naam, leeftijd, bril, geslacht AS 'Is man of vrouw'
FROM Verdachte
WHERE geslacht = 'vrouw'	
	AND bril = 'nee'
	AND leeftijd > 28
	AND leeftijd < 32
	AND haarkleur = 'blond';

-- ## ACHTERKANT KAARTJE 6
-- Geef de naam van de verdachte STILLETJES door aan de docent. 

-----------------------------------------------------------------------
-- info voor docent:
-- >> juiste naam verdachte 1: Aloisia Rivalant, id 599


-- $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ --
-- /////////////// VERDACHTE NR. 2 /////////////////// --
-- (dit is een lastige, vooral op het eind)

-- Maak zelf even leuke zinnetjes bij de kaartjes...

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 1
-- scene schets: 2 cops met Nikon en zonnebrillen schaduwen een verdachte....

-- ## ACHTERKANT KAARTJE 1
-- 2.1
-- Uit een busje stapt een man
-- 306 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 2
-- Juiste antwoord van 2.1:
-- 306 rows

SELECT *
FROM Verdachte
WHERE geslacht = 'man';

-- ## ACHTERKANT KAARTJE 2
-- 2.2
-- Ze zien resten van een whopper in zijn baard zitten
-- 146 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 3
-- Juiste antwoord van 2.2:
-- 146 rows

SELECT *
FROM Verdachte
WHERE geslacht = 'man'
	AND gezichtsbeharing = 1;


-- ## ACHTERKANT KAARTJE 3
-- 2.3
-- Het haar is bruin. 
-- 23 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 4
-- Juiste antwoord van 2.3:
-- 23 rows

SELECT *
FROM Verdachte
WHERE geslacht = 'man'
	AND gezichtsbeharing = 1
	AND haarkleur = 'bruin';

-- ## ACHTERKANT KAARTJE 4
-- 2.4
-- Ze zien een litteken op zijn wang. (boolean: 0 = niet aanwezig, 1 = wel aanwezig, null = niet bekend)
-- 9 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 5
-- Juiste antwoord van 2.4:
-- 9 rows

SELECT *
FROM Verdachte
WHERE geslacht = 'man'
	AND gezichtsbeharing = 1
	AND haarkleur = 'bruin'
	AND littekens = 1;

-- ## ACHTERKANT KAARTJE 5
-- 2.5
-- In zijn busje wordt een versleten pasje gevonden van de bibliotheek. Bij 'naam' is alleen nog een x zichtbaar.
-- 1 row

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 6
-- Juiste antwoord van 2.5:
-- 1 row

SELECT *
FROM Verdachte
WHERE geslacht = 'man'
	AND gezichtsbeharing = 1
	AND haarkleur = 'bruin'
	AND naam LIKE '%x%';	--> wildcards (gebruik van joker % )


-- ## ACHTERKANT KAARTJE 6
-- Geef de naam van de verdachte STILLETJES door aan de docent. 

-----------------------------------------------------------------------
-- info voor docent:
-- >> Juiste naam verdachte 2: Alexandros Sorrell, id 61


	

-- ///////////////////////////////////////////////////
-- Het surveiance team van de interpol houdt enkele verdachten de gaten met camera en telelens...

-- $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ --
-- /////////////// VERDACHTE NR. 3 /////////////////// --

-- Maak zelf even leuke zinnetjes bij de kaartjes...

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 1
-- scene schets: 2 cops met Nikon en zonnebrillen schaduwen een verdachte....

-- ## ACHTERKANT KAARTJE 1
-- 3.1 
-- Verdachte wordt geschat op: ouder dan 70
-- 188 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 2
-- Juiste antwoord van 3.1:
-- 188 rows

SELECT *
FROM Verdachte
WHERE leeftijd > 70;

-- ## ACHTERKANT KAARTJE 2
-- 3.2 
-- Verdachte heeft geen tatoeages (BIT, dus 0)
-- 103 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 3
-- Juiste antwoord van 3.2:
-- 103 rows

SELECT *
FROM Verdachte
WHERE leeftijd > 70
	AND tatoeages = 0;

-- ## ACHTERKANT KAARTJE 3
-- 3.3
-- Verdachte heeft bruine ogen
-- 33 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 4
-- Juiste antwoord van 3.3:
-- 33 rows

SELECT *
FROM Verdachte
WHERE leeftijd > 70
	AND tatoeages = 0
	AND kleur_ogen = 'bruin';

-- ## ACHTERKANT KAARTJE 4
-- 3.4
-- Verdachte wordt geschat op: jonger dan 85
-- 22 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 5
-- Juiste antwoord van 3.4:
-- 22 rows

SELECT *
FROM Verdachte
WHERE leeftijd > 70
	AND tatoeages = 0
	AND kleur_ogen = 'bruin'
	AND leeftijd < 85;

-- ## ACHTERKANT KAARTJE 5
-- 3.5
-- De cops kunnen niet goed zien of verdachte littekens heeft of niet (... IS NULL)
-- 1 row

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 6
-- Juiste antwoord van 3.5:
-- 1 row

SELECT *
FROM Verdachte
WHERE leeftijd > 70		-- leeftijd bij elkaar
	AND leeftijd < 85
	AND tatoeages = 0
	AND kleur_ogen = 'bruin'
	AND littekens IS NULL;

-- ## ACHTERKANT KAARTJE 6
-- Geef de naam van de verdachte STILLETJES door aan de docent. 

-----------------------------------------------------------------------
-- info voor docent:
-- >> Juiste naam verdachte 3: Hillie Handrik, id 214



-- $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ --
-- /////////////// VERDACHTE NR. 4 /////////////////// --
-- vanuit tbl Verdachte
-- Maak zelf even leuke zinnetjes bij de kaartjes...

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 1
-- scene schets: 2 cops met Nikon en zonnebrillen schaduwen een verdachte....

-- ## ACHTERKANT KAARTJE 1
-- 4.1
-- We hebben te maken met persoon van gemiddelde lengte. 
-- 186 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 2
-- Juiste antwoord van 4.1:
-- 186 rows

SELECT *
FROM Verdachte
WHERE lengte = 'gemiddeld';

-- ## ACHTERKANT KAARTJE 2
-- 4.2
-- Deze persoon heeft grijs haar
-- 62 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 3
-- Juiste antwoord van 4.2:
-- 62 rows

SELECT *
FROM Verdachte
WHERE lengte = 'gemiddeld'
	AND haarkleur = 'grijs';

-- ## ACHTERKANT KAARTJE 3
-- 4.3
-- Deze persoon heeft ook nog eens grote voeten.
-- 16 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 4
-- Juiste antwoord van 4.3:
-- 16 rows

SELECT *
FROM Verdachte
WHERE lengte = 'gemiddeld'
	AND haarkleur = 'grijs'
	AND schoenmaat = 'groot';

-- ## ACHTERKANT KAARTJE 4
-- 4.4
-- Het blijkt hier om een vrouw te gaan.
-- 8 rows

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 5
-- Juiste antwoord van 4.4:
-- 8 rows

SELECT *
FROM Verdachte
WHERE lengte = 'gemiddeld'
	AND haarkleur = 'grijs'
	AND schoenmaat = 'groot'
	AND geslacht = 'vrouw';


-- ## ACHTERKANT KAARTJE 5
-- 4.5
-- Deze vrouw blijkt een snor te hebben (BIT: 1)
-- 1 row

-----------------------------------------------------------------------
-- ## VOORKANT KAARTJE 6
-- Juiste antwoord van 4.5:
-- 1 row

SELECT *
FROM Verdachte
WHERE lengte = 'gemiddeld'
	AND haarkleur = 'grijs'
	AND schoenmaat = 'groot'
	AND geslacht = 'vrouw'
	AND gezichtsbeharing = 1;

-- ## ACHTERKANT KAARTJE 6
-- Geef de naam van de verdachte STILLETJES door aan de docent. 

-----------------------------------------------------------------------
-- info voor docent:
-- >> Juiste naam verdachte 4: Benditee Cauthra, id 195



