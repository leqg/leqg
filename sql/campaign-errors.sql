SELECT      *
FROM        `tracking`
WHERE       `campaign` = :campaign
AND         `control` = 1
AND         (
                `status` = "rejected"
            OR  `status` = "invalid"
            )