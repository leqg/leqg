SELECT      `campaign`,
            `id`,
            `email` AS `coord`,
            `status`
FROM        `tracking`
WHERE       `campaign` = :campaign
AND         `status` = "queued"
ORDER BY    `id` ASC
