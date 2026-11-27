-- Datenbankschema der Kita-App (rekonstruiert aus einem phpMyAdmin-Export, Datensaetze entfernt).
-- Enthaelt nur Tabellenstruktur, Indizes, AUTO_INCREMENT sowie die Stammdaten
-- fuer Nutzertypen, Abwesenheitsgruende und Eventkategorien.

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `kita_demo` (Beispielname, siehe .env)
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_abr_absencereason`
--

CREATE TABLE `tbl_abr_absencereason` (
  `abr_index` int(11) NOT NULL,
  `abr_text` varchar(255) NOT NULL,
  `abr_adminonlyselect` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `tbl_abr_absencereason`
--

INSERT INTO `tbl_abr_absencereason` (`abr_index`, `abr_text`, `abr_adminonlyselect`) VALUES
(1, 'Krankheit', 0),
(2, 'Urlaub & Sonstiges', 0),
(3, 'Personalmangel', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_abs_absence`
--

CREATE TABLE `tbl_abs_absence` (
  `abs_index` int(11) NOT NULL,
  `abs_date` date NOT NULL,
  `abs_ref_index` int(11) NOT NULL,
  `abs_uty_index` int(11) NOT NULL,
  `abs_abr_index` int(11) NOT NULL,
  `abs_status` tinyint(1) NOT NULL DEFAULT 0,
  `abs_points` int(11) NOT NULL DEFAULT 0,
  `abs_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_clo_closedday`
--

CREATE TABLE `tbl_clo_closedday` (
  `clo_index` int(11) NOT NULL,
  `clo_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_evc_eventcategory`
--

CREATE TABLE `tbl_evc_eventcategory` (
  `evc_index` int(11) NOT NULL,
  `evc_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `tbl_evc_eventcategory`
--

INSERT INTO `tbl_evc_eventcategory` (`evc_index`, `evc_name`) VALUES
(1, 'notbetreuung'),
(2, 'party'),
(3, 'closed'),
(4, 'missing'),
(5, 'info'),
(6, 'w&auml;sche'),
(7, 'open'),
(8, 'Versammlung');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_evt_event`
--

CREATE TABLE `tbl_evt_event` (
  `evt_index` int(11) NOT NULL,
  `evt_date` date NOT NULL,
  `evt_header` varchar(255) NOT NULL,
  `evt_text` text NOT NULL,
  `evt_image` varchar(255) NOT NULL,
  `evt_appendix` varchar(255) NOT NULL,
  `evt_evc_index` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_joa_jobanswer`
--

CREATE TABLE `tbl_joa_jobanswer` (
  `joa_index` int(11) NOT NULL,
  `joa_job_index` int(11) NOT NULL,
  `joa_usr_index` int(11) NOT NULL,
  `joa_from` varchar(50) NOT NULL,
  `joa_to` varchar(50) NOT NULL,
  `joa_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_job_joboffer`
--

CREATE TABLE `tbl_job_joboffer` (
  `job_index` int(11) NOT NULL,
  `job_abs_index` int(11) NOT NULL,
  `job_joa_index` int(11) NOT NULL,
  `job_date` date NOT NULL,
  `job_from` varchar(50) NOT NULL,
  `job_to` varchar(50) NOT NULL,
  `job_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_lau_laundry`
--

CREATE TABLE `tbl_lau_laundry` (
  `lau_index` int(11) NOT NULL,
  `lau_date` date NOT NULL,
  `lau_usr_index` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_lem_listeventmapping`
--

CREATE TABLE `tbl_lem_listeventmapping` (
  `lem_index` int(11) NOT NULL,
  `lem_lis_index` int(11) NOT NULL,
  `lem_evt_index` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_lie_listelement`
--

CREATE TABLE `tbl_lie_listelement` (
  `lie_index` int(11) NOT NULL,
  `lie_lis_index` int(11) NOT NULL,
  `lie_text` varchar(255) NOT NULL,
  `lie_usr_index` int(11) NOT NULL DEFAULT 0,
  `lie_comment` text NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_lin_login`
--

CREATE TABLE `tbl_lin_login` (
  `lin_index` int(11) NOT NULL,
  `lin_usr_index` int(11) NOT NULL,
  `lin_name` varchar(255) NOT NULL,
  `lin_email` varchar(255) NOT NULL,
  `lin_phone` varchar(255) NOT NULL,
  `lin_email__notify` tinyint(1) NOT NULL,
  `lin_phone__notify` tinyint(1) NOT NULL,
  `lin_active` int(11) NOT NULL DEFAULT 1,
  `lin_password` varchar(255) NOT NULL,
  `lin_loginfail` int(11) NOT NULL,
  `lin_locked` tinyint(1) NOT NULL,
  `lin_admin` tinyint(4) NOT NULL DEFAULT 0,
  `lin_edunotalone` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_lis_list`
--

CREATE TABLE `tbl_lis_list` (
  `lis_index` int(11) NOT NULL,
  `lis_name` varchar(255) NOT NULL,
  `lis_text` text NOT NULL,
  `lis_commentenabled` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_log_log`
--

CREATE TABLE `tbl_log_log` (
  `log_index` int(11) NOT NULL,
  `log_text` varchar(255) NOT NULL,
  `log_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_mes_message`
--

CREATE TABLE `tbl_mes_message` (
  `mes_index` int(11) NOT NULL,
  `mes_usr_index` int(11) NOT NULL,
  `mes_usr_email` varchar(255) NOT NULL,
  `mes_usr_signal` varchar(255) NOT NULL,
  `mes_header` varchar(255) NOT NULL,
  `mes_text` text NOT NULL,
  `mes_status` tinyint(4) NOT NULL DEFAULT 0,
  `mes_timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_nbt_notbetreuung`
--

CREATE TABLE `tbl_nbt_notbetreuung` (
  `nbt_index` int(11) NOT NULL,
  `nbt_date` date NOT NULL,
  `nbt_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_pwr_passwordreset`
--

CREATE TABLE `tbl_pwr_passwordreset` (
  `pwr_index` int(11) NOT NULL,
  `pwr_hash` varchar(500) NOT NULL,
  `pwr_lin_index` int(11) NOT NULL,
  `pwr_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_ros_roster`
--

CREATE TABLE `tbl_ros_roster` (
  `ros_index` int(11) NOT NULL,
  `ros_dayofweek` int(11) NOT NULL,
  `ros_usr_index` int(11) NOT NULL,
  `ros_from` varchar(255) NOT NULL,
  `ros_to` varchar(255) NOT NULL,
  `ros_from__real` varchar(20) NOT NULL,
  `ros_to__real` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_ses_session`
--

CREATE TABLE `tbl_ses_session` (
  `ses_index` int(11) NOT NULL,
  `ses_lin_index` int(11) NOT NULL,
  `ses_key` text NOT NULL,
  `ses_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_swi_switch`
--

CREATE TABLE `tbl_swi_switch` (
  `swi_index` int(11) NOT NULL,
  `swi_date` date NOT NULL,
  `swi_usr_index__from` int(11) NOT NULL,
  `swi_abs_points__from` int(11) NOT NULL,
  `swi_abs_points__to` int(11) NOT NULL,
  `swi_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_tok_token`
--

CREATE TABLE `tbl_tok_token` (
  `tok_index` int(11) NOT NULL,
  `tok_lin_index` int(11) NOT NULL,
  `tok_token` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_txt_text`
--

CREATE TABLE `tbl_txt_text` (
  `txt_index` int(11) NOT NULL,
  `txt_desc` varchar(50) NOT NULL,
  `txt_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_usr_user`
--

CREATE TABLE `tbl_usr_user` (
  `usr_index` int(11) NOT NULL,
  `usr_name` varchar(255) NOT NULL,
  `usr_uty_index` tinyint(2) NOT NULL,
  `usr_kid_acclimatisation` tinyint(4) NOT NULL DEFAULT 0,
  `usr_sta_atleasttwo` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_uty_usertype`
--

CREATE TABLE `tbl_uty_usertype` (
  `uty_index` int(11) NOT NULL,
  `uty_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `tbl_uty_usertype`
--

INSERT INTO `tbl_uty_usertype` (`uty_index`, `uty_name`) VALUES
(1, 'Eltern'),
(2, 'Erzieher'),
(3, 'Aushilfe'),
(4, 'Admin');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `tbl_evt_event`
--
ALTER TABLE `tbl_evt_event`
  ADD PRIMARY KEY (`evt_index`);

--
-- Indizes für die Tabelle `tbl_lin_login`
--
ALTER TABLE `tbl_lin_login`
  ADD PRIMARY KEY (`lin_index`),
  ADD UNIQUE KEY `lin_index` (`lin_index`);

--
-- Indizes für die Tabelle `tbl_txt_text`
--
ALTER TABLE `tbl_txt_text`
  ADD PRIMARY KEY (`txt_index`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `tbl_evt_event`
--
ALTER TABLE `tbl_evt_event`
  MODIFY `evt_index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT für Tabelle `tbl_txt_text`
--
ALTER TABLE `tbl_txt_text`
  MODIFY `txt_index` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
