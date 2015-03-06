SELECT      `contact`
FROM        `destinataire`
WHERE       `campagne` = :campaign
ORDER BY    `contact` ASC
LIMIT       :first , 10
