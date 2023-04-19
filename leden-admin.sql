-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Gegenereerd op: 5 April 2023 om 13.30
-- Serverversie: 5.7.40
-- PHP-versie: 8.0.26


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ledenadministratie`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `boekjaar`
--

CREATE TABLE `boekjaar` (
  `ID` int(11) NOT NULL,
  `Jaar` year(4) NOT NULL,
  `Bedrag` float NOT NULL DEFAULT '100'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `boekjaar`
--

INSERT INTO `boekjaar` (`ID`, `Jaar`, `Bedrag`) VALUES
(1, 2021, 150);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `contributie`
--

CREATE TABLE `contributie` (
  `ID` int(11) NOT NULL,
  `Lid` int(100) NOT NULL,
  `Betaald` float NOT NULL DEFAULT '0',
  `Boekjaar` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `familie`
--

CREATE TABLE `familie` (
  `ID` int(100) NOT NULL,
  `Naam` varchar(255) NOT NULL,
  `Adres` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `familielid`
--

CREATE TABLE `familielid` (
  `ID` int(100) NOT NULL,
  `Naam` varchar(255) NOT NULL,
  `Familie` int(11) NOT NULL,
  `Geboortedatum` date NOT NULL,
  `SoortLid` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `gebruikers`
--

CREATE TABLE `gebruikers` (
  `ID` int(100) NOT NULL,
  `Email` varchar(254) NOT NULL,
  `Wachtwoord` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `gebruikers`
--

INSERT INTO `gebruikers` (`ID`, `Email`, `Wachtwoord`) VALUES
(1, 'admin@ledenadministratie.nl', '$2y$10$AcFhrN7vbnO.OHUXVTROqOOK4fCEVgM8rSEp5FPLaiNb1WbrApyTC'),
(2, 'penningmeester@ledenadministratie.nl', '$2y$10$KP62uMSnt.DbHkZDdHHwT.OVrQ.BOeJV5Mh6M7WJa6B2LaBDL23qS'),
(3, 'secretaris@ledenadministratie.nl', '$2y$10$stqnH2UhAHdQvWTTzcA8y.YFoXyuqQjos41WeDIAOJivXLCcvGmRa');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `soortlid`
--

CREATE TABLE `soortlid` (
  `ID` int(100) NOT NULL,
  `Naam` varchar(255) NOT NULL,
  `ContributiePercentage` int(3) NOT NULL DEFAULT '100',
  `Omschrijving` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `soortlid`
--

INSERT INTO `soortlid` (`ID`, `Naam`, `ContributiePercentage`, `Omschrijving`) VALUES
(1, 'Standaard', 100, 'Dit lid is een standaard lid en betaald het normale contributie bedrag.'),
(2, 'Speciaal', 110, 'Dit lid kan buiten de standaard tijden het club terrein betreden voor vrije tijds besteding.');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `boekjaar`
--
ALTER TABLE `boekjaar`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Jaar` (`Jaar`);

--
-- Indexen voor tabel `contributie`
--
ALTER TABLE `contributie`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Lid` (`Lid`),
  ADD KEY `Boekjaar` (`Boekjaar`);

--
-- Indexen voor tabel `familie`
--
ALTER TABLE `familie`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Adres` (`Adres`);

--
-- Indexen voor tabel `familielid`
--
ALTER TABLE `familielid`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Familie` (`Familie`),
  ADD KEY `Soortlid` (`SoortLid`);

--
-- Indexen voor tabel `gebruikers`
--
ALTER TABLE `gebruikers`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexen voor tabel `soortlid`
--
ALTER TABLE `soortlid`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Naam` (`Naam`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `boekjaar`
--
ALTER TABLE `boekjaar`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT voor een tabel `contributie`
--
ALTER TABLE `contributie`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `familie`
--
ALTER TABLE `familie`
  MODIFY `ID` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `familielid`
--
ALTER TABLE `familielid`
  MODIFY `ID` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `gebruikers`
--
ALTER TABLE `gebruikers`
  MODIFY `ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `soortlid`
--
ALTER TABLE `soortlid`
  MODIFY `ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `contributie`
--
ALTER TABLE `contributie`
  ADD CONSTRAINT `contributie_ibfk_3` FOREIGN KEY (`Boekjaar`) REFERENCES `boekjaar` (`ID`),
  ADD CONSTRAINT `contributie_ibfk_4` FOREIGN KEY (`Lid`) REFERENCES `familielid` (`ID`);

--
-- Beperkingen voor tabel `familielid`
--
ALTER TABLE `familielid`
  ADD CONSTRAINT `familielid_ibfk_1` FOREIGN KEY (`Familie`) REFERENCES `familie` (`ID`),
  ADD CONSTRAINT `familielid_ibfk_2` FOREIGN KEY (`SoortLid`) REFERENCES `soortlid` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
