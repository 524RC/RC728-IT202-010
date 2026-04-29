CREATE TABLE IF NOT EXISTS Anime (
    id            INT          PRIMARY KEY AUTO_INCREMENT,
    mal_id        INT          UNIQUE,
    is_api        TINYINT(1)   NOT NULL DEFAULT 0,
    title         VARCHAR(255) NOT NULL,
    description   TEXT,
    picture_url   VARCHAR(512),
    mal_url       VARCHAR(512),
    created       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);