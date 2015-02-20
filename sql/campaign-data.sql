SELECT      `campagne_id` AS `id`,
            `campagne_type` AS `type`,
            `campagne_titre` AS `titre`,
            `campagne_message` AS `message`,
            `campagne_date` AS `date`,
            `campagne_status` AS `status`,
            `user_id` AS `user`,
            `template`
FROM        `campagne`
WHERE       `campagne_id` = :campagne
ORDER BY    `campagne_date` ASC
LIMIT       0, 1
