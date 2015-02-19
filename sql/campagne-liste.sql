SELECT      `campagne_id` AS `id`,
            `user_id` AS `user`,
            `campagne_type` AS `type`,
            `campagne_titre` AS `titre`,
            `campagne_message` AS `message`, 
            `campagne_date` AS `date`
FROM        `campagne`
WHERE       `campagne_type` = :type
ORDER BY    `campagne_date` DESC