UPDATE      `tracking`
SET         `status` = :status,
            `reject_reason` = :reason
WHERE       `campaign` = :campaign
AND         `email` = :coord
