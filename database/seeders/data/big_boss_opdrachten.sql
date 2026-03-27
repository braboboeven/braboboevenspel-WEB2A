-------------------------------------------
--            BIG BOSS OPDRACHTEN         --
-------------------------------------------

-- 1.1
-- We zoeken verdachten die in gevangenis "Vught" hebben gezeten.
SELECT DISTINCT v.verdachte_id, v.naam, m.gevangenis
FROM Verdachte v
JOIN Misdaad m ON v.verdachte_id = m.verdachte_id
WHERE m.gevangenis = 'Vught';

-- 1.2
-- Zoek verdachten met misdaad_type "fraude" en gedrag "slecht".
SELECT DISTINCT v.verdachte_id, v.naam, m.misdaad_type, m.gedrag
FROM Verdachte v
JOIN Misdaad m ON v.verdachte_id = m.verdachte_id
WHERE m.misdaad_type = 'fraude'
AND m.gedrag = 'slecht';

-- 1.3
-- Verdachten met misdaad na 2018-01-01.
SELECT DISTINCT v.verdachte_id, v.naam, m.datum_gepleegd
FROM Verdachte v
JOIN Misdaad m ON v.verdachte_id = m.verdachte_id
WHERE m.datum_gepleegd > '2018-01-01';

-- 1.4
-- Verdachten met misdaad_type "diefstal" en geslacht "man".
SELECT DISTINCT v.verdachte_id, v.naam, v.geslacht, m.misdaad_type
FROM Verdachte v
JOIN Misdaad m ON v.verdachte_id = m.verdachte_id
WHERE m.misdaad_type = 'diefstal'
AND v.geslacht = 'man';

-- 1.5
-- Verdachten met slecht gedrag en haarkleur zwart.
SELECT DISTINCT v.verdachte_id, v.naam, v.haarkleur, m.gedrag
FROM Verdachte v
JOIN Misdaad m ON v.verdachte_id = m.verdachte_id
WHERE m.gedrag = 'slecht'
AND v.haarkleur = 'zwart';
