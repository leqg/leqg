SELECT      `historique`.`historique_id` AS `id`,
            MD5(`historique`.`historique_id`) AS `md5`,
            `historique`.`contact_id` AS `people`,
            `historique`.`compte_id` AS `user`,
            `historique`.`dossier_id` AS `folder`,
            `historique`.`historique_type` AS `type`,
            `historique`.`historique_lieu` AS `lieu`,
            `historique`.`historique_date` AS `date`,
            `historique`.`historique_objet` AS `objet`,
            `historique`.`historique_notes` AS `notes`,
            `historique`.`historique_timestamp` AS `time`,
            `historique`.`campagne_id` AS `campaign`,
            `people`.`nom` AS `nom`,
            `people`.`nom_usage` AS `nom_usage`,
            `people`.`prenoms` AS `prenoms`,
            `people`.`sexe` AS `sexe`,
            MD5(`people`.`id`) AS `contact_md5`,
            `people`.`organisme` AS `organisme`,
            `people`.`fonction` AS `fonction`
FROM        `historique`
LEFT JOIN   `people`
ON          `people`.`id` = `historique`.`contact_id`
WHERE       `historique`.`campagne_id` = :campaign
