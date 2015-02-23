SELECT      `campagne_id` AS `id`,
            `user_id` AS `user`,
            `campagne_type` AS `type`,
            `campagne_status` AS `status`,
            `titre`,
            `message`, 
            `campagne_date` AS `date`
FROM        `campagne`
WHERE       `campagne_type` = :type
AND         `campagne_status` != 'close'
ORDER BY    `campagne_date` DESC
