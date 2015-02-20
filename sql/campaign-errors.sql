SELECT      *
FROM        `tracking`
WHERE       `campaign` = :campaign
AND         (
                `status` = "rejected"
            OR  `status` = "invalid"
            )