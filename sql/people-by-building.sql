SELECT      `people` AS `id`
FROM        `address`
WHERE       `building` = :building
AND         `type` = "officiel"
