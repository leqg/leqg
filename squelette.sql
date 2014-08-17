CREATE TABLE `arrondissements` (
  `arrondissement_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `arrondissement_numero` smallint(2) unsigned zerofill NOT NULL,
  `departement_id` smallint(3) unsigned NOT NULL,
  `arrondissement_nom` varchar(50) NOT NULL,
  `arrondissement_chef_lieu` int(5) NOT NULL,
  PRIMARY KEY (`arrondissement_id`),
  UNIQUE KEY `Arrondissement` (`departement_id`,`arrondissement_numero`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `bureaux` (
  `bureau_id` int(11) NOT NULL AUTO_INCREMENT,
  `canton_id` smallint(4) unsigned NOT NULL,
  `commune_id` mediumint(5) unsigned NOT NULL,
  `bureau_numero` int(5) unsigned NOT NULL,
  `bureau_nom` varchar(100) NOT NULL,
  `bureau_adresse` varchar(255) NOT NULL,
  `bureau_cp` int(5) unsigned zerofill NOT NULL,
  `bureau_ville` varchar(65) NOT NULL,
  PRIMARY KEY (`bureau_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `cantons` (
  `canton_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `arrondissement_id` smallint(2) unsigned NOT NULL,
  `canton_numero` smallint(3) unsigned NOT NULL,
  `canton_nom` varchar(50) NOT NULL,
  `canton_chef_lieu` int(5) NOT NULL,
  PRIMARY KEY (`canton_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `codes_postaux` (
  `code_postal` mediumint(5) unsigned zerofill NOT NULL,
  `commune_id` mediumint(5) unsigned zerofill NOT NULL,
  PRIMARY KEY (`code_postal`),
  UNIQUE KEY `commune_id` (`commune_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `communes` (
  `commune_id` mediumint(5) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `departement_id` smallint(3) unsigned NOT NULL,
  `commune_nom` varchar(100) NOT NULL,
  `commune_nom_propre` varchar(100) NOT NULL,
  PRIMARY KEY (`commune_id`),
  KEY `departement_id` (`departement_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `compte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `nickname` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` int(10) unsigned zerofill NOT NULL,
  `autorisations` int(1) unsigned NOT NULL,
  `derniere_connexion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `demande_reinitialisation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `contacts` (
  `contact_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `immeuble_id` mediumint(6) unsigned NOT NULL,
  `contact_nom` varchar(100) NOT NULL,
  `contact_nom_usage` varchar(100) NOT NULL,
  `contact_prenoms` varchar(100) NOT NULL,
  `contact_naissance_date` date NOT NULL,
  `contact_naissance_commune_id` mediumint(5) NOT NULL,
  `contact_sexe` set('M','F','i') NOT NULL DEFAULT 'i',
  `contact_deces` tinyint(1) NOT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_mobile` int(10) unsigned zerofill DEFAULT NULL,
  `contact_telephone` int(10) unsigned zerofill DEFAULT NULL,
  `contact_twitter` varchar(100) DEFAULT NULL,
  `contact_optout_global` tinyint(1) NOT NULL DEFAULT '0',
  `contact_optout_email` tinyint(1) NOT NULL DEFAULT '0',
  `contact_optout_mobile` tinyint(1) NOT NULL DEFAULT '0',
  `contact_optout_telephone` tinyint(1) NOT NULL DEFAULT '0',
  `contact_electeur` tinyint(1) NOT NULL,
  `contact_electeur_europeen` tinyint(1) NOT NULL,
  `contact_electeur_numero` int(10) unsigned NOT NULL,
  `contact_militant` tinyint(1) NOT NULL,
  `contact_sympathisant` tinyint(1) NOT NULL,
  `contact_tags` text,
  PRIMARY KEY (`contact_id`),
  KEY `contact_nom` (`contact_nom`,`contact_nom_usage`,`contact_prenoms`),
  KEY `commune_id` (`immeuble_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `departements` (
  `departement_id` smallint(3) unsigned NOT NULL,
  `region_id` smallint(2) unsigned DEFAULT NULL,
  `departement_nom` varchar(50) DEFAULT NULL,
  `departement_chef_lieu` int(5) NOT NULL,
  PRIMARY KEY (`departement_id`),
  UNIQUE KEY `departement_chef_lieu` (`departement_chef_lieu`),
  KEY `region_id` (`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `dossiers` (
  `dossier_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dossier_nom` varchar(255) CHARACTER SET latin1 NOT NULL,
  `dossier_description` text CHARACTER SET latin1 NOT NULL,
  `dossier_contacts` text CHARACTER SET latin1 NOT NULL,
  `dossier_statut` tinyint(1) NOT NULL DEFAULT '1',
  `dossier_date_ouverture` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dossier_date_fermeture` datetime DEFAULT NULL,
  PRIMARY KEY (`dossier_id`),
  KEY `dossier_nom` (`dossier_nom`,`dossier_date_ouverture`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fichiers` (
  `fichier_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `compte_id` int(11) NOT NULL,
  `interaction_id` int(11) NOT NULL,
  `dossier_id` int(11) NOT NULL,
  `fichier_nom` varchar(255) CHARACTER SET latin1 NOT NULL,
  `fichier_labels` text CHARACTER SET latin1 NOT NULL,
  `fichier_description` text CHARACTER SET latin1 NOT NULL,
  `fichier_url` text CHARACTER SET latin1 NOT NULL,
  `fichier_reference` varchar(255) NOT NULL,
  `fichier_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fichier_id`),
  KEY `fichier_reference` (`fichier_reference`),
  KEY `contact_id` (`contact_id`),
  KEY `fichier_nom` (`fichier_nom`),
  KEY `compte_id` (`compte_id`),
  KEY `objet_id` (`interaction_id`),
  KEY `dossier_id` (`dossier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `historique` (
  `historique_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` bigint(20) NOT NULL,
  `compte_id` int(11) NOT NULL,
  `historique_type` set('contact','telephone','email','courrier','autre') NOT NULL,
  `historique_date` date NOT NULL,
  `historique_lieu` varchar(255) NOT NULL,
  `historique_objet` varchar(255) NOT NULL,
  `historique_thematiques` text NOT NULL,
  `historique_notes` text NOT NULL,
  `historique_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`historique_id`),
  KEY `contact_id` (`contact_id`),
  KEY `compte_id` (`compte_id`),
  KEY `contact_id_2` (`contact_id`),
  KEY `historique_objet` (`historique_objet`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `immeubles` (
  `immeuble_id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `bureau_id` mediumint(5) unsigned NOT NULL,
  `rue_id` mediumint(6) unsigned NOT NULL,
  `immeuble_numero` varchar(5) NOT NULL,
  PRIMARY KEY (`immeuble_id`),
  KEY `bureau_id` (`bureau_id`),
  KEY `rue_id` (`rue_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `regions` (
  `region_id` smallint(2) unsigned NOT NULL,
  `region_nom` varchar(50) DEFAULT NULL,
  `region_chef_lieu` int(5) NOT NULL,
  PRIMARY KEY (`region_id`),
  KEY `region` (`region_id`,`region_nom`),
  KEY `region_chef_lieu` (`region_chef_lieu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `rues` (
  `rue_id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `commune_id` mediumint(5) unsigned zerofill NOT NULL,
  `rue_nom` tinytext NOT NULL,
  PRIMARY KEY (`rue_id`),
  KEY `commune_id` (`commune_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `taches` (
  `tache_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `compte_id` int(10) unsigned NOT NULL,
  `tache_description` text NOT NULL,
  `tache_deadline` date NOT NULL,
  `tache_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tache_contacts` text NOT NULL,
  `tache_destinataire` text NOT NULL,
  `tache_terminee` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tache_id`),
  KEY `compte_id` (`compte_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tags` (
  `tag_nom` varchar(100) NOT NULL,
  PRIMARY KEY (`tag_nom`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
