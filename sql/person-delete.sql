DELETE FROM `people` WHERE `id` = :person;
DELETE FROM `historique` WHERE `contact_id` = :person;
DELETE FROM `coordonnees` WHERE `contact_id` = :person;
DELETE FROM `address` WHERE `contact` = :person;