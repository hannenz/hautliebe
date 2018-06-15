-- phpMyAdmin SQL Dump
-- version 2.8.2.4
-- http://www.phpmyadmin.net
-- 
-- Erstellungszeit: 01. April 2011 um 11:48
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_content_de`
-- 

DROP TABLE IF EXISTS `cmt_content_de`;
CREATE TABLE `cmt_content_de` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_pageid` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_objecttemplate` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_objectgroup` int(11) default NULL,
  `cmt_position` int(11) default NULL,
  `cmt_visible` tinyint(4) default NULL,
  `head1` varchar(255) collate utf8_unicode_ci default NULL,
  `head2` varchar(255) collate utf8_unicode_ci default NULL,
  `head3` varchar(255) collate utf8_unicode_ci default NULL,
  `head4` varchar(255) collate utf8_unicode_ci default NULL,
  `head5` varchar(255) collate utf8_unicode_ci default NULL,
  `text1` text collate utf8_unicode_ci,
  `text2` text collate utf8_unicode_ci,
  `text3` text collate utf8_unicode_ci,
  `text4` text collate utf8_unicode_ci,
  `text5` text collate utf8_unicode_ci,
  `cmt_created` datetime default NULL,
  `cmt_createdby` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_lastmodified` datetime default NULL,
  `cmt_lastmodifiedby` varchar(255) collate utf8_unicode_ci default NULL,
  `image1` varchar(255) collate utf8_unicode_ci default NULL,
  `image2` varchar(255) collate utf8_unicode_ci default NULL,
  `image3` varchar(255) collate utf8_unicode_ci default NULL,
  `image4` varchar(255) collate utf8_unicode_ci default NULL,
  `image5` varchar(255) collate utf8_unicode_ci default NULL,
  `html1` text collate utf8_unicode_ci,
  `file1` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

-- 
-- Daten für Tabelle `cmt_content_de`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_content_languages`
-- 

DROP TABLE IF EXISTS `cmt_content_languages`;
CREATE TABLE `cmt_content_languages` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_languagename` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_language` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_charset` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_addquery` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_position` int(11) NOT NULL default '0',
  `cmt_domain_id` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `cmt_content_languages`
-- 

INSERT INTO `cmt_content_languages` (`id`, `cmt_languagename`, `cmt_language`, `cmt_charset`, `cmt_addquery`, `cmt_position`, `cmt_domain_id`) VALUES (1, 'deutsch', 'de', 'utf8', '', 1, NULL);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_dberrorlog`
-- 

