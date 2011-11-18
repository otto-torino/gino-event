-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 18 nov, 2011 at 12:04 AM
-- Versione MySQL: 5.1.48
-- Versione PHP: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Struttura della tabella `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `ctg` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `hours` time NOT NULL,
  `location` varchar(200) NOT NULL,
  `duration` int(1) NOT NULL,
  `informations` text NOT NULL,
  `description` text NOT NULL,
  `summary` text NOT NULL,
  `image` varchar(200) NOT NULL,
  `attachment` varchar(200) NOT NULL,
  `private` enum('yes','no') NOT NULL,
  `lng` varchar(20) NOT NULL,
  `lat` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `event_box`
--

CREATE TABLE IF NOT EXISTS `event_box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  `subtitle` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `text_attachment` varchar(200) NOT NULL,
  `image` varchar(200) NOT NULL,
  `attachment` varchar(200) NOT NULL,
  `active` enum('yes','no') NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `event_ctg`
--

CREATE TABLE IF NOT EXISTS `event_ctg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `event_grp`
--

CREATE TABLE IF NOT EXISTS `event_grp` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `no_admin` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dump dei dati per la tabella `event_grp`
--

INSERT INTO `event_grp` (`id`, `name`, `description`, `no_admin`) VALUES
(1, 'responsabili', 'Gestisce l''assegnazione degli utenti ai singoli gruppi. Gestisce la creazione e modifica di eventi.', 'no'),
(2, 'utenti privati', 'Visualizza eventi privati', 'yes');

-- --------------------------------------------------------

--
-- Struttura della tabella `event_opt`
--

CREATE TABLE IF NOT EXISTS `event_opt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `viewList_title` varchar(200) NOT NULL,
  `card_title` varchar(200) NOT NULL,
  `searchPage_title` varchar(200) NOT NULL,
  `viewCal_title` varchar(200) NOT NULL,
  `first_day_monday` tinyint(1) NOT NULL,
  `day_chars` int(2) NOT NULL,
  `wide_view` tinyint(1) NOT NULL,
  `wide_view_position` int(2) NOT NULL,
  `manage_ctg` tinyint(1) NOT NULL,
  `manage_sel` tinyint(1) NOT NULL,
  `manage_newsl` tinyint(1) NOT NULL,
  `manage_sort` tinyint(1) NOT NULL,
  `items_for_page` int(3) NOT NULL,
  `char_summary` int(4) NOT NULL,
  `img_width` int(5) NOT NULL,
  `thumb_width` int(5) NOT NULL,
  `eventLayer` tinyint(1) NOT NULL,
  `winWidth` int(4) NOT NULL,
  `winHeight` int(4) NOT NULL,
  `randomViewer_title` varchar(255) NOT NULL,
  `randomViewer_num` int(4) NOT NULL,
  `selectedViewerA_title` varchar(255) NOT NULL,
  `selectedViewerA_num` int(4) NOT NULL,
  `selectedViewerB_title` varchar(255) NOT NULL,
  `selectedViewerB_num` int(4) NOT NULL,
  `personalizedViewer_title` varchar(255) NOT NULL,
  `personalizedViewer_num` int(4) NOT NULL,
  `archiveViewer_title` varchar(255) NOT NULL,
  `ctgViewerA_id` int(11) NOT NULL,
  `ctgViewerA_title` varchar(255) NOT NULL,
  `ctgViewerA_num` int(4) NOT NULL,
  `ctgViewerA_pag` tinyint(1) NOT NULL,
  `ctgViewerB_id` int(11) NOT NULL,
  `ctgViewerB_title` varchar(255) NOT NULL,
  `ctgViewerB_num` int(4) NOT NULL,
  `ctgViewerB_pag` tinyint(1) NOT NULL,
  `ctgViewerC_id` int(11) NOT NULL,
  `ctgViewerC_title` varchar(255) NOT NULL,
  `ctgViewerC_num` int(4) NOT NULL,
  `ctgViewerC_pag` tinyint(1) NOT NULL,
  `ctgViewerD_id` int(11) NOT NULL,
  `ctgViewerD_title` varchar(255) NOT NULL,
  `ctgViewerD_num` int(4) NOT NULL,
  `ctgViewerD_pag` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `event_sel`
--

CREATE TABLE IF NOT EXISTS `event_sel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `reference` int(11) NOT NULL,
  `aggregator` int(11) NOT NULL,
  `priority` smallint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `event_sel_b`
--

CREATE TABLE IF NOT EXISTS `event_sel_b` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `reference` int(11) NOT NULL,
  `aggregator` int(11) NOT NULL,
  `priority` smallint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `event_usr`
--

CREATE TABLE IF NOT EXISTS `event_usr` (
  `instance` int(11) NOT NULL,
  `group_id` int(2) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
