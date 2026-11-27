-- Synthetische Demodaten fuer eine lokale Testinstallation.
-- Alle Personen, Einrichtungen und Kontaktdaten sind frei erfunden.
-- Vorher database/schema.sql einspielen.
--
-- Login-Daten (Passwort jeweils: demo1234):
--   eltern1@example.test   (Elternteil, Kind "Mia Muster")
--   eltern2@example.test   (Elternteil, Kind "Ben Beispiel")
--   eltern3@example.test   (Elternteil, Kind "Lea Testkind")
--   erzieher1@example.test (Erzieher/in)
--   erzieher2@example.test (Erzieher/in, Teilzeit)
--   aushilfe1@example.test (Aushilfe)
--   admin@example.test     (Admin, lin_admin=1)
--
-- Hinweis: Die Anwendung speichert Passwoerter als MD5-Hash (Altbestand).

INSERT INTO `tbl_usr_user` (`usr_index`, `usr_name`, `usr_uty_index`, `usr_kid_acclimatisation`, `usr_sta_atleasttwo`) VALUES
(1, 'Mia Muster', 1, 0, 0),
(2, 'Ben Beispiel', 1, 0, 0),
(3, 'Lea Testkind', 1, 1, 0),
(4, 'Erika Erzieherin', 2, 0, 0),
(5, 'Emil Erzieher', 2, 0, 1),
(6, 'Alex Aushilfe', 3, 0, 0),
(7, 'Admin Demo', 4, 0, 0);

INSERT INTO `tbl_lin_login` (`lin_index`, `lin_usr_index`, `lin_name`, `lin_email`, `lin_phone`, `lin_email__notify`, `lin_phone__notify`, `lin_active`, `lin_password`, `lin_loginfail`, `lin_locked`, `lin_admin`, `lin_edunotalone`) VALUES
(1, 1, 'Maria Muster',     'eltern1@example.test',   '+49 000 0000001', 1, 0, 1, MD5('demo1234'), 0, 0, 0, 0),
(2, 2, 'Bernd Beispiel',   'eltern2@example.test',   '+49 000 0000002', 1, 0, 1, MD5('demo1234'), 0, 0, 0, 0),
(3, 3, 'Lena Testkind',    'eltern3@example.test',   '+49 000 0000003', 0, 0, 1, MD5('demo1234'), 0, 0, 0, 0),
(4, 4, 'Erika Erzieherin', 'erzieher1@example.test', '+49 000 0000004', 1, 0, 1, MD5('demo1234'), 0, 0, 0, 0),
(5, 5, 'Emil Erzieher',    'erzieher2@example.test', '+49 000 0000005', 1, 0, 1, MD5('demo1234'), 0, 0, 0, 1),
(6, 6, 'Alex Aushilfe',    'aushilfe1@example.test', '+49 000 0000006', 1, 0, 1, MD5('demo1234'), 0, 0, 0, 0),
(7, 7, 'Admin Demo',       'admin@example.test',     '+49 000 0000007', 1, 0, 1, MD5('demo1234'), 0, 0, 1, 0);

-- Dienstplan der Erzieher (0=Sonntag ... 6=Samstag)
INSERT INTO `tbl_ros_roster` (`ros_index`, `ros_dayofweek`, `ros_usr_index`, `ros_from`, `ros_to`, `ros_from__real`, `ros_to__real`) VALUES
(1, 1, 4, '08:00:00', '16:00:00', '08:00:00', '16:00:00'),
(2, 2, 4, '08:00:00', '16:00:00', '08:00:00', '16:00:00'),
(3, 3, 4, '08:00:00', '16:00:00', '08:00:00', '16:00:00'),
(4, 4, 4, '08:00:00', '16:00:00', '08:00:00', '16:00:00'),
(5, 5, 4, '08:00:00', '14:00:00', '08:00:00', '14:00:00'),
(6, 1, 5, '08:00:00', '12:00:00', '08:00:00', '12:00:00'),
(7, 2, 5, '08:00:00', '12:00:00', '08:00:00', '12:00:00'),
(8, 3, 5, '08:00:00', '12:00:00', '08:00:00', '12:00:00');

-- Beispiel-Abwesenheiten (Kind krank, Kind im Urlaub)
INSERT INTO `tbl_abs_absence` (`abs_index`, `abs_date`, `abs_ref_index`, `abs_uty_index`, `abs_abr_index`, `abs_status`, `abs_points`, `abs_created`) VALUES
(1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 1, 1, 1, 0, 0, NOW()),
(2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 2, 1, 2, 0, 0, NOW());

-- Termine
INSERT INTO `tbl_evt_event` (`evt_index`, `evt_date`, `evt_header`, `evt_text`, `evt_image`, `evt_appendix`, `evt_evc_index`) VALUES
(1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'Elternabend', 'Demo-Termin: Elternabend um 19:30 Uhr.', '', '', 2),
(2, DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Schliesstag', 'Demo-Termin: Die Einrichtung bleibt geschlossen.', '', '', 3);

-- Waeschedienst-Termine
INSERT INTO `tbl_lau_laundry` (`lau_index`, `lau_date`, `lau_usr_index`) VALUES
(1, DATE_ADD(CURDATE(), INTERVAL 5 DAY), 1),
(2, DATE_ADD(CURDATE(), INTERVAL 12 DAY), 0);

-- Einfache Liste (z.B. Mitbringliste fuer ein Fest)
INSERT INTO `tbl_lis_list` (`lis_index`, `lis_name`, `lis_text`, `lis_commentenabled`) VALUES
(1, 'Mitbringliste Sommerfest', 'Wer bringt was mit?', 1);
INSERT INTO `tbl_lie_listelement` (`lie_index`, `lie_lis_index`, `lie_text`, `lie_usr_index`, `lie_comment`) VALUES
(1, 1, 'Kuchen', 1, 'Apfelkuchen'),
(2, 1, 'Getraenke', 0, ''),
(3, 1, 'Obst', 0, '');
INSERT INTO `tbl_lem_listeventmapping` (`lem_index`, `lem_lis_index`, `lem_evt_index`) VALUES
(1, 1, 1);

-- Dokumente (Dateien muessen manuell nach uploads/doc/ gelegt werden)
INSERT INTO `tbl_txt_text` (`txt_index`, `txt_desc`, `txt_text`) VALUES
(1, 'Beispieldokument', 'beispiel.pdf');