DROP TABLE IF EXISTS `cmt_dberrorlog`;
CREATE TABLE `cmt_dberrorlog` (
  `id` int(11) NOT NULL auto_increment,
  `error_datetime` datetime default NULL,
  `mysql_error_number` int(11) default NULL,
  `mysql_error_message` text collate utf8_unicode_ci,
  `mysql_query` text collate utf8_unicode_ci,
  `script_name` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_pageid` int(11) default NULL,
  `cmt_pagelang` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_applicationid` varchar(255) collate utf8_unicode_ci default NULL,
  `script_querystring` text collate utf8_unicode_ci,
  `cmt_userid` int(11) default NULL,
  `referer_ip` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `cmt_dberrorlog`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_domains`
-- 

DROP TABLE IF EXISTS `cmt_domains`;
CREATE TABLE `cmt_domains` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_domain` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_domain_description` text collate utf8_unicode_ci,
  `cmt_domain_title` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `cmt_domains`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_execute_code`
-- 

DROP TABLE IF EXISTS `cmt_execute_code`;
CREATE TABLE `cmt_execute_code` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_tablename` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_executiontime` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_code` text collate utf8_unicode_ci,
  `cmt_description` text collate utf8_unicode_ci,
  `cmt_isinternal` tinyint(4) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- 
-- Daten für Tabelle `cmt_execute_code`
-- 

INSERT INTO `cmt_execute_code` (`id`, `cmt_tablename`, `cmt_executiontime`, `cmt_code`, `cmt_description`, `cmt_isinternal`) VALUES (1, '6', 'entry_onsave', '{EVAL}\r\n$db = new DBCex();\r\n$query = \\"SELECT cmt_grouptype, cmt_showitems FROM cmt_users_groups WHERE id = \\''\\".$cmt_tabledata[\\''cmt_usergroup\\''].\\"\\''\\";\r\n$db->Query($query);\r\n$r = $db->Get();\r\n$cmt_tabledata[\\''cmt_usertype\\''] = $r[\\''cmt_grouptype\\''];\r\n\r\n// Gruppenrechte übernehmen\r\n$cmt_tabledata[\\''cmt_showitems\\''] = $r[\\''cmt_showitems\\''];\r\n\r\n// Passwort codieren, wenn Eintrag neu\r\nif ($cmt_tabledata[\\''cmt_pass\\''] != urldecode($_POST[\\''old_pass\\''])) {\r\n   $cmt_tabledata[\\''cmt_pass\\''] = md5($cmt_tabledata[\\''cmt_pass\\'']);\r\n}\r\n{ENDEVAL}', '', 1),
(2, '6', 'entry_onload', '{EVAL}\r\n$old_pass = $cmt_tabledata[\\''cmt_pass\\''];\r\n\r\nif ($cmt_action == \\"new\\") {\r\n    $conso=array(\\"b\\",\\"c\\",\\"d\\",\\"f\\",\\"g\\",\\"h\\",\\"j\\",\\"k\\",\\"l\\",\\"m\\",\\"n\\",\\"p\\",\\"r\\",\\"s\\",\\"t\\",\\"v\\",\\"w\\",\\"x\\",\\"y\\",\\"z\\");\r\n    $vocal=array(\\"a\\",\\"e\\",\\"i\\",\\"o\\",\\"u\\");\r\n    $password=\\"\\";\r\n\r\n    srand ((double)microtime()*1000000);\r\n\r\n    for($f=1; $f<=4; $f++) {\r\n        $password.=$conso[rand(0,19)];\r\n        $password.=$vocal[rand(0,4)];\r\n    }\r\n    $cmt_tabledata[\\''cmt_pass\\''] = $password;\r\n}\r\n{ENDEVAL}', 'Schlägt beim Anlegen eines Users ein neues Passwort vor und speichert bei Bearbeitung eines Users das alte Passwort zwecks Sicherung.', 1),
(3, '4', 'entry_onload', '{EVAL}\r\nif ($cmt_tabledata[\\''cmt_isinternal\\'']  && CMT_USERTYPE != \\"admin\\") {\r\n   //header (\\"Location:\\".SELFURL);\r\n   //exit();\r\n$cmt_abort = 1;\r\n}\r\n{ENDEVAL}', '', NULL),
(4, '13', 'entry_aftersave', '{INCLUDE:\\"applications/appinc_pages_after_edit_actions.inc\\"}', 'Dupliziert auch die Inhalte einer Seite nach dem Duplizieren.', 1),
(5, '13', 'entry_onload', '{INCLUDE:\\"applications/appinc_pages_before_edit_actions.inc\\"}', '', 0);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_export_saved`
-- 

DROP TABLE IF EXISTS `cmt_export_saved`;
CREATE TABLE `cmt_export_saved` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_exportname` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_settings` text collate utf8_unicode_ci,
  `cmt_savedby` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_savedat` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_type` varchar(64) collate utf8_unicode_ci default NULL,
  `cmt_description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `cmt_export_saved`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_fields`
-- 

DROP TABLE IF EXISTS `cmt_fields`;
CREATE TABLE `cmt_fields` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_tablename` varchar(64) collate utf8_unicode_ci default NULL,
  `cmt_fieldname` varchar(64) collate utf8_unicode_ci default NULL,
  `cmt_fieldtype` varchar(64) collate utf8_unicode_ci default NULL,
  `cmt_index` tinyint(1) default '0',
  `cmt_fieldquery` text collate utf8_unicode_ci,
  `cmt_options` text collate utf8_unicode_ci,
  `cmt_default` text collate utf8_unicode_ci,
  `cmt_fielddesc` text collate utf8_unicode_ci,
  `cmt_fieldalias` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=258 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=258 ;

-- 
-- Daten für Tabelle `cmt_fields`
-- 

INSERT INTO `cmt_fields` (`id`, `cmt_tablename`, `cmt_fieldname`, `cmt_fieldtype`, `cmt_index`, `cmt_fieldquery`, `cmt_options`, `cmt_default`, `cmt_fielddesc`, `cmt_fieldalias`) VALUES (1, 'cmt_execute_code', 'cmt_tablename', 'select', NULL, NULL, 'a:12:{s:11:"noselection";s:0:"";s:18:"multiple_separator";s:0:"";s:6:"values";s:0:"";s:7:"aliases";s:0:"";s:10:"from_table";s:10:"cmt_tables";s:22:"from_table_value_field";s:2:"id";s:22:"from_table_alias_field";s:18:"{VAR:cmt_showname}";s:18:"from_table_add_sql";s:44:"WHERE cmt_type=''table'' ORDER BY cmt_showname";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Name der Tabelle / Application', 'Tabelle'),
(2, 'cmt_execute_code', 'cmt_executiontime', 'select', NULL, NULL, 'a:11:{s:11:"noselection";s:0:"";s:18:"multiple_separator";s:0:"";s:6:"values";s:243:"overview_onload\r\noverview_onshow_entry\r\noverview_aftershow_entry\r\noverview_oncomplete\r\nentry_onload\r\nentry_onshow_field\r\nentry_aftershow_field\r\nentry_oncomplete\r\nentry_onsave\r\nentry_aftersave\r\nentry_ondelete\r\nentry_afterdelete\r\nupload_onupload";s:7:"aliases";s:421:"Übersicht: beim Laden\r\nÜbersicht: vor dem  Anzeigen einer Zeile\r\nÜbersicht: nach dem  Anzeigen einer Zeile\r\nÜbersicht: am Ende der Seite\r\nEintrag: beim Laden\r\nEintrag: vor dem Anzeigen jedes Feldes\r\nEintrag: nach dem Anzeigen jedes Feldes\r\nEintrag: am Ende der Seite\r\nEintrag: vor dem Speichern\r\nEintrag: nach dem Speichern\r\nEintrag: vor dem Löschen\r\nEintrag: nach dem Löschen\r\nnach dem Upload, vor Dateispeicherung";s:10:"from_table";s:0:"";s:22:"from_table_alias_field";s:0:"";s:18:"from_table_add_sql";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Zeitpunkt und Ort, zu welchen der PHP-Code ausgeführt werden soll.', 'Ausführungszeitpunkt'),
(3, 'cmt_execute_code', 'cmt_code', 'text', NULL, NULL, '', '{EVAL}\r\n\r\n{ENDEVAL}', 'PHP-Code, der ausgeführt werden soll. Der PHP-Code muss innerhalb eines {EVAL}...{ENDEVAL}-Blockes stehen, da auch alle andere Parser-Makros zur Verfügung stehen.', 'Code-Quelltext'),
(4, 'cmt_execute_code', 'cmt_description', 'text', 0, NULL, '', '', 'Hier kann ein Text eingegeben werden, der den Code-Quelltext beschreibt.', 'Beschreibung'),
(5, 'cmt_execute_code', 'cmt_isinternal', 'flag', NULL, NULL, 'a:1:{s:5:"value";s:0:"";}', '', 'Ist diese Option ausgewählt, wird dieser Quelltext als für das Funktionieren des Systems relevant angesehen und kann nur durch einen Administrator bearbeitet / gelöscht werden.', 'Content-O-Mat Quelltext'),
(10, 'cmt_tables', 'cmt_group', 'select', NULL, NULL, 'a:14:{s:11:"noselection";s:0:"";s:6:"values";s:0:"";s:7:"aliases";s:0:"";s:10:"from_table";s:17:"cmt_tables_groups";s:22:"from_table_value_field";s:2:"id";s:22:"from_table_alias_field";s:19:"{VAR:cmt_groupname}";s:18:"from_table_add_sql";s:21:"ORDER BY cmt_grouppos";s:18:"multiple_separator";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:16:"recursive_parent";s:0:"";s:28:"recursive_parent_value_field";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Wird in dieser Gruppe angezeigt.', 'Gruppe'),
(11, 'cmt_tables', 'cmt_tablename', 'string', 0, '', '', '', 'MySQL-Name der Tabelle.', 'Tabellenname'),
(12, 'cmt_tables', 'cmt_showname', 'string', 0, '', '', '', 'Name / Alias der Tabelle, der in der Navigation angezeigt wird.', 'angezeigter Tabellenname'),
(13, 'cmt_tables', 'cmt_include', 'link', 0, '', 'a:1:{s:4:"path";s:18:"admin/applications";}', '', 'Anwendung / Datei die geladen werden soll.', 'Include-Datei'),
(14, 'cmt_tables', 'cmt_itempos', 'position', 0, '', 'a:1:{s:6:"parent";s:9:"cmt_group";}', '', 'Position innerhalb der Gruppe.', 'Gruppenposition'),
(15, 'cmt_tables', 'cmt_ownservice', 'link', 0, '', 'a:1:{s:4:"path";s:8:"includes";}', '', 'Eigene Datei, die zu den Standard-Suchfunktionen geladen werden soll.', 'Eigener Tabellenservice'),
(16, 'cmt_tables', 'cmt_addvars', 'text', 0, '', '', '', 'Interne und eigene Variablen, um die Tabellendarstellung zu steuern.', 'Zusätzliche Variablen'),
(17, 'cmt_tables', 'cmt_showfields', 'text', 0, '', '', '', 'Felder, die in dieser Reihenfolge in der Tabellenübersicht angezeigt werden sollen.', 'Übersichtsstruktur'),
(18, 'cmt_tables', 'cmt_editstruct', 'text', 0, '', '', '', 'Reihenfolge und Darstellungsoptionen der Felder in der Bearbeitungsansicht eines Eintrags.', 'Editierstruktur'),
(19, 'cmt_tables', 'cmt_type', 'select', 0, '', 'a:2:{s:6:"values";a:2:{i:0;s:5:"table";i:1;s:11:"application";}s:7:"aliases";a:2:{i:0;s:7:"Tabelle";i:1;s:9:"Anwendung";}}', '', 'Art des Eintrags: Datenbanktabelle oder Anwendung', 'Typ'),
(20, 'cmt_tables', 'cmt_templates', 'text', 0, '', '', '', 'Angaben zu den Templates, die diese Tabelle / Anwendung verwendet.', 'Templates'),
(21, 'cmt_tables', 'cmt_itemvisible', 'flag', 0, NULL, '', '1', 'Zeigt an, ob die Tabelle / Anwendung in der Navigation angezeigt werden soll.', 'Sichtbarkeit: Eintrag'),
(22, 'cmt_tables', 'cmt_target', 'string', 0, NULL, '', '', 'Zielfenster f&uuml;r den Link in der Navigation (z.B. "mainframe", "_blank", "_top"). Wird hie rnichts eingetragen, wird automatisch ''cmt_applauncher'' verwendet.', 'Zielfenster'),
(23, 'cmt_tables', 'cmt_queryvars', 'text', 0, NULL, '', '', 'Optionale Variablen für den Querystring im Navigationslink.<br>Die Variablen müssen durch eine Zeilenschaltung voneinander getrennt werden.', 'Querystring-Variablen'),
(24, 'cmt_tables', 'cmt_charset', 'string', 0, '', '', '', 'Standard-Zeichensatz der Tabelle.', 'Zeichensatz'),
(25, 'cmt_tables', 'cmt_collation', 'string', 0, '', '', '', 'Interne Sortierreihenfolge der MySQL-Datenbank', 'Sortierreihenfolge'),
(26, 'cmt_tables', 'cmt_systemtable', 'flag', 0, NULL, NULL, NULL, 'Gibt an, ob die Tabelle eine Content-o-mat Systemtabelle ist.', 'Systemtabelle'),
(27, 'cmt_tables', 'cmt_tablesettings', 'text', 0, NULL, NULL, '', 'Einstellungen für die Tabelle/Applikation (werden serialisiert gespeichert)', 'Tabelleneinstellungen'),
(35, 'cmt_tables_groups', 'cmt_groupname', 'string', 0, '', '', 'Neue Gruppe', 'Name der Gruppe', 'Gruppenname'),
(36, 'cmt_tables_groups', 'cmt_grouppos', 'position', 0, '', '', '', 'Position der Gruppe', 'Gruppenposition'),
(37, 'cmt_tables_groups', 'cmt_visible', 'flag', 0, NULL, '', '1', 'Soll Gruppe in der Navigation angezeigt werden?', 'Gruppensichtbarkeit'),
(38, 'cmt_tables_groups', 'cmt_isimportgroup', 'flag', 0, NULL, NULL, '0', 'Markiert den Ordner, der für Tabellen-Importe genutzt werden soll.', 'Import-Ordner'),
(39, 'cmt_tables_groups', 'cmt_groupsettings', 'text', 0, NULL, NULL, '', 'Einstellungen für die Gruppe (werden serialisiert gespeichert)', 'Gruppeneinstellungen'),
(50, 'cmt_users_groups', 'cmt_restrictions', 'text', 0, NULL, '', '', 'Definiert Einschränkungen bei der Anzeige der Tabellendaten f&uuml;r diese Benutzergruppe.', 'Einschränkungen'),
(51, 'cmt_users_groups', 'cmt_addvars', 'text', 0, NULL, '', '', 'Einstellungen f&uuml;r die Anwendung / Tabelle', 'Einstellungen'),
(52, 'cmt_users_groups', 'cmt_showfields', 'text', 0, NULL, '', '', 'Angezeigte Felder in der Tabellenübersicht.', 'Übersicht: angezeigte Felder'),
(53, 'cmt_users_groups', 'cmt_editstruct', 'text', 0, NULL, '', '', 'Reihenfolge der Felder in der Detailansicht eines Eintrags.', 'Detailansicht: Struktur'),
(54, 'cmt_users_groups', 'cmt_grouptype', 'select', NULL, NULL, 'a:14:{s:11:"noselection";s:0:"";s:6:"values";s:11:"user\r\nadmin";s:7:"aliases";s:23:"Benutzer\r\nAdministrator";s:10:"from_table";s:0:"";s:22:"from_table_value_field";s:0:"";s:22:"from_table_alias_field";s:0:"";s:18:"from_table_add_sql";s:0:"";s:18:"multiple_separator";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:16:"recursive_parent";s:0:"";s:28:"recursive_parent_value_field";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', 'user', 'Definiert die grundlegende Art des Benutzers: Nur Benutzer, die die auch am Kern des Systems arbeiten müssen, sollten hier als Administratoren definiert werden.', 'Gruppenart'),
(55, 'cmt_users_groups', 'cmt_groupname', 'string', 0, NULL, '', '', 'Name der Benutzergruppe.', 'Benutzergruppenname'),
(56, 'cmt_users_groups', 'cmt_showitems', 'text', 0, NULL, '', '', 'IDs der Tabellen und Anwendungen, die angezeigt werden sollen.', 'angezeigte Elemente'),
(57, 'cmt_users_groups', 'cmt_startpage', 'string', 0, NULL, '', '', 'URL der HTML-Seite, die nach erfolgreichem Loggin geladen werden soll.', 'Startseite/ -frame'),
(58, 'cmt_users_groups', 'cmt_startapp', 'select', NULL, NULL, 'a:14:{s:11:"noselection";s:26:"-- Standard-Startscreen --";s:6:"values";s:0:"";s:7:"aliases";s:0:"";s:10:"from_table";s:10:"cmt_tables";s:22:"from_table_value_field";s:2:"id";s:22:"from_table_alias_field";s:18:"{VAR:cmt_showname}";s:18:"from_table_add_sql";s:21:"ORDER BY cmt_showname";s:18:"multiple_separator";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:16:"recursive_parent";s:0:"";s:28:"recursive_parent_value_field";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Applikation/ Tabelle, die nach erfolgreichem Loggin gestartet werden soll.', 'Start-Applikation'),
(59, 'cmt_users_groups', 'cmt_groupdirectory', 'link', 0, NULL, 'a:2:{s:7:"onlydir";s:1:"1";s:11:"noselection";s:22:"-- kein Verzeichnis --";}', '', '', 'Gruppenverzeichnis'),
(70, 'cmt_users', 'cmt_lastlogin', 'datetime', 0, NULL, '', '0000-00-00 00:00:00', 'Zeitpunkt des letzten Logins des Users', 'letzter Login'),
(71, 'cmt_users', 'cmt_usergroup', 'select', NULL, NULL, 'a:14:{s:11:"noselection";s:0:"";s:6:"values";s:0:"";s:7:"aliases";s:0:"";s:10:"from_table";s:16:"cmt_users_groups";s:22:"from_table_value_field";s:2:"id";s:22:"from_table_alias_field";s:19:"{VAR:cmt_groupname}";s:18:"from_table_add_sql";s:22:"ORDER BY cmt_groupname";s:18:"multiple_separator";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:16:"recursive_parent";s:0:"";s:28:"recursive_parent_value_field";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Gruppe, zu der dieser Benutzer gehört.', 'Benutzergruppe'),
(72, 'cmt_users', 'cmt_restrictions', 'text', 0, NULL, '', '', 'Weitere Einschränkungen f&uuml;r den Benutzer in der Tabellenansicht.', 'Einschränkungen'),
(73, 'cmt_users', 'cmt_addvars', 'text', 0, NULL, '', '', 'Tabelleneinstellungen.', 'Einstellungen'),
(74, 'cmt_users', 'cmt_showfields', 'text', 0, NULL, '', '', 'Angezeigte Felder in der Tabellenübersicht.', 'Übersicht: angezeigte Felder'),
(75, 'cmt_users', 'cmt_editstruct', 'text', 0, NULL, '', '', 'Struktur der Detailansicht.', 'Detailansicht: Struktur'),
(76, 'cmt_users', 'cmt_username', 'string', 0, '', '', '', 'Name des Benutzers (wird f&uuml;r die Anmeldung / den Login benutzt).', 'Benutzername'),
(77, 'cmt_users', 'cmt_pass', 'string', 0, '', '', '', 'Codiertes Passwort des Benutzers.', 'Passwort'),
(78, 'cmt_users', 'cmt_exptime', 'datetime', 0, '', '', '0000-00-00 00:00:00', 'Noch nicht implementiert: Verfallszeitpunkt des Passworts des Benutzers.', 'Verfallszeitpunkt'),
(79, 'cmt_users', 'cmt_creationdate', 'datetime', 0, '', 'a:1:{s:7:"current";s:1:"1";}', '0000-00-00 00:00:00', 'Zeitpunkt, zu dem der Benutzer erstellt wurde.', 'Erstellungszeit'),
(80, 'cmt_users', 'cmt_passchanged', 'datetime', 0, '', 'a:1:{s:7:"current";s:1:"1";}', '0000-00-00 00:00:00', 'Letzte Änderung des Benutzerpasswortes.', 'Passwort zuletzt geändert'),
(81, 'cmt_users', 'cmt_useralias', 'string', 0, NULL, '', '', 'Hier kann zus&auml;tzlich zum Login-Namen ein richtiger Name f&uuml;r den Benutzer angegeben werden.', 'Echter Name des Benutzers'),
(82, 'cmt_users', 'cmt_uservars', 'text', 0, '', '', '', 'Gespeicherte interne Variablen f&uuml;r den Benutzer.', 'Benutzervariablen'),
(83, 'cmt_users', 'cmt_showitems', 'text', 0, NULL, '', '', 'Tabellen / Applikationen, die für den User angezeigt werden.', 'Angezeigte Tabellen'),
(84, 'cmt_users', 'cmt_usertype', 'string', 0, NULL, '', '', 'Art des Benutzers (wird automatisch beim Speichern aus der zugehörigen Gruppe erzeugt).', 'Benutzerart'),
(85, 'cmt_users', 'cmt_userdirectory', 'string', 0, NULL, '', '', 'Persönliches Verzeichnis des Benutzers', 'persönliches Verzeichnis'),
(86, 'cmt_users', 'cmt_startpage', 'string', 0, NULL, '', '', 'URL der HTML-Seite, die nach erfolgreichem Loggin angezeigt werden soll.', 'Startseite/ -frame'),
(87, 'cmt_users', 'cmt_startapp', 'select', NULL, NULL, 'a:14:{s:11:"noselection";s:0:"";s:6:"values";s:0:"";s:7:"aliases";s:0:"";s:10:"from_table";s:10:"cmt_tables";s:22:"from_table_value_field";s:2:"id";s:22:"from_table_alias_field";s:18:"{VAR:cmt_showname}";s:18:"from_table_add_sql";s:21:"ORDER BY cmt_showname";s:18:"multiple_separator";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:16:"recursive_parent";s:0:"";s:28:"recursive_parent_value_field";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Applikation/ Tabelle, die nach erfolgreichem Loggin angezeigt werden soll.', 'Start-Applikation'),
(88, 'cmt_users', 'cmt_cmtstyle', 'link', 0, NULL, 'a:3:{s:4:"path";s:15:"admin/templates";s:7:"onlydir";s:1:"1";s:5:"depth";s:1:"1";}', 'admin/templates/default/', 'Ausgew&auml;hlter Stil f&uuml;r die Darstellung des Content-O-Maten.', 'Content-O-Mat Stil'),
(95, 'cmt_sessions', 'cmt_sessionid', 'string', 1, '', '', '', 'ID der Session (SID)', 'Session-ID'),
(96, 'cmt_sessions', 'cmt_exptime', 'integer', 0, '', '', '', 'Alter der Session als Unix-Timestamp', 'Timestamp'),
(97, 'cmt_sessions', 'cmt_vars', 'text', 0, '', '', '', 'Container für serialisierte Session Variablen', 'Session-Vars'),
(98, 'cmt_sessions', 'cmt_loggedin', 'flag', 0, '', '', '', 'Ist der Session-Besitzer eingeloggt?', 'eingeloggt'),
(99, 'cmt_sessions', 'cmt_userid', 'integer', 0, '', '', '', 'ID des eingeloggten Nutzers', 'Nutzer-ID'),
(110, 'cmt_content_de', 'cmt_pageid', 'select_recursive', NULL, NULL, 'a:6:{s:11:"noselection";s:0:"";s:10:"from_table";s:12:"cmt_pages_de";s:6:"parent";s:2:"id";s:18:"parent_value_field";s:12:"cmt_parentid";s:18:"parent_alias_field";s:9:"cmt_title";s:7:"add_sql";s:20:"ORDER BY cmt_pagepos";}', 'root', 'Objekt wird angezeigt auf dieser Seite.', 'Seite'),
(111, 'cmt_content_de', 'cmt_objecttemplate', 'select', NULL, NULL, 'a:14:{s:11:"noselection";s:0:"";s:6:"values";s:0:"";s:7:"aliases";s:0:"";s:10:"from_table";s:21:"cmt_templates_objects";s:22:"from_table_value_field";s:2:"id";s:22:"from_table_alias_field";s:14:"{VAR:cmt_name}";s:18:"from_table_add_sql";s:21:"ORDER BY cmt_position";s:18:"multiple_separator";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:16:"recursive_parent";s:0:"";s:28:"recursive_parent_value_field";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Verwendete Layoutvorlage für dieses Objekt.', 'Layoutvorlage'),
(112, 'cmt_content_de', 'cmt_objectgroup', 'integer', 0, '', '', '', 'Gruppe/ Spalte des Layout-Objektes', 'Objekt-Gruppe'),
(113, 'cmt_content_de', 'cmt_position', 'integer', 0, '', '', '1', 'Reihenfolge des Objektes innerhalb der Layoutposition', 'Reihenfolge'),
(114, 'cmt_content_de', 'cmt_visible', 'flag', 0, '', '', '1', '', 'Objekt sichtbar'),
(115, 'cmt_content_de', 'head1', 'string', NULL, NULL, '', '', '', 'Überschrift 1'),
(116, 'cmt_content_de', 'head2', 'string', NULL, NULL, '', '', '', 'Überschrift 2'),
(117, 'cmt_content_de', 'head3', 'string', NULL, NULL, '', '', '', 'Überschrift 3'),
(118, 'cmt_content_de', 'head4', 'string', NULL, NULL, '', '', '', 'Überschrift 4'),
(119, 'cmt_content_de', 'head5', 'string', NULL, NULL, '', '', '', 'Überschrift 5'),
(120, 'cmt_content_de', 'text1', 'text', 0, '', '', '', '', 'Text 1'),
(121, 'cmt_content_de', 'text2', 'text', 0, '', '', '', '', 'Text 2'),
(122, 'cmt_content_de', 'text3', 'text', 0, '', '', '', '', 'Text 3'),
(123, 'cmt_content_de', 'text4', 'text', 0, '', '', '', '', 'Text 4'),
(124, 'cmt_content_de', 'text5', 'text', 0, '', '', '', '', 'Text 5'),
(125, 'cmt_content_de', 'cmt_created', 'datetime', NULL, NULL, 'a:2:{s:7:"current";s:2:"on";s:13:"show_calendar";s:2:"on";}', '0000-00-00 00:00:00', '', 'Erstellt am'),
(126, 'cmt_content_de', 'cmt_createdby', 'system_var', 0, '', 'a:2:{s:4:"type";s:12:"CMT_USERNAME";s:4:"show";s:1:"1";}', '', '', 'Erstellt von'),
(127, 'cmt_content_de', 'cmt_lastmodified', 'datetime', NULL, NULL, 'a:1:{s:14:"always_current";s:2:"on";}', '0000-00-00 00:00:00', '', 'Zuletzt aktualisiert'),
(128, 'cmt_content_de', 'cmt_lastmodifiedby', 'system_var', 0, '', 'a:2:{s:4:"type";s:12:"CMT_USERNAME";s:4:"show";s:1:"1";}', '', '', 'Zuletzt aktualisiert von'),
(129, 'cmt_content_de', 'image1', 'string', 0, '', '', '', 'Erstes optionales Bild', 'Bild 1'),
(130, 'cmt_content_de', 'image2', 'string', 0, '', '', '', '2. optionales Bild', 'Bild 2'),
(131, 'cmt_content_de', 'image3', 'string', 0, '', '', '', '3. optionales Bild', 'Bild 3'),
(132, 'cmt_content_de', 'image4', 'string', 0, '', '', '', '4. optionales Bild', 'Bild 4'),
(133, 'cmt_content_de', 'image5', 'string', 0, '', '', '', '5. optionales Bild', 'Bild 5'),
(134, 'cmt_content_de', 'html1', 'text', 0, '', '', '', '', 'Html 1'),
(135, 'cmt_content_de', 'file1', 'string', 0, '', '', '', '', 'eingebunde Datei 1'),
(145, 'cmt_links_de', 'cmt_type', 'select', NULL, NULL, 'a:14:{s:11:"noselection";s:0:"";s:6:"values";s:44:"internal\r\nexternal\r\ndownload\r\nmailto\r\nscript";s:7:"aliases";s:78:"Website (intern)\r\nWWW (extern)\r\nDatei-Download\r\nE-Mail\r\nJavascript / Anweisung";s:10:"from_table";s:0:"";s:22:"from_table_value_field";s:0:"";s:22:"from_table_alias_field";s:0:"";s:18:"from_table_add_sql";s:0:"";s:18:"multiple_separator";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:16:"recursive_parent";s:0:"";s:28:"recursive_parent_value_field";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Link-Typ: zu interner Seite oder zu einem externen Ziel', 'Typ'),
(146, 'cmt_links_de', 'cmt_page', 'select_recursive', NULL, NULL, 'a:6:{s:11:"noselection";s:26:"--- kein interner Link ---";s:10:"from_table";s:12:"cmt_pages_de";s:6:"parent";s:2:"id";s:18:"parent_value_field";s:12:"cmt_parentid";s:18:"parent_alias_field";s:9:"cmt_title";s:7:"add_sql";s:20:"ORDER BY cmt_pagepos";}', '', 'Name der Seite des internen Links', 'interner Link: Websiteseite'),
(147, 'cmt_links_de', 'cmt_url', 'string', 0, '', '', 'http://', 'URL zum externen Ziel', 'externer Link: URL'),
(148, 'cmt_links_de', 'cmt_target', 'select', NULL, NULL, 'a:14:{s:11:"noselection";s:21:"-- Standardfenster --";s:6:"values";s:19:"_blank\r\n_self\r\n_top";s:7:"aliases";s:85:"neues Fenster (_blank)\r\neigenes Fenster/Frame (_self)\r\ngesamtes Browserfenster (_top)";s:10:"from_table";s:0:"";s:22:"from_table_value_field";s:0:"";s:22:"from_table_alias_field";s:0:"";s:18:"from_table_add_sql";s:0:"";s:18:"multiple_separator";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:16:"recursive_parent";s:0:"";s:28:"recursive_parent_value_field";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Zielfenster des externen Links', 'Zielfenster'),
(149, 'cmt_links_de', 'cmt_addhtml', 'string', NULL, NULL, '', '', 'Optional: Zusätzliche HTML-Angaben wie ''style="..."'', ''class="..."'' oder onClick="Javascript".', 'zusätzliches HTML'),
(150, 'cmt_links_de', 'cmt_linkonpage', 'string', 0, '', '', '', 'Link befindet sich auf Seite (ID)', 'Link auf Seite'),
(151, 'cmt_links_de', 'cmt_created', 'datetime', 0, '', 'a:1:{s:7:"current";s:1:"1";}', '0000-00-00 00:00:00', '', 'Erstellt am'),
(152, 'cmt_links_de', 'cmt_createdby', 'system_var', 0, '', 'a:1:{s:4:"type";s:10:"CMT_USERID";}', '', '', 'Erstellt von'),
(153, 'cmt_links_de', 'cmt_lastmodified', 'datetime', 0, '', 'a:1:{s:15:"allways_current";s:1:"1";}', '0000-00-00 00:00:00', '', 'Zuletzt aktualisiert'),
(154, 'cmt_links_de', 'cmt_lastmodifiedby', 'system_var', 0, '', 'a:1:{s:4:"type";s:10:"CMT_USERID";}', '', '', 'Zuletzt aktualisiert von'),
(155, 'cmt_links_de', 'cmt_lang', 'string', 0, '', '', '', 'Sprache der Seite zu welcher der Link gehört', 'Sprache'),
(156, 'cmt_links_de', 'cmt_linkid', 'integer', 0, NULL, NULL, NULL, '', 'Link-ID'),
(165, 'cmt_pages_de', 'cmt_title', 'string', 0, '', '', '', 'Titel der Seite', 'Seitentitel'),
(166, 'cmt_pages_de', 'cmt_parentid', 'select_recursive', NULL, NULL, 'a:6:{s:11:"noselection";s:0:"";s:10:"from_table";s:12:"cmt_pages_de";s:6:"parent";s:2:"id";s:18:"parent_value_field";s:12:"cmt_parentid";s:18:"parent_alias_field";s:9:"cmt_title";s:7:"add_sql";s:20:"ORDER BY cmt_pagepos";}', 'root', 'ID der Übergeordneten Seite', 'Übergeordnete Seite (id)'),
(167, 'cmt_pages_de', 'cmt_pagepos', 'position', 0, '', 'a:1:{s:6:"parent";s:12:"cmt_parentid";}', '', 'Position der Seite in der Verzeichnisstruktur', 'Seitenposition'),
(168, 'cmt_pages_de', 'cmt_template', 'select', NULL, NULL, 'a:14:{s:11:"noselection";s:0:"";s:6:"values";s:0:"";s:7:"aliases";s:0:"";s:10:"from_table";s:19:"cmt_templates_pages";s:22:"from_table_value_field";s:2:"id";s:22:"from_table_alias_field";s:14:"{VAR:cmt_name}";s:18:"from_table_add_sql";s:21:"ORDER BY cmt_position";s:18:"multiple_separator";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:16:"recursive_parent";s:0:"";s:28:"recursive_parent_value_field";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Seitenvorlage', 'Seitenvorlage'),
(169, 'cmt_pages_de', 'cmt_urlalias', 'string', NULL, NULL, '', '', 'Name der in der Seiten-URL angezeigt wird', 'Aliasname für URL'),
(170, 'cmt_pages_de', 'cmt_type', 'select', NULL, NULL, 'a:14:{s:11:"noselection";s:0:"";s:6:"values";s:18:"page\r\nfolder\r\nlink";s:7:"aliases";s:19:"Seite\r\nOrdner\r\nLink";s:10:"from_table";s:0:"";s:22:"from_table_value_field";s:0:"";s:22:"from_table_alias_field";s:0:"";s:18:"from_table_add_sql";s:0:"";s:18:"multiple_separator";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:16:"recursive_parent";s:0:"";s:28:"recursive_parent_value_field";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Art des Eintrags: Seite, Ordner oder Link (wird nur in der Übersicht angezeigt, nicht in der Navigation)', 'Typ'),
(257, 'cmt_pages_de', 'cmt_relations', 'relation', NULL, NULL, 'a:2:{s:10:"from_table";s:89:"a:1:{i:1;s:71:"a:3:{s:4:"name";s:0:"";s:11:"alias_field";s:0:"";s:7:"add_sql";s:0:"";}";}";s:18:"multiple_separator";s:1:",";}', '', 'Sofern eingerichtet, ist hier die Auswahl von Verknüpfungen (Relationen) zu anderen Datenbanktabelleneinträgen für diese Seite möglich.', 'Verknüpfungen'),
(172, 'cmt_pages_de', 'cmt_isroot', 'select', NULL, NULL, 'a:14:{s:11:"noselection";s:0:"";s:6:"values";s:4:"0\r\n1";s:7:"aliases";s:36:"--- nicht Startseite ---\r\nStartseite";s:10:"from_table";s:0:"";s:22:"from_table_value_field";s:0:"";s:22:"from_table_alias_field";s:0:"";s:18:"from_table_add_sql";s:0:"";s:18:"multiple_separator";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:16:"recursive_parent";s:0:"";s:28:"recursive_parent_value_field";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '0', 'Definiert die Startseite der gesamten Website', 'Website-Startseite'),
(173, 'cmt_pages_de', 'cmt_creationdate', 'datetime', 0, '', 'a:1:{s:7:"current";s:1:"1";}', '0000-00-00 00:00:00', 'Zeitpunkt der Seitenerstellung', 'Erstellungsdatum'),
(174, 'cmt_pages_de', 'cmt_createdby', 'system_var', 0, '', 'a:2:{s:4:"type";s:10:"CMT_USERID";s:4:"show";s:1:"1";}', '', 'Name des Users, der die Seite angelegt hat.', 'Angelegt durch'),
(175, 'cmt_pages_de', 'cmt_showinnav', 'select', NULL, NULL, 'a:11:{s:11:"noselection";s:0:"";s:18:"multiple_separator";s:0:"";s:6:"values";s:8:"0\r\n1\r\n99";s:7:"aliases";s:33:"nicht anzeigen\r\nanzeigen\r\nsperren";s:10:"from_table";s:0:"";s:22:"from_table_alias_field";s:0:"";s:18:"from_table_add_sql";s:0:"";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '1', '<p>Definiert, ob die Seite in der Navigation angezeigt werden soll. Ggf. (abhängig vom Seitentemplate) muss ein Ordner, in welchem sich eine Seite befindet, die nicht in der Navigation angezeigt werden soll, ebenfalls auf ''verbergen'' gestellt werden.</p>\r\n<p><b>Verhalten:</b><br />\r\n<i>nicht anzeigen</i>: Seite wird in der Navigation nicht angezeigt, ist aber mit der direkten URL aufrufbar.<br />\r\n<i>anzeigen</i>: Seite wird in der Navigation angezeigt (Standard)<br />\r\n<i>gesperrt</i>: Seite wird in der Navigation nicht angezeigt. Bei einem direkten Aufruf über die URL wird ein "404 - Datei nicht gefunden" an den Browser gesendet!</p>', 'in Navigation anzeigen'),
(176, 'cmt_pages_de', 'cmt_pageid', 'integer', 0, NULL, NULL, '', '', 'Seiten-ID'),
(177, 'cmt_pages_de', 'cmt_protected', 'flag', NULL, NULL, 'a:1:{s:5:"value";s:0:"";}', '', 'Zeigt an, ob die Seite / der Ordner passwortgeschützt sein soll. Ist dieses Feld angekreuzt, müssen auch die nachfolgenden EintrÃ¤ge ausgefüllt werden!', 'Seite geschützt'),
(178, 'cmt_pages_de', 'cmt_protected_var', 'string', 0, NULL, NULL, 'cmt_visitorloggedin', 'Name der Session-Variable, die gesetzt sein muss, damit der Benutzer auf dieser Seite eingeloggt ist.', 'Variable: Besucher eingeloggt'),
(179, 'cmt_pages_de', 'cmt_protected_loginpage', 'select_recursive', NULL, NULL, 'a:6:{s:11:"noselection";s:0:"";s:10:"from_table";s:12:"cmt_pages_de";s:6:"parent";s:2:"id";s:18:"parent_value_field";s:12:"cmt_parentid";s:18:"parent_alias_field";s:9:"cmt_title";s:7:"add_sql";s:20:"ORDER BY cmt_pagepos";}', '', 'Seite zu der umgeleitet wird, wenn der Besucher (noch) nicht eingeloggt ist. Hier sollte eine Seite ausgewählt werden, auf welcher sich ein Login-Formular befindet.', 'Login-Seite'),
(180, 'cmt_pages_de', 'cmt_link', 'string', NULL, NULL, '', '', 'URL des manuell eingefügten Links', 'Link-URL'),
(181, 'cmt_pages_de', 'cmt_link_target', 'string', NULL, NULL, '', '', 'Zielfenster des manuell eingefügten Links, z.B. _blank', 'Zielfenster'),
(190, 'cmt_templates_objects', 'cmt_name', 'string', 0, '', '', '', 'Name der Objekt-Layoutvorlage', 'Name'),
(191, 'cmt_templates_objects', 'cmt_source', 'text', 0, '', '', '', 'HTML-Quelltext', 'HTML-Quelltext'),
(192, 'cmt_templates_pages', 'cmt_position', 'position', NULL, NULL, 'a:1:{s:6:"parent";s:0:"";}', '', '', 'Reihenfolge'),
(200, 'cmt_templates_pages', 'cmt_name', 'string', 0, '', '', '', 'Name der Objekt-Layoutvorlage', 'Name'),
(201, 'cmt_templates_pages', 'cmt_source', 'text', 0, '', '', '', 'HTML-Quelltext', 'HTML-Quelltext'),
(202, 'cmt_templates_objects', 'cmt_position', 'position', NULL, NULL, 'a:1:{s:6:"parent";s:0:"";}', '', '', 'Reihenfolge'),
(210, 'cmt_export_saved', 'cmt_exportname', 'string', 0, '', '', '', '', 'Name'),
(211, 'cmt_export_saved', 'cmt_settings', 'text', 0, '', '', '', '', 'Einstellungen'),
(212, 'cmt_export_saved', 'cmt_savedby', 'system_var', 0, '', 'a:1:{s:4:"type";s:10:"CMT_USERID";}', '', '', 'gespeichert von'),
(213, 'cmt_export_saved', 'cmt_savedat', 'system_var', 0, '', 'a:1:{s:4:"type";s:13:"sys_timestamp";}', '', '', 'gespeichert am'),
(214, 'cmt_export_saved', 'cmt_type', 'string', 0, '', '', '', '', 'Exporttyp'),
(215, 'cmt_export_saved', 'cmt_description', 'text', 0, '', '', '', '', 'Beschreibung'),
(225, 'cmt_content_languages', 'cmt_languagename', 'string', NULL, NULL, '', '', 'Name/ Bezeichnung der Sprache dieser Website-Version, z.B. "deutsch", "english", etc.', 'Sprachenname'),
(226, 'cmt_content_languages', 'cmt_language', 'string', NULL, NULL, '', '', 'Sprachkürzel, welches intern für die Sprachversion verwendet werden soll.\r\nDie Abkürzung sollte so kurz wie möglich gewählt werden, z.B. <i>en</i>, <i>fr</i> oder <i>it</i>. Bitte verwenden Sie keine Sonder- oder Leerzeichen im Kürzel.', 'Sprachkürzel'),
(227, 'cmt_content_languages', 'cmt_charset', 'string', NULL, NULL, '', '', 'Wählen Sie hier den passenden Zeichensatz für die Website-Inhalte. Bitte beachten Sie, dass eine nachträgliche Änderung des Zeichensatzes zu fehlerhafter Darstellung der Websiteinhalte der gewählten Sprache führen kann.', 'Zeichensatz'),
(228, 'cmt_content_languages', 'cmt_addquery', 'string', NULL, NULL, '', '', 'Hier können eigene Auswahlkriterien für die MySQL-Query, welche die Seiten aufruft, angegeben werden.\r\n\r\n<i>AND myfield=''1''</i>\r\n\r\nz.B. würde nur die Seiten ausgeben, die die Eigenschaften ''anzeigen'' und ''myfield=1'' erfüllen.', 'zusatzliche MySQL-Kriterien'),
(229, 'cmt_content_languages', 'cmt_position', 'position', NULL, NULL, 'a:1:{s:6:"parent";s:0:"";}', '', 'Regelt die Position der Sprachversion auf der Übersichtsseite.', 'Position'),
(240, 'cmt_dberrorlog', 'error_datetime', 'datetime', 0, NULL, NULL, '0000-00-00 00:00:00', '', 'Fehlerzeitpunkt'),
(241, 'cmt_dberrorlog', 'mysql_error_number', 'integer', 0, NULL, NULL, '', '', 'MySQL-Fehlernummer'),
(242, 'cmt_dberrorlog', 'mysql_error_message', 'text', 0, NULL, NULL, '', '', 'Fehlermeldung'),
(243, 'cmt_dberrorlog', 'mysql_query', 'text', 0, NULL, NULL, '', '', 'Datenbank-Query'),
(244, 'cmt_dberrorlog', 'script_name', 'string', 0, NULL, NULL, '', '', 'Skriptname'),
(245, 'cmt_dberrorlog', 'cmt_pageid', 'integer', 0, NULL, NULL, '', '', 'Content-o-mat: Seiten-ID'),
(246, 'cmt_dberrorlog', 'cmt_pagelang', 'string', 0, NULL, NULL, '', '', 'Content-o-mat: Sprach-ID'),
(247, 'cmt_dberrorlog', 'cmt_applicationid', 'string', 0, NULL, NULL, '', '', 'Content-o-mat: Anwendungs-ID'),
(248, 'cmt_dberrorlog', 'script_querystring', 'text', 0, NULL, NULL, '', '', 'Querystring'),
(249, 'cmt_dberrorlog', 'cmt_userid', 'integer', 0, NULL, NULL, '', '', 'Content-o-mat: Benutzer-ID'),
(250, 'cmt_dberrorlog', 'referer_ip', 'string', 0, NULL, NULL, '', '', 'Referer IP'),
(251, 'cmt_domains', 'cmt_domain', 'string', NULL, NULL, '', '', 'Name der Domain oder Subdomain (www.mydomain.de oder dev.contentomat.de).', 'Domain'),
(252, 'cmt_domains', 'cmt_domain_description', 'text', NULL, NULL, '', '', 'Optionaler Hilfetext, bzw. Beschreibung', 'Beschreibung'),
(253, 'cmt_domains', 'cmt_domain_title', 'string', NULL, NULL, '', '', 'Titel der Domain für die Benennung im CMS.', 'Titel der Domain'),
(254, 'cmt_pages_de', 'cmt_domain_id', 'select', NULL, NULL, 'a:12:{s:11:"noselection";s:42:"--- Keine besondere Domain ausgewählt ---";s:18:"multiple_separator";s:8:"__scol__";s:6:"values";s:0:"";s:7:"aliases";s:0:"";s:10:"from_table";s:11:"cmt_domains";s:22:"from_table_value_field";s:2:"id";s:22:"from_table_alias_field";s:41:"{VAR:cmt_domain} ({VAR:cmt_domain_title})";s:18:"from_table_add_sql";s:19:"ORDER BY cmt_domain";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Wird diese Seite nicht über die hier definierte Domain aufgerufen, wird der User zur richtigen Domain weitergeleitet.\r\nWenn hier keine Domain(s) ausgewählt werden, wird die Standard-Domain der Sprachversion, bzw. die vom User angewählte Domain verwendet.', 'Domain der Seite'),
(255, 'cmt_content_languages', 'cmt_domain_id', 'select', NULL, NULL, 'a:12:{s:11:"noselection";s:29:"--- Keine Standard-Domain ---";s:18:"multiple_separator";s:8:"__scol__";s:6:"values";s:0:"";s:7:"aliases";s:0:"";s:10:"from_table";s:11:"cmt_domains";s:22:"from_table_value_field";s:2:"id";s:22:"from_table_alias_field";s:41:"{VAR:cmt_domain} ({VAR:cmt_domain_title})";s:18:"from_table_add_sql";s:19:"ORDER BY cmt_domain";s:21:"recursive_noselection";s:0:"";s:20:"recursive_from_table";s:0:"";s:28:"recursive_parent_alias_field";s:0:"";s:17:"recursive_add_sql";s:0:"";}', '', 'Optionale Domain für diese Sprachversion der Website, z.B. wenn verschiedene Sprachversionen unter einer jeweils eigenen Domain laufen sollen (z.B. www.contentomat.de, www.contentomat.fr)', 'Standardomain der Sprachversion');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_links_de`
-- 

DROP TABLE IF EXISTS `cmt_links_de`;
CREATE TABLE `cmt_links_de` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_linkid` int(11) default NULL,
  `cmt_type` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_page` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_url` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_target` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_addhtml` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_linkonpage` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_created` datetime default NULL,
  `cmt_createdby` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_lastmodified` datetime default NULL,
  `cmt_lastmodifiedby` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_lang` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `cmt_links_de`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_pages_de`
-- 

DROP TABLE IF EXISTS `cmt_pages_de`;
CREATE TABLE `cmt_pages_de` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_title` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_parentid` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_pagepos` int(11) NOT NULL default '0',
  `cmt_template` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_urlalias` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_type` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_isroot` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_creationdate` datetime default NULL,
  `cmt_createdby` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_showinnav` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_pageid` int(11) default NULL,
  `cmt_protected` tinyint(4) default NULL,
  `cmt_protected_var` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_protected_loginpage` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_link` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_link_target` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_domain_id` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_relations` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- 
-- Daten für Tabelle `cmt_pages_de`
-- 

INSERT INTO `cmt_pages_de` (`id`, `cmt_title`, `cmt_parentid`, `cmt_pagepos`, `cmt_template`, `cmt_urlalias`, `cmt_type`, `cmt_isroot`, `cmt_creationdate`, `cmt_createdby`, `cmt_showinnav`, `cmt_pageid`, `cmt_protected`, `cmt_protected_var`, `cmt_protected_loginpage`, `cmt_link`, `cmt_link_target`, `cmt_domain_id`, `cmt_relations`) VALUES (1, 'WEBSITE', 'root', 1, '1', '', 'folder', '1', '2009-04-06 12:00:00', '1', '1', 1, 0, '', 'root', '', '', NULL, NULL),
(2, 'TESTAREA', 'root', 2, '1', '', 'folder', '0', '2009-04-06 12:00:00', '1', '1', 0, 0, '', 'root', '', '', '', NULL),
(3, 'Startseite', '1', 1, '1', '', 'page', '0', '2009-04-06 20:08:38', '1', '1', 0, 0, 'cmt_visitorloggedin', 'root', '', '', '', '');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_session_details`
-- 

DROP TABLE IF EXISTS `cmt_session_details`;
CREATE TABLE `cmt_session_details` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_sessionid` varchar(32) collate utf8_unicode_ci default NULL,
  `cmt_starttime` int(11) default NULL,
  `cmt_loggedin` tinyint(1) default '0',
  `cmt_userid` int(11) default NULL,
  `cmt_useragent` varchar(128) collate utf8_unicode_ci default NULL,
  `cmt_referer` varchar(128) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `cmt_session_details`
-- 



-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_sessions`
-- 

DROP TABLE IF EXISTS `cmt_sessions`;
CREATE TABLE `cmt_sessions` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_sessionid` varchar(32) collate utf8_unicode_ci default NULL,
  `cmt_exptime` int(11) default NULL,
  `cmt_vars` mediumtext collate utf8_unicode_ci,
  `cmt_loggedin` tinyint(1) default '0',
  `cmt_userid` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cmt_sessionid` (`cmt_sessionid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `cmt_sessions`
-- 



-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_sessions_raw`
-- 

DROP TABLE IF EXISTS `cmt_sessions_raw`;
CREATE TABLE `cmt_sessions_raw` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_sessionid` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `cmt_time` int(11) default NULL,
  `cmt_page` varchar(128) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `cmt_sessions_raw`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_tables`
-- 

DROP TABLE IF EXISTS `cmt_tables`;
CREATE TABLE `cmt_tables` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_tablename` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_showname` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_charset` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `cmt_collation` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_include` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_itempos` int(11) default NULL,
  `cmt_addvars` text collate utf8_unicode_ci,
  `cmt_showfields` text collate utf8_unicode_ci,
  `cmt_editstruct` text collate utf8_unicode_ci,
  `cmt_group` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_ownservice` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_type` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_templates` text collate utf8_unicode_ci,
  `cmt_itemvisible` tinyint(4) default NULL,
  `cmt_target` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_queryvars` text collate utf8_unicode_ci,
  `cmt_systemtable` tinyint(4) default NULL,
  `cmt_tablesettings` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

-- 
-- Daten für Tabelle `cmt_tables`
-- 

INSERT INTO `cmt_tables` (`id`, `cmt_tablename`, `cmt_showname`, `cmt_charset`, `cmt_collation`, `cmt_include`, `cmt_itempos`, `cmt_addvars`, `cmt_showfields`, `cmt_editstruct`, `cmt_group`, `cmt_ownservice`, `cmt_type`, `cmt_templates`, `cmt_itemvisible`, `cmt_target`, `cmt_queryvars`, `cmt_systemtable`, `cmt_tablesettings`) VALUES (1, '', 'Datei-Manager', '', NULL, 'app_filebrowser.php', 1, 'uploads = 6\r\nroot = \r\nshow_all_subfolders =', '', '', '1', '', 'application', '', 1, NULL, NULL, NULL, 'a:3:{s:4:"icon";s:7:"default";s:4:"root";s:4:"img/";s:7:"uploads";s:1:"4";}'),
(2, '', 'Tabellen-Manager', '', '', 'app_tablebrowser.php', 3, '', '', 'cmt_groupname\r\ncmt_grouppos\r\ncmt_visible\r\n{DONTSHOW}id', '1', '', 'application', '', 1, '', '', 0, 'a:1:{s:4:"icon";s:24:"cmt_defaulttableicon.png";}'),
(3, '', 'Kabasound Import-Export', '', NULL, 'app_kabasound.php', 6, 'import_directory  = import_export/', '', '', '1', '', 'application', '', 1, NULL, NULL, NULL, 'a:2:{s:4:"icon";s:7:"default";s:16:"import_directory";s:20:"admin/import_export/";}'),
(4, 'cmt_execute_code', 'Code-Manager', 'utf8', 'utf8_unicode_ci', '', 4, 'sort_fields = 0\nsort_dir = 2\nsearch_fields = 0\nshow_ipp = 1\nshow_iteminfos = \nshow_pageselect = 1\nadd_item = 1\nenter_query = 0\ncmt_showname = Code-Manager\nsort_directions = 0\nsort_aliases = \nsearch_aliases = \ntable_alias = Code Manager\nshow_ippnumber = 10\nshow_query = \ncmt_ownservice = \ntable_icon = ', 'cmt_tablename\r\ncmt_executiontime\r\ncmt_code\r\ncmt_description', 'cmt_tablename\r\ncmt_executiontime\r\n{HEAD}PHP-Code\r\ncmt_code\r\n{HEAD}Code-Erklä½rung / -Beschreibung (optional)\r\ncmt_description\r\ncmt_isinternal\r\n{DONTSHOW}id', '1', 'includes/tabinc_execute_code.inc', 'table', 'a:4:{s:18:"dont_use_templates";s:0:"";s:14:"overview_frame";s:0:"";s:12:"overview_row";s:0:"";s:10:"edit_entry";s:0:"";}', 1, '', '', 0, 'a:9:{s:4:"icon";s:7:"default";s:11:"sort_fields";s:1:"0";s:15:"sort_directions";s:1:"0";s:13:"search_fields";s:1:"0";s:8:"show_ipp";s:1:"1";s:14:"show_ippnumber";s:2:"10";s:15:"show_pageselect";s:1:"1";s:14:"cmt_ownservice";s:48:"../admin/serviceincludes/tabinc_execute_code.inc";s:8:"add_item";s:1:"1";}'),
(5, 'cmt_users_groups', 'Benutzergruppen', 'utf8', 'utf8_unicode_ci', '', 2, 'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\n', 'cmt_groupname', '{DONTSHOW}id\r\n{HEAD}Gruppenname und -typ\r\ncmt_groupname\r\ncmt_grouptype\r\n{LAYER:0}Rechte und Einstellungen\r\ncmt_showitems\r\ncmt_restrictions\r\ncmt_showfields\r\ncmt_editstruct\r\ncmt_addvars\r\n{ENDLAYER}', '15', NULL, 'table', NULL, 0, '', '', 0, 'a:12:{s:4:"icon";s:24:"cmt_defaulttableicon.png";s:11:"sort_fields";s:1:"2";s:12:"sort_aliases";s:1:"1";s:15:"sort_directions";s:1:"2";s:13:"search_fields";s:1:"2";s:14:"search_aliases";s:1:"1";s:8:"show_ipp";s:1:"1";s:14:"show_ippnumber";s:2:"10";s:15:"show_pageselect";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:14:"cmt_ownservice";s:0:"";s:8:"add_item";s:1:"1";}'),
(6, 'cmt_users', 'Benutzer', 'utf8', 'utf8_unicode_ci', '', 3, 'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = ', 'id\r\ncmt_username\r\ncmt_usergroup\r\ncmt_useralias', '{DONTSHOW}id\r\n{HEAD}Benutzerdaten\r\ncmt_username\r\ncmt_pass\r\ncmt_usergroup\r\ncmt_useralias\r\n{NOEDIT}cmt_usertype\r\n{DONTSHOW}cmt_exptime\r\n{LAYER:0}Datums-Informationen\r\ncmt_lastlogin\r\ncmt_passchanged\r\ncmt_creationdate\r\n{ENDLAYER}\r\n{LAYER:0}Rechte\r\ncmt_showitems\r\ncmt_restrictions\r\ncmt_addvars\r\ncmt_showfield\r\ncmt_editstruct\r\n{ENDLAYER}\r\n{LAYER:0}Variablen\r\ncmt_uservars\r\n{ENDLAYER}\r\n{OWNHIDDEN:old_pass}\r\n{DONTSHOW}cmt_loggedin', '15', '', 'table', '', 0, '', '', 0, 'a:23:{s:12:"table_select";s:1:"1";s:11:"sort_fields";s:1:"2";s:8:"sort_dir";s:1:"2";s:14:"sort_direction";s:1:"2";s:13:"search_fields";s:1:"2";s:8:"show_ipp";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:8:"add_item";s:1:"1";s:11:"enter_query";s:1:"0";s:15:"show_pageselect";s:1:"1";s:7:"uploads";s:1:"6";s:4:"root";s:0:"";s:19:"show_all_subfolders";s:0:"";s:12:"cmt_showname";s:12:"Code-Manager";s:16:"import_directory";s:14:"import_export/";s:15:"sort_directions";s:1:"2";s:12:"sort_aliases";s:1:"1";s:14:"search_aliases";s:1:"1";s:11:"table_alias";s:0:"";s:14:"show_ippnumber";s:2:"10";s:10:"show_query";s:0:"";s:14:"cmt_ownservice";s:35:"includes/cmtinc_tables_settings.inc";s:10:"table_icon";s:0:"";}'),
(7, '', 'Benutzerverwaltung', '', NULL, 'app_rightsmanager.php', 1, 'sort_fields = 2\r\nsort_directions = 2\r\nsearch_fields = 2\r\nshow_ipp = 1\r\nshow_iteminfos = 1\r\nadd_item = 1\r\nsort_aliases = 1\r\nshow_pageselect = 1\r\nsearch_aliases = 1\r\ntable_alias = \r\nshow_ippnumber = 10\r\nshow_query = 0', '', '', '15', '', 'application', '', 1, NULL, NULL, NULL, 'a:23:{s:12:"table_select";s:1:"1";s:11:"sort_fields";s:1:"2";s:8:"sort_dir";s:1:"2";s:14:"sort_direction";s:1:"2";s:13:"search_fields";s:1:"2";s:8:"show_ipp";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:8:"add_item";s:1:"1";s:11:"enter_query";s:1:"0";s:15:"show_pageselect";s:1:"1";s:7:"uploads";s:1:"6";s:4:"root";s:0:"";s:19:"show_all_subfolders";s:0:"";s:12:"cmt_showname";s:12:"Code-Manager";s:16:"import_directory";s:14:"import_export/";s:15:"sort_directions";s:1:"2";s:12:"sort_aliases";s:1:"1";s:14:"search_aliases";s:1:"1";s:11:"table_alias";s:0:"";s:14:"show_ippnumber";s:2:"10";s:10:"show_query";s:1:"0";s:14:"cmt_ownservice";s:35:"includes/cmtinc_tables_settings.inc";s:10:"table_icon";s:0:"";}'),
(8, '', 'Website-Struktur', '', '', 'app_pages.php', 2, 'sort_fields = 2\r\nsort_directions = 2\r\nsearch_fields = 2\r\nshow_ipp = 1\r\nshow_iteminfos = 1\r\nadd_item = 1\r\nsort_aliases = 1\r\nshow_pageselect = 1\r\nsearch_aliases = 1\r\ntable_alias = \r\nshow_ippnumber = 10\r\nshow_query = 0', '', '', '16', '', 'application', '', 1, '', '', 0, 'a:23:{s:12:"table_select";s:1:"1";s:11:"sort_fields";s:1:"2";s:8:"sort_dir";s:1:"2";s:14:"sort_direction";s:1:"2";s:13:"search_fields";s:1:"2";s:8:"show_ipp";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:8:"add_item";s:1:"1";s:11:"enter_query";s:1:"0";s:15:"show_pageselect";s:1:"1";s:7:"uploads";s:1:"6";s:4:"root";s:0:"";s:19:"show_all_subfolders";s:0:"";s:12:"cmt_showname";s:12:"Code-Manager";s:16:"import_directory";s:14:"import_export/";s:15:"sort_directions";s:1:"2";s:12:"sort_aliases";s:1:"1";s:14:"search_aliases";s:1:"1";s:11:"table_alias";s:0:"";s:14:"show_ippnumber";s:2:"10";s:10:"show_query";s:1:"0";s:14:"cmt_ownservice";s:35:"includes/cmtinc_tables_settings.inc";s:10:"table_icon";s:0:"";}'),
(9, '', 'Layout', '', NULL, 'app_layout.php', 3, 'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\ncmt_ownservice = \ntable_icon = \n', '', '', '16', '', 'application', 'a:4:{s:18:"dont_use_templates";s:1:"1";s:14:"overview_frame";s:0:"";s:12:"overview_row";s:0:"";s:9:"editentry";s:0:"";}', 0, '_top', '', NULL, 'a:23:{s:12:"table_select";s:1:"1";s:11:"sort_fields";s:1:"2";s:8:"sort_dir";s:1:"2";s:14:"sort_direction";s:1:"2";s:13:"search_fields";s:1:"2";s:8:"show_ipp";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:8:"add_item";s:1:"1";s:11:"enter_query";s:1:"0";s:15:"show_pageselect";s:1:"1";s:7:"uploads";s:1:"6";s:4:"root";s:0:"";s:19:"show_all_subfolders";s:0:"";s:12:"cmt_showname";s:12:"Code-Manager";s:16:"import_directory";s:14:"import_export/";s:15:"sort_directions";s:1:"2";s:12:"sort_aliases";s:1:"1";s:14:"search_aliases";s:1:"1";s:11:"table_alias";s:0:"";s:14:"show_ippnumber";s:2:"10";s:10:"show_query";s:1:"0";s:14:"cmt_ownservice";s:0:"";s:10:"table_icon";s:0:"";}'),
(10, 'cmt_export_saved', 'gespeicherte Exporte', 'utf8', 'utf8_unicode_ci', '', 7, 'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = Gepeicherte Exporte\nshow_ippnumber = 10\nshow_query = \ncmt_ownservice = \ntable_icon = ', '', '', '1', '', 'table', '', 0, '', '', 0, 'a:23:{s:12:"table_select";s:1:"1";s:11:"sort_fields";s:1:"2";s:8:"sort_dir";s:1:"2";s:14:"sort_direction";s:1:"2";s:13:"search_fields";s:1:"2";s:8:"show_ipp";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:8:"add_item";s:1:"1";s:11:"enter_query";s:1:"0";s:15:"show_pageselect";s:1:"1";s:7:"uploads";s:1:"6";s:4:"root";s:0:"";s:19:"show_all_subfolders";s:0:"";s:12:"cmt_showname";s:12:"Code-Manager";s:16:"import_directory";s:14:"import_export/";s:15:"sort_directions";s:1:"2";s:12:"sort_aliases";s:1:"1";s:14:"search_aliases";s:1:"1";s:11:"table_alias";s:19:"Gepeicherte Exporte";s:14:"show_ippnumber";s:2:"10";s:10:"show_query";s:0:"";s:14:"cmt_ownservice";s:0:"";s:10:"table_icon";s:0:"";}'),
(11, 'cmt_content_de', 'Content', 'utf8', 'utf8_unicode_ci', '', 5, 'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = \ncmt_ownservice = \ntable_icon = ', '', '', '16', '', 'table', '', 1, '', '', 0, 'a:12:{s:10:"table_icon";s:7:"default";s:11:"sort_fields";s:1:"2";s:12:"sort_aliases";s:1:"1";s:15:"sort_directions";s:1:"2";s:13:"search_fields";s:1:"2";s:14:"search_aliases";s:1:"1";s:8:"show_ipp";s:1:"1";s:14:"show_ippnumber";s:2:"10";s:15:"show_pageselect";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:14:"cmt_ownservice";s:0:"";s:10:"show_query";s:1:"1";}'),
(12, 'cmt_links_de', 'Hyperlinks', 'utf8', 'utf8_unicode_ci', '', 4, 'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\n', '', '', '16', '', 'table', '', 0, '', '', 0, 'a:12:{s:4:"icon";s:4:"none";s:11:"sort_fields";s:1:"2";s:12:"sort_aliases";s:1:"1";s:15:"sort_directions";s:1:"2";s:13:"search_fields";s:1:"2";s:14:"search_aliases";s:1:"1";s:8:"show_ipp";s:1:"1";s:14:"show_ippnumber";s:2:"10";s:15:"show_pageselect";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:14:"cmt_ownservice";s:0:"";s:8:"add_item";s:1:"1";}'),
(13, 'cmt_pages_de', 'Website Seiten', 'utf8', 'utf8_unicode_ci', '', 7, 'sort_fields = 2\r\nsort_directions = 2\r\nsearch_fields = 2\r\nshow_ipp = 1\r\nshow_iteminfos = 1\r\nadd_item = 1\r\nsort_aliases = 1\r\nshow_pageselect = 1\r\nsearch_aliases = 1\r\ntable_alias = \r\nshow_ippnumber = 10\r\nshow_query = 0', '', '{INCLUDE:"applications/appinc_pages_duplicate_select.inc"}\r\n{HEAD}Seiteneigenschaften\r\ncmt_title\r\ncmt_showinnav\r\n{HEAD}Weitere Seiteneigenschaften\r\ncmt_parentid\r\ncmt_type\r\ncmt_template\r\n{SERVICE}\r\n{LAYER:0}Externer Link\r\ncmt_link\r\ncmt_link_target\r\n{ENDLAYER}\r\n{LAYER:0}Passwortschutz\r\n{INCLUDE:"applications/appinc_pages_inherit_protection.inc"}\r\ncmt_protected\r\ncmt_protected_loginpage\r\ncmt_protected_var\r\n{ENDLAYER}\r\n{LAYER:0}Optionale Angaben\r\ncmt_domain_id\r\ncmt_urlalias\r\ncmt_relations\r\n{ENDLAYER}\r\n{LAYER:0}Details\r\ncmt_isroot\r\ncmt_creationdate\r\ncmt_createdby\r\ncmt_pagepos\r\n{ENDLAYER}\r\n{DONTSHOW}cmt_lang\r\n{DONTSHOW}id\r\n{DONTSHOW}cmt_pageid', '16', '', 'table', '', 0, '', '', 0, 'a:23:{s:12:"table_select";s:1:"1";s:11:"sort_fields";s:1:"2";s:8:"sort_dir";s:1:"2";s:14:"sort_direction";s:1:"2";s:13:"search_fields";s:1:"2";s:8:"show_ipp";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:8:"add_item";s:1:"1";s:11:"enter_query";s:1:"0";s:15:"show_pageselect";s:1:"1";s:7:"uploads";s:1:"6";s:4:"root";s:0:"";s:19:"show_all_subfolders";s:0:"";s:12:"cmt_showname";s:12:"Code-Manager";s:16:"import_directory";s:14:"import_export/";s:15:"sort_directions";s:1:"2";s:12:"sort_aliases";s:1:"1";s:14:"search_aliases";s:1:"1";s:11:"table_alias";s:0:"";s:14:"show_ippnumber";s:2:"10";s:10:"show_query";s:1:"0";s:14:"cmt_ownservice";s:0:"";s:10:"table_icon";s:0:"";}'),
(14, 'cmt_templates_objects', 'Objekt-Vorlagen', 'utf8', 'utf8_unicode_ci', '', 1, 'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\n', 'cmt_name\r\ncmt_position', 'cmt_name\r\n{FORMAT:rows=20, width=90%, class=codebox}cmt_source\r\ncmt_position\r\n{DONTSHOW}id', '17', '', 'table', '', 1, '', '', 0, 'a:12:{s:4:"icon";s:24:"cmt_defaulttableicon.png";s:11:"sort_fields";s:1:"2";s:12:"sort_aliases";s:1:"1";s:15:"sort_directions";s:1:"2";s:13:"search_fields";s:1:"2";s:14:"search_aliases";s:1:"1";s:8:"show_ipp";s:1:"1";s:14:"show_ippnumber";s:2:"10";s:15:"show_pageselect";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:14:"cmt_ownservice";s:0:"";s:8:"add_item";s:1:"1";}'),
(15, 'cmt_templates_pages', 'Seiten-Vorlagen', 'utf8', 'utf8_unicode_ci', '', 2, 'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\n', 'cmt_name\r\ncmt_position', 'cmt_name\r\n{FORMAT:width=100%}cmt_source\r\ncmt_position\r\n{DONTSHOW}id', '17', '', 'table', '', 1, '', '', 0, 'a:23:{s:12:"table_select";s:1:"1";s:11:"sort_fields";s:1:"2";s:8:"sort_dir";s:1:"2";s:14:"sort_direction";s:1:"2";s:13:"search_fields";s:1:"2";s:8:"show_ipp";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:8:"add_item";s:1:"1";s:11:"enter_query";s:1:"0";s:15:"show_pageselect";s:1:"1";s:7:"uploads";s:1:"6";s:4:"root";s:0:"";s:19:"show_all_subfolders";s:0:"";s:12:"cmt_showname";s:12:"Code-Manager";s:16:"import_directory";s:14:"import_export/";s:15:"sort_directions";s:1:"2";s:12:"sort_aliases";s:1:"1";s:14:"search_aliases";s:1:"1";s:11:"table_alias";s:0:"";s:14:"show_ippnumber";s:2:"10";s:10:"show_query";s:1:"0";s:14:"cmt_ownservice";s:0:"";s:10:"table_icon";s:0:"";}'),
(16, 'cmt_content_languages', 'Website Sprachversionen', 'utf8', 'utf8_unicode_ci', '', 6, 'sort_fields = 2\r\nsort_directions = 2\r\nsearch_fields = 2\r\nshow_ipp = 1\r\nshow_iteminfos = 1\r\nadd_item = 1\r\nsort_aliases = 1\r\nshow_pageselect = 1\r\nsearch_aliases = 1\r\ntable_alias = \r\nshow_ippnumber = 10\r\nshow_query = 0\r\ncmt_ownservice = \r\ntable_icon =', 'cmt_languagename\r\ncmt_language\r\ncmt_domain_id\r\ncmt_charset\r\ncmt_position', '{DONTSHOW}id\r\n{HEAD}Sprachangaben\r\ncmt_languagename\r\ncmt_language\r\n{HEAD}Detailangaben\r\ncmt_charset\r\ncmt_addquery\r\ncmt_position', '16', '', 'table', '', 0, '', '', 0, 'a:13:{s:10:"table_icon";s:7:"default";s:11:"sort_fields";s:1:"2";s:12:"sort_aliases";s:1:"1";s:15:"sort_directions";s:1:"2";s:13:"search_fields";s:1:"2";s:14:"search_aliases";s:1:"1";s:8:"show_ipp";s:1:"1";s:14:"show_ippnumber";s:2:"10";s:15:"show_pageselect";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:14:"cmt_ownservice";s:0:"";s:8:"add_item";s:1:"1";s:10:"show_query";s:1:"1";}'),
(17, NULL, 'Startseite', '', NULL, 'app_welcome.php', 1, NULL, NULL, NULL, '16', NULL, 'application', NULL, 1, '', '', 0, NULL),
(18, 'cmt_dberrorlog', 'DB-Errorlog', 'utf8', 'utf8_unicode_ci', NULL, 5, NULL, 'error_datetime\r\nmysql_error_message\r\nmysql_query\r\nscript_querystring\r\nreferer_ip', '{DONTSHOW}id\r\n{HEAD}Fehler\r\nerror_datetime\r\nmysql_error_number\r\nmysql_error_message\r\nmysql_query\r\n{HEAD}Content-o-mat Daten\r\nscript_name\r\ncmt_pageid\r\ncmt_pagelang\r\ncmt_applicationid\r\nscript_querystring\r\n{HEAD}Verursacher\r\ncmt_userid\r\nreferer_ip', '1', NULL, 'table', NULL, 1, '', '', 0, 'a:19:{s:11:"sort_fields";s:1:"2";s:15:"sort_directions";s:1:"2";s:12:"sort_aliases";s:1:"1";s:13:"search_fields";s:1:"2";s:14:"search_aliases";s:1:"1";s:8:"show_ipp";s:1:"1";s:14:"show_ippnumber";s:2:"10";s:15:"show_pageselect";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:8:"add_item";s:1:"1";s:11:"enter_query";s:1:"0";s:10:"show_query";s:1:"0";s:14:"cmt_ownservice";s:0:"";s:4:"icon";s:24:"cmt_defaulttableicon.png";s:18:"external_templates";s:25:"admin/external_templates/";s:11:"big_buttons";s:0:"";s:9:"hover_row";s:1:"1";s:9:"max_chars";s:3:"200";s:18:"max_chars_appendix";s:3:"...";}'),
(19, 'cmt_domains', 'Website Domains', 'utf8', 'utf8_unicode_ci', NULL, 8, NULL, NULL, NULL, '16', NULL, 'table', NULL, 1, '', '', 1, 'a:19:{s:11:"sort_fields";s:1:"2";s:15:"sort_directions";s:1:"2";s:12:"sort_aliases";s:1:"1";s:13:"search_fields";s:1:"2";s:14:"search_aliases";s:1:"1";s:8:"show_ipp";s:1:"1";s:14:"show_ippnumber";s:2:"10";s:15:"show_pageselect";s:1:"1";s:14:"show_iteminfos";s:1:"1";s:8:"add_item";s:1:"1";s:11:"enter_query";s:1:"0";s:10:"show_query";s:1:"0";s:14:"cmt_ownservice";s:0:"";s:4:"icon";s:24:"cmt_defaulttableicon.png";s:18:"external_templates";s:25:"admin/external_templates/";s:11:"big_buttons";s:0:"";s:9:"hover_row";s:1:"1";s:9:"max_chars";s:3:"200";s:18:"max_chars_appendix";s:3:"...";}');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_tables_groups`
-- 

DROP TABLE IF EXISTS `cmt_tables_groups`;
CREATE TABLE `cmt_tables_groups` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_groupname` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_grouppos` int(11) default NULL,
  `cmt_visible` tinyint(4) default NULL,
  `cmt_isimportgroup` tinyint(4) default NULL,
  `cmt_groupsettings` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

-- 
-- Daten für Tabelle `cmt_tables_groups`
-- 

INSERT INTO `cmt_tables_groups` (`id`, `cmt_groupname`, `cmt_grouppos`, `cmt_visible`, `cmt_isimportgroup`, `cmt_groupsettings`) VALUES (1, 'Administration', 2, 1, 0, 'a:2:{s:4:"icon";s:9:"otherIcon";s:8:"iconPath";s:24:"altimg/groups/1/icon.png";}'),
(15, 'Benutzer', 3, 1, 0, 'a:2:{s:4:"icon";s:9:"otherIcon";s:8:"iconPath";s:25:"altimg/groups/15/icon.png";}'),
(16, 'Website', 1, 1, 0, 'a:2:{s:4:"icon";s:9:"otherIcon";s:8:"iconPath";s:25:"altimg/groups/16/icon.png";}'),
(17, 'Templates', 5, 1, 0, 'a:2:{s:4:"icon";s:9:"otherIcon";s:8:"iconPath";s:25:"altimg/groups/17/icon.png";}'),
(19, 'Import-Ordner', 7, 0, 1, 'a:2:{s:4:"icon";s:9:"otherIcon";s:8:"iconPath";s:37:"../admin/altimages/groups/19/icon.png";}');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_templates_objects`
-- 

DROP TABLE IF EXISTS `cmt_templates_objects`;
CREATE TABLE `cmt_templates_objects` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_name` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_source` text collate utf8_unicode_ci,
  `cmt_position` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

-- 
-- Daten für Tabelle `cmt_templates_objects`
-- 

INSERT INTO `cmt_templates_objects` (`id`, `cmt_name`, `cmt_source`, `cmt_position`) VALUES (2, 'Überschrift 1. Grades', '<h1>{HEAD:1}</h1>', 1),
(3, 'Bild', '<div class=\\"image_container\\">{IMAGE:1:img}</div>\r\n{IF ({LAYOUTMODE} == true || {ISSET:head1:CONTENT})}<div class=\\"imageCaption\\">{HEAD:1}</div>{ENDIF}', 5),
(5, 'HTML', '{HTML:1:img}', 7),
(8, 'Text', '<div class=\\"text\\">{TEXT:1:all}</div>', 4),
(12, 'PHP-Skript', '{SCRIPT:1:phpincludes}\r\n{IF ({LAYOUTMODE})}\r\n<div class=\\"cmtContentFieldHead\\">Variable 1 (opt.)</div><div>{HEAD:1}</div>\r\n<div class=\\"cmtContentFieldHead\\">Variable 2 (opt.)</div><div>{HEAD:2}</div>\r\n<div class=\\"cmtContentFieldHead\\">Variable 3 (opt.)</div><div>{HEAD:3}</div>\r\n{ENDIF}', 6),
(14, 'Überschrift 2. Grades', '<h2>{HEAD:1}</h2>', 2),
(15, 'Überschrift 3. Grades', '<h3>{HEAD:1}</h3>', 3),
(16, 'Link (automatisch): zurück zur vorherigen Seite', '<div class="linkBack"><img src="{CONSTANT:PATHTOWEBROOT}img/link_back.gif">\r\n{IF ({LAYOUTMODE})}zu verlinkender Text:<br><span>{HEAD:1}</span>\r\n{ELSE}<a href="{PAGEURL:{PAGEID}:{PAGELANG}}">{HEAD:1}</a>{ENDIF}</div>', 8),
(20, 'Textanker', '{IF ({LAYOUTMODE})}<img src=\\"{CONSTANT:PATHTOADMIN}{CONSTANT:CMT_TEMPLATE}img/icon_layout_anchor_symbol.gif\\" style=\\"margin-right: 4px;\\"><span>{HEAD:1}</span>{ELSE}<a name=\\"{HEAD:1}\\"></a>{ENDIF}', 9);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_templates_pages`
-- 

DROP TABLE IF EXISTS `cmt_templates_pages`;
CREATE TABLE `cmt_templates_pages` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_name` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_source` text collate utf8_unicode_ci,
  `cmt_position` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `cmt_templates_pages`
-- 

INSERT INTO `cmt_templates_pages` (`id`, `cmt_name`, `cmt_source`, `cmt_position`) VALUES (1, 'Standardseite', '{INCLUDE:PATHTOWEBROOT.\\"templates/standard.tpl\\"}', 1);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_users`
-- 

DROP TABLE IF EXISTS `cmt_users`;
CREATE TABLE `cmt_users` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_username` varchar(64) collate utf8_unicode_ci default NULL,
  `cmt_pass` varchar(64) collate utf8_unicode_ci default NULL,
  `cmt_exptime` int(11) default NULL,
  `cmt_uservars` text collate utf8_unicode_ci,
  `cmt_creationdate` datetime default NULL,
  `cmt_passchanged` datetime default NULL,
  `cmt_lastlogin` datetime default NULL,
  `cmt_usergroup` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_restrictions` text collate utf8_unicode_ci,
  `cmt_addvars` text collate utf8_unicode_ci,
  `cmt_showfields` text collate utf8_unicode_ci,
  `cmt_editstruct` text collate utf8_unicode_ci,
  `cmt_useralias` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_showitems` text collate utf8_unicode_ci,
  `cmt_usertype` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_userdirectory` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_startpage` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_startapp` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_cmtstyle` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `cmt_users`
-- 

INSERT INTO `cmt_users` (`id`, `cmt_username`, `cmt_pass`, `cmt_exptime`, `cmt_uservars`, `cmt_creationdate`, `cmt_passchanged`, `cmt_lastlogin`, `cmt_usergroup`, `cmt_restrictions`, `cmt_addvars`, `cmt_showfields`, `cmt_editstruct`, `cmt_useralias`, `cmt_showitems`, `cmt_usertype`, `cmt_userdirectory`, `cmt_startpage`, `cmt_startapp`, `cmt_cmtstyle`) VALUES (1, 'cmt', '2ec987485e4d734faeb439a7427f8633', 0, 'a:1:{s:12:"cmt_uservars";a:9:{i:18;a:3:{s:7:"cmt_ipp";s:2:"10";s:7:"sort_by";a:2:{i:1;s:14:"error_datetime";i:2;s:0:"";}s:8:"sort_dir";a:2:{i:1;s:4:"desc";i:2;s:3:"asc";}}i:2;a:1:{s:6:"cmtIpp";s:2:"30";}i:11;a:3:{s:7:"cmt_ipp";s:2:"10";s:7:"sort_by";s:0:"";s:8:"sort_dir";s:0:"";}i:1;a:2:{s:6:"sortBy";s:0:"";s:7:"sortDir";s:0:"";}i:14;a:3:{s:7:"cmt_ipp";s:2:"10";s:7:"sort_by";a:1:{i:1;s:12:"cmt_position";}s:8:"sort_dir";a:1:{i:1;s:3:"asc";}}i:8;a:3:{s:7:"cmt_ipp";s:0:"";s:7:"sort_by";s:0:"";s:8:"sort_dir";s:0:"";}i:13;a:3:{s:7:"cmt_ipp";s:2:"10";s:7:"sort_by";s:0:"";s:8:"sort_dir";s:0:"";}i:20;a:3:{s:7:"cmt_ipp";s:2:"10";s:7:"sort_by";s:0:"";s:8:"sort_dir";s:0:"";}i:15;a:3:{s:7:"cmt_ipp";N;s:7:"sort_by";N;s:8:"sort_dir";N;}}}', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-04-01 11:38:04', '1', '', '', '', '', 'John Doe', '', 'admin', '', '', '17', 'default/');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cmt_users_groups`
-- 

DROP TABLE IF EXISTS `cmt_users_groups`;
CREATE TABLE `cmt_users_groups` (
  `id` int(11) NOT NULL auto_increment,
  `cmt_groupname` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_showitems` text collate utf8_unicode_ci,
  `cmt_restrictions` text collate utf8_unicode_ci,
  `cmt_addvars` text collate utf8_unicode_ci,
  `cmt_showfields` text collate utf8_unicode_ci,
  `cmt_editstruct` text collate utf8_unicode_ci,
  `cmt_grouptype` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_startpage` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_startapp` varchar(255) collate utf8_unicode_ci default NULL,
  `cmt_groupdirectory` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- 
-- Daten für Tabelle `cmt_users_groups`
-- 

INSERT INTO `cmt_users_groups` (`id`, `cmt_groupname`, `cmt_showitems`, `cmt_restrictions`, `cmt_addvars`, `cmt_showfields`, `cmt_editstruct`, `cmt_grouptype`, `cmt_startpage`, `cmt_startapp`, `cmt_groupdirectory`) VALUES (1, 'Administrator', '', '', '', '', '', 'admin', NULL, NULL, NULL),
(2, 'Redakteur', '', '', '', '', '', 'user', NULL, NULL, NULL);
