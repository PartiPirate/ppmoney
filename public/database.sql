-- SQL Dump

--
-- Structure de la table `counter_parties`
--

CREATE TABLE IF NOT EXISTS `counter_parties` (
  `cpa_id` int(11) NOT NULL AUTO_INCREMENT,
  `cpa_project_id` int(11) NOT NULL,
  `cpa_amount` decimal(10,2) NOT NULL,
  `cpa_content` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`cpa_id`),
  KEY `cpa_project_id` (`cpa_project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `pro_id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_code` varchar(255) NOT NULL,
  `pro_label` varchar(255) NOT NULL,
  `pro_content` text NOT NULL,
  `pro_amount_goal` decimal(10,2) NOT NULL,
  `pro_status` enum('open','finished','canceled') NOT NULL DEFAULT 'open',
  PRIMARY KEY (`pro_id`),
  KEY `pro_status` (`pro_status`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `tra_id` int(11) NOT NULL AUTO_INCREMENT,
  `tra_amount` decimal(10,2) NOT NULL,
  `tra_reference` varchar(255) NOT NULL,
  `tra_status` enum('calling','accepted','refused','canceled') NOT NULL DEFAULT 'calling',
  `tra_confirmed` tinyint(4) NOT NULL DEFAULT '0',
  `tra_date` datetime NOT NULL,
  `tra_email` varchar(255) NOT NULL,
  `tra_purpose` text NOT NULL COMMENT 'Contenu JSON du but de la transaction',
  `tra_lastname` varchar(255) DEFAULT NULL,
  `tra_firstname` varchar(255) DEFAULT NULL,
  `tra_address` varchar(255) DEFAULT NULL,
  `tra_zipcode` varchar(255) DEFAULT NULL,
  `tra_city` varchar(255) DEFAULT NULL,
  `tra_country` varchar(255) DEFAULT NULL,
  `tra_telephone` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`tra_id`),
  KEY `tra_reference` (`tra_reference`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Gère les transactions' AUTO_INCREMENT=1337 ;
