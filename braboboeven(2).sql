-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Gegenereerd op: 13 jan 2026 om 20:28
-- Serverversie: 5.7.24
-- PHP-versie: 8.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `braboboeven`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `alle_boeven_database_vragen`
--

CREATE TABLE `alle_boeven_database_vragen` (
  `vraag_sleutel_id` int(11) NOT NULL,
  `verdachte_nr` int(11) NOT NULL,
  `sub_vraag_nr` decimal(3,1) NOT NULL,
  `vraag_tekst` text NOT NULL,
  `correcte_query` text NOT NULL,
  `verwacht_resultaat_aantal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `alle_boeven_database_vragen`
--

INSERT INTO `alle_boeven_database_vragen` (`vraag_sleutel_id`, `verdachte_nr`, `sub_vraag_nr`, `vraag_tekst`, `correcte_query`, `verwacht_resultaat_aantal`) VALUES
(1, 5, '5.1', 'We moeten een verdachte vinden die een misdaad heeft gepleegd. Zijn/haar gedrag in de gevangenis was toen slecht', 'SELECT * FROM Misdaad WHERE gedrag = \'slecht\'', 34),
(2, 5, '5.2', 'Deze verdachte heeft een verdachte_id dat begint met 3...', 'SELECT * FROM Misdaad WHERE gedrag = \'slecht\' AND verdachte_id LIKE \'3%\'', 8),
(3, 5, '5.3', 'Sorteer het resultaat van 5.2 alfabetisch op gevangenisnaam', 'SELECT * FROM Misdaad WHERE gedrag = \'slecht\' AND verdachte_id LIKE \'3%\' ORDER BY gevangenis', 8),
(4, 5, '5.4', 'De misdaad is gepleegd op 22 oktober 2014 ( 2014-10-22 )', 'SELECT verdachte_id FROM Misdaad WHERE gedrag = \'slecht\' AND verdachte_id LIKE \'3%\' AND datum_gepleegd = \'2014-10-22\'', 1),
(5, 5, '5.5', 'Zoek in de tabel Verdachte op wat de naam is van de gevonden verdachte_id van 5.4', 'SELECT naam FROM Verdachte WHERE verdachte_id = 322', 1),
(6, 6, '6.1', 'Verdachte heeft in Alcatraz gezeten.', 'SELECT * FROM Misdaad WHERE gevangenis = \'Alcatraz\'', 22),
(7, 6, '6.2', 'Alle misdaden waarvan de verdachte heeft gezeten in Alcatraz voor een misdaad gepleegd begin september 2014 (tussen 2014-09-01 en 2014-09-15 ). Gebruik BETWEEN.', 'SELECT * FROM Misdaad WHERE gevangenis = \'Alcatraz\' AND datum_gepleegd BETWEEN \'2014-09-01\' AND \'2014-09-15\'', 2),
(8, 6, '6.3', 'Zelfde als de vorige opdracht, maar dan < en > gebruiken.', 'SELECT * FROM Misdaad WHERE gevangenis = \'Alcatraz\' AND datum_gepleegd > \'2014-09-01\' AND datum_gepleegd < \'2014-09-15\'', 2),
(9, 6, '6.4', 'Hij/zij gedroeg zich slecht in de gevangenis.', 'SELECT * FROM Misdaad WHERE gevangenis = \'Alcatraz\' AND datum_gepleegd BETWEEN \'2014-09-01\' AND \'2014-09-15\' AND gedrag = \'slecht\'', 1),
(10, 6, '6.5', 'Wie is deze man of vrouw? Naam en geslacht opzoeken.', 'SELECT naam, geslacht FROM Verdachte WHERE verdachte_id = 75', 1),
(11, 1, '1.1', 'De verdachte komt een gebouw uit. Het is een vrouw.', 'SELECT * FROM Verdachte WHERE geslacht = \'vrouw\'', 294),
(12, 1, '1.2', 'Van deze verdachte willen we de naam, leeftijd, geslacht en of ze een bril heeft. De kolom \'geslacht\' moet het kopje hebben: \'Is man of vrouw\'', 'SELECT naam, leeftijd, bril, geslacht AS \'Is man of vrouw\' FROM Verdachte WHERE geslacht = \'vrouw\'', 294),
(13, 1, '1.3', 'Ze zien dat de verdachte geen bril draagt.', 'SELECT naam, leeftijd, bril, geslacht AS \'Is man of vrouw\' FROM Verdachte WHERE geslacht = \'vrouw\' AND bril = \'nee\'', 144),
(14, 1, '1.4', 'De verdachte wordt geschat tussen de 28 en 32 jaar (dus ouder dan 28 en jonger dan 32)', 'SELECT naam, leeftijd, bril, geslacht AS \'Is man of vrouw\' FROM Verdachte WHERE geslacht = \'vrouw\' AND bril = \'nee\' AND leeftijd > 28 AND leeftijd < 32', 7),
(15, 1, '1.5', 'Door de wind zien de cops haar blonde haren dansen in de wind.', 'SELECT naam, leeftijd, bril, geslacht AS \'Is man of vrouw\' FROM Verdachte WHERE geslacht = \'vrouw\' AND bril = \'nee\' AND leeftijd > 28 AND leeftijd < 32 AND haarkleur = \'blond\'', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `inzendingen`
--

CREATE TABLE `inzendingen` (
  `inzending_id` int(11) NOT NULL,
  `student_naam` varchar(100) NOT NULL,
  `vraag_sleutel_id` int(11) NOT NULL,
  `ingediende_query` text,
  `ingediend_resultaat` varchar(255) DEFAULT NULL,
  `score_euro` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `inzendingen`
--

INSERT INTO `inzendingen` (`inzending_id`, `student_naam`, `vraag_sleutel_id`, `ingediende_query`, `ingediend_resultaat`, `score_euro`) VALUES
(1, 'Jan Pietersen', 1, 'SELECT * FROM Misdaad WHERE gedrag = \'slecht\'', NULL, 0),
(2, 'Lisa Vrolijk', 2, 'SELECT * FROM Misdaad WHERE gedrag = \'slecht\' AND verdachte_id LIKE \'3%\'', NULL, 0),
(3, 'Dirk Kwibus', 1, 'SELECT * FROM Misdaad WHERE gedrag = \'slecht\' AND gevangenis = \'Alcatraz\'', NULL, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `leerlingen`
--

CREATE TABLE `leerlingen` (
  `idLeerling` int(11) NOT NULL,
  `Naam` varchar(255) NOT NULL,
  `score` int(11) NOT NULL,
  `timer` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `leerlingen`
--

INSERT INTO `leerlingen` (`idLeerling`, `Naam`, `score`, `timer`) VALUES
(1, 'test', 0, '33:01'),
(2, 'ensarrrrr', 1000, '00:45'),
(3, 'dededede', 10000, '15:36');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `misdaad`
--

CREATE TABLE `misdaad` (
  `misdaad_id` int(11) NOT NULL,
  `verdachte_id` int(11) NOT NULL,
  `misdaad_type` varchar(45) NOT NULL,
  `datum_gepleegd` date NOT NULL,
  `gevangenis` varchar(45) NOT NULL,
  `gedrag` varchar(45) DEFAULT NULL,
  `start_datum` date NOT NULL,
  `eind_datum` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `verdachte`
--

CREATE TABLE `verdachte` (
  `verdachte_id` int(11) NOT NULL,
  `naam` varchar(50) NOT NULL,
  `geslacht` varchar(5) NOT NULL,
  `leeftijd` int(11) NOT NULL,
  `lengte` varchar(9) NOT NULL,
  `haarkleur` varchar(5) NOT NULL,
  `kleur_ogen` varchar(5) NOT NULL,
  `gezichtsbeharing` bit(1) NOT NULL,
  `tatoeages` bit(1) NOT NULL,
  `bril` varchar(3) NOT NULL,
  `littekens` bit(1) DEFAULT NULL,
  `schoenmaat` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `verdachte`
--

INSERT INTO `verdachte` (`verdachte_id`, `naam`, `geslacht`, `leeftijd`, `lengte`, `haarkleur`, `kleur_ogen`, `gezichtsbeharing`, `tatoeages`, `bril`, `littekens`, `schoenmaat`) VALUES
(1, 'Ailene Dearlove', 'vrouw', 45, 'klein', 'blond', 'blauw', b'1', b'0', 'nee', b'0', 'groot'),
(2, 'Claiborn Mullard', 'man', 72, 'gemiddeld', 'bruin', 'groen', b'0', b'0', 'ja', b'1', 'klein'),
(3, 'Ardith Cave', 'vrouw', 82, 'gemiddeld', 'rood', 'blauw', b'0', b'0', 'nee', b'1', 'klein'),
(4, 'Quintin Fulkes', 'man', 68, 'klein', 'zwart', 'groen', b'1', b'1', 'nee', b'1', 'klein'),
(5, 'Ganny Nyssen', 'man', 85, 'groot', 'blond', 'bruin', b'1', b'0', 'nee', b'1', 'groot'),
(6, 'Tris Bridgewater', 'man', 46, 'gemiddeld', 'bruin', 'groen', b'0', b'1', 'ja', b'1', 'klein'),
(7, 'Isabella Kift', 'vrouw', 34, 'groot', 'bruin', 'blauw', b'0', b'1', 'nee', b'1', 'klein'),
(8, 'Pearla Toppes', 'vrouw', 90, 'groot', 'bruin', 'bruin', b'0', b'0', 'ja', b'0', 'klein'),
(9, 'Estell Scroggie', 'vrouw', 82, 'gemiddeld', 'bruin', 'bruin', b'1', b'1', 'ja', NULL, 'gemiddeld'),
(10, 'Correna Uttridge', 'vrouw', 67, 'groot', 'rood', 'blauw', b'1', b'0', 'nee', b'1', 'groot'),
(11, 'Saleem Flancinbaum', 'man', 39, 'groot', 'blond', 'bruin', b'0', b'1', 'ja', b'0', 'groot'),
(12, 'Darryl Boise', 'man', 42, 'klein', 'bruin', 'blauw', b'1', b'0', 'ja', b'0', 'groot'),
(13, 'Carrissa McClurg', 'vrouw', 46, 'groot', 'zwart', 'bruin', b'1', b'0', 'nee', b'1', 'klein'),
(14, 'Ode Baggett', 'man', 57, 'klein', 'bruin', 'blauw', b'0', b'1', 'nee', b'0', 'klein'),
(15, 'Morey Handling', 'man', 28, 'klein', 'blond', 'blauw', b'0', b'0', 'nee', b'0', 'groot'),
(16, 'Forest Morales', 'man', 66, 'gemiddeld', 'blond', 'bruin', b'1', b'0', 'ja', b'0', 'gemiddeld'),
(17, 'Laverne Halford', 'vrouw', 71, 'groot', 'blond', 'blauw', b'0', b'0', 'nee', b'0', 'klein'),
(18, 'Ripley McGloin', 'man', 35, 'gemiddeld', 'bruin', 'blauw', b'1', b'0', 'ja', b'0', 'gemiddeld'),
(19, 'Fionnula Fontenot', 'vrouw', 29, 'groot', 'bruin', 'blauw', b'1', b'1', 'ja', b'0', 'klein'),
(20, 'Sinclair Fulk', 'man', 86, 'klein', 'rood', 'blauw', b'0', b'0', 'ja', b'1', 'groot'),
(21, 'Rockwell Bullene', 'man', 57, 'groot', 'blond', 'blauw', b'1', b'1', 'nee', b'1', 'groot'),
(22, 'Foster Mardle', 'man', 34, 'gemiddeld', 'rood', 'bruin', b'1', b'1', 'ja', b'0', 'klein'),
(23, 'Todd Bosward', 'man', 49, 'groot', 'zwart', 'blauw', b'0', b'0', 'nee', b'0', 'gemiddeld'),
(24, 'Silas Levicount', 'man', 70, 'klein', 'blond', 'blauw', b'0', b'0', 'nee', b'0', 'groot'),
(25, 'Perry Normanvell', 'vrouw', 76, 'klein', 'blond', 'blauw', b'1', b'0', 'ja', NULL, 'klein'),
(26, 'Koo McIlwain', 'vrouw', 79, 'groot', 'bruin', 'groen', b'1', b'1', 'nee', b'1', 'klein'),
(27, 'Reidar Imrie', 'man', 80, 'klein', 'blond', 'bruin', b'0', b'0', 'ja', b'1', 'gemiddeld'),
(28, 'Chelsea Karys', 'vrouw', 79, 'klein', 'blond', 'bruin', b'0', b'1', 'ja', b'1', 'klein'),
(29, 'Selene Sauvan', 'vrouw', 38, 'groot', 'blond', 'groen', b'1', b'0', 'ja', b'0', 'klein'),
(30, 'Holmes Treven', 'man', 41, 'groot', 'zwart', 'groen', b'0', b'0', 'ja', b'1', 'klein'),
(31, 'Sherry Trueman', 'vrouw', 36, 'gemiddeld', 'blond', 'bruin', b'0', b'1', 'nee', b'0', 'klein'),
(32, 'Harlen Meegin', 'man', 62, 'klein', 'rood', 'groen', b'0', b'0', 'ja', b'1', 'klein'),
(33, 'Vergil Heffernan', 'man', 45, 'groot', 'zwart', 'blauw', b'0', b'1', 'nee', b'1', 'groot'),
(34, 'Alano Flannery', 'man', 26, 'klein', 'rood', 'groen', b'0', b'0', 'ja', NULL, 'gemiddeld'),
(35, 'Giffer Vaugham', 'man', 34, 'klein', 'rood', 'bruin', b'1', b'0', 'ja', b'1', 'groot'),
(36, 'Ban Wilkisson', 'man', 69, 'klein', 'blond', 'groen', b'0', b'0', 'ja', NULL, 'groot'),
(37, 'Werner Jamme', 'man', 72, 'groot', 'rood', 'bruin', b'0', b'0', 'ja', b'1', 'gemiddeld'),
(38, 'Doe Robertz', 'vrouw', 71, 'klein', 'rood', 'bruin', b'1', b'1', 'ja', b'1', 'gemiddeld'),
(39, 'Ingemar Crowcroft', 'man', 53, 'groot', 'rood', 'blauw', b'0', b'0', 'nee', b'1', 'gemiddeld'),
(40, 'Sandie Matisoff', 'vrouw', 75, 'groot', 'blond', 'bruin', b'0', b'0', 'ja', b'1', 'groot'),
(41, 'Oswell Crebo', 'man', 62, 'klein', 'blond', 'groen', b'1', b'1', 'ja', b'0', 'gemiddeld'),
(42, 'Natividad Woolford', 'vrouw', 32, 'klein', 'rood', 'bruin', b'1', b'1', 'ja', b'1', 'klein'),
(43, 'Abdul Geraldi', 'man', 75, 'klein', 'rood', 'bruin', b'0', b'1', 'ja', b'1', 'klein'),
(44, 'Jaine Madgewick', 'vrouw', 49, 'klein', 'zwart', 'blauw', b'1', b'1', 'nee', b'1', 'gemiddeld');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `vragen_misdaad`
--

CREATE TABLE `vragen_misdaad` (
  `vraag_id` int(11) NOT NULL,
  `verdachte_nr` int(11) NOT NULL,
  `sub_vraag_nr` decimal(3,1) NOT NULL,
  `vraag_tekst` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `vragen_misdaad`
--

INSERT INTO `vragen_misdaad` (`vraag_id`, `verdachte_nr`, `sub_vraag_nr`, `vraag_tekst`) VALUES
(1, 5, '5.1', 'We moeten een verdachte vinden die een misdaad heeft gepleegd. Zijn/haar gedrag in de gevangenis was toen slecht.'),
(2, 5, '5.2', 'Deze verdachte heeft een verdachte_id dat begint met 3...'),
(3, 5, '5.3', 'Sorteer het resultaat van 5.2 alfabetisch op gevangenisnaam'),
(4, 5, '5.4', 'De misdaad is gepleegd op 22 oktober 2014 ( 2014-10-22 )'),
(5, 5, '5.5', 'Zoek in de tabel Verdachte op wat de naam is van de gevonden verdachte_id van 5.4'),
(6, 6, '6.1', 'Verdachte heeft in Alcatraz gezeten.'),
(7, 6, '6.2', 'Alle misdaden waarvan de verdachte heeft gezeten in Alcatraz voor een misdaad gepleegd begin september 2014 (tussen 2014-09-01 en 2014-09-15 ). Gebruik BETWEEN.'),
(8, 6, '6.3', 'Zelfde als de vorige opdracht, maar dan < en > gebruiken.'),
(9, 6, '6.4', 'Hij/zij gedroeg zich slecht in de gevangenis.'),
(10, 6, '6.5', 'Wie is deze man of vrouw? Naam en geslacht opzoeken.'),
(11, 7, '7.1', 'De misdaad is bankfraude of openbare dronkenschap.'),
(12, 7, '7.2', 'Sorteer van de vorige zoekopdracht het misdaad_type omgekeerd alfabetisch (dus z-a)'),
(13, 7, '7.3', 'De verdachte zit nog steeds voor deze misdaad in de gevangenis (eind_datum IS NULL). Denk aan haakjes om de twee OR-voorwaarden!'),
(14, 7, '7.4', 'De verdachte is erg lief in de gevangenis'),
(15, 7, '7.5', 'Wat is de naam van deze verdachte? Wat is de leeftijd?'),
(16, 8, '8.1', 'De verdachte heeft voor deze misdaad in gevangenis Vught gezeten.'),
(17, 8, '8.2', 'Sorteer de vorige lijst op verdachte_id van laag naar hoog'),
(18, 8, '8.3', 'Het misdaad_id is hoger dan het verdachte_id'),
(19, 8, '8.4', 'Selecteer het verdachte_id, gevangenis, misdaad_type en misdaad_id van de vorige lijst (in deze volgorde). $$ Voor 500 bonusdollars: voeg toe bij de select: het verschil tussen de twee id\'s met titel \'verschil\')'),
(20, 8, '8.5', 'Wat is de naam van deze verdachte? Wat voor een schoenmaat heeft de verdachte?');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `vragen_verdachte`
--

CREATE TABLE `vragen_verdachte` (
  `vraag_id` int(11) NOT NULL,
  `verdachte_nr` int(11) NOT NULL,
  `sub_vraag_nr` decimal(3,1) NOT NULL,
  `vraag_tekst` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `vragen_verdachte`
--

INSERT INTO `vragen_verdachte` (`vraag_id`, `verdachte_nr`, `sub_vraag_nr`, `vraag_tekst`) VALUES
(1, 1, '1.1', 'De verdachte komt een gebouw uit. Het is een vrouw.'),
(2, 1, '1.2', 'Van deze verdachte willen we de naam, leeftijd, geslacht en of ze een bril heeft. De kolom \'geslacht\' moet het kopje hebben: \'Is man of vrouw\''),
(3, 1, '1.3', 'Ze zien dat de verdachte geen bril draagt.'),
(4, 1, '1.4', 'De verdachte wordt geschat tussen de 28 en 32 jaar (dus ouder dan 28 en jonger dan 32)'),
(5, 1, '1.5', 'Door de wind zien de cops haar blonde haren dansen in de wind.'),
(6, 2, '2.1', 'Uit een busje stapt een man'),
(7, 2, '2.2', 'Ze zien resten van een whopper in zijn baard zitten'),
(8, 2, '2.3', 'Het haar is bruin.'),
(9, 2, '2.4', 'Ze zien een litteken op zijn wang. (boolean: 0 = niet aanwezig, 1 = wel aanwezig, null = niet bekend)'),
(10, 2, '2.5', 'In zijn busje wordt een versleten pasje gevonden van de bibliotheek. Bij \'naam\' is alleen nog een x zichtbaar.'),
(11, 3, '3.1', 'Verdachte wordt geschat op: ouder dan 70'),
(12, 3, '3.2', 'Verdachte heeft geen tatoeages (BIT, dus 0)'),
(13, 3, '3.3', 'Verdachte heeft bruine ogen'),
(14, 3, '3.4', 'Verdachte wordt geschat op: jonger dan 85'),
(15, 3, '3.5', 'De cops kunnen niet goed zien of verdachte littekens heeft of niet (... IS NULL)'),
(16, 4, '4.1', 'We hebben te maken met persoon van gemiddelde lengte.'),
(17, 4, '4.2', 'Deze persoon heeft grijs haar'),
(18, 4, '4.3', 'Deze persoon heeft ook nog eens grote voeten.'),
(19, 4, '4.4', 'Het blijkt hier om een vrouw te gaan.'),
(20, 4, '4.5', 'Deze vrouw blijkt een snor te hebben (BIT: 1)');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `alle_boeven_database_vragen`
--
ALTER TABLE `alle_boeven_database_vragen`
  ADD PRIMARY KEY (`vraag_sleutel_id`);

--
-- Indexen voor tabel `inzendingen`
--
ALTER TABLE `inzendingen`
  ADD PRIMARY KEY (`inzending_id`),
  ADD UNIQUE KEY `unique_inzending` (`student_naam`,`vraag_sleutel_id`),
  ADD KEY `vraag_sleutel_id` (`vraag_sleutel_id`);

--
-- Indexen voor tabel `leerlingen`
--
ALTER TABLE `leerlingen`
  ADD PRIMARY KEY (`idLeerling`);

--
-- Indexen voor tabel `misdaad`
--
ALTER TABLE `misdaad`
  ADD PRIMARY KEY (`misdaad_id`);

--
-- Indexen voor tabel `verdachte`
--
ALTER TABLE `verdachte`
  ADD PRIMARY KEY (`verdachte_id`);

--
-- Indexen voor tabel `vragen_misdaad`
--
ALTER TABLE `vragen_misdaad`
  ADD PRIMARY KEY (`vraag_id`);

--
-- Indexen voor tabel `vragen_verdachte`
--
ALTER TABLE `vragen_verdachte`
  ADD PRIMARY KEY (`vraag_id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `alle_boeven_database_vragen`
--
ALTER TABLE `alle_boeven_database_vragen`
  MODIFY `vraag_sleutel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT voor een tabel `inzendingen`
--
ALTER TABLE `inzendingen`
  MODIFY `inzending_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `leerlingen`
--
ALTER TABLE `leerlingen`
  MODIFY `idLeerling` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `vragen_misdaad`
--
ALTER TABLE `vragen_misdaad`
  MODIFY `vraag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT voor een tabel `vragen_verdachte`
--
ALTER TABLE `vragen_verdachte`
  MODIFY `vraag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `inzendingen`
--
ALTER TABLE `inzendingen`
  ADD CONSTRAINT `inzendingen_ibfk_1` FOREIGN KEY (`vraag_sleutel_id`) REFERENCES `alle_boeven_database_vragen` (`vraag_sleutel_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
