UPDATE      `tracking`
SET         `status` = :status
WHERE       `campaign` = :campaign
AND         `email` = :coord
