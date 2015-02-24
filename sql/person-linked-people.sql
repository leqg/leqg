SELECT      *
FROM        `liaisons`
WHERE       `ficheA` = :person
OR          `ficheB` = :person