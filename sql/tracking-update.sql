UPDATE      `tracking`
SET         `id` = :id,
            `status` = :status,
            `reject_reason` = :reject
WHERE       `campaign` = :campaign
AND         `email` = :email