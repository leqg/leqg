SELECT      COUNT(*)
FROM        `people`
WHERE       `people`.`electeur` = 1
AND         `people`.`id` IN (
    SELECT      `address`.`people`
    FROM        `address`
    WHERE       `address`.`city` = :city
)