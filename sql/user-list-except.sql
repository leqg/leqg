SELECT      `email`,
            `firstname`, 
            `lastname`,
            `auth_level`,
            `client`,
            `last_reinit`,
            `id`,
            `phone`,
            `last_login`
FROM        `user` 
WHERE       `auth_level` >= :auth
AND         `client` = :client
AND         `id` NOT IN (:exclude)
