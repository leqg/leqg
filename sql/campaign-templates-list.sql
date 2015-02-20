SELECT      `campagne_id` AS `id`,
            `campagne_type` AS `type`,
            `campagne_titre` AS `titre`,
            `template`
FROM        `campagne`
WHERE       `template` != ''
ORDER BY    `campagne_date` ASC
LIMIT       0, 1
