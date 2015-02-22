DELETE FROM `historique` WHERE `historique_id` = :event;
DELETE FROM `fichiers` WHERE `historique_id` = :event;
DELETE FROM `taches` WHERE `historique_id` = :event;
