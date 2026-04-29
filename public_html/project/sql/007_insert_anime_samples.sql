INSERT INTO Anime (mal_id, is_api, title, description, picture_url, mal_url) VALUES

-- API-fetched rows (is_api = 1, mal_id populated from API response)
(21, 1, 'One Piece',
 'Barely surviving in a barrel after passing through a terrible whirlpool at sea, carefree Monkey D. Luffy ends up aboard a ship under attack by fearsome pirates.',
 'https://cdn.myanimelist.net/r/50x70/images/anime/6/73245.jpg',
 'https://myanimelist.net/anime/21/One_Piece'),

(30276, 1, 'One Punch Man',
 'The seemingly unimpressive Saitama has a rather unique hobby: being a hero. In order to pursue his childhood dream, Saitama relentlessly trained for three years.',
 'https://cdn.myanimelist.net/r/50x70/images/anime/12/76049.jpg',
 'https://myanimelist.net/anime/30276/One_Punch_Man'),

(32182, 1, 'Mob Psycho 100',
 'Eighth-grader Shigeo "Mob" Kageyama has tapped into his inner wellspring of psychic prowess at a young age.',
 'https://cdn.myanimelist.net/r/50x70/images/anime/8/80356.jpg',
 'https://myanimelist.net/anime/32182/Mob_Psycho_100'),

-- Custom/manual rows (is_api = 0, mal_id NULL)
(NULL, 0, 'My Favorite Underrated Anime',
 'A personal recommendation not pulled from any API search.',
 NULL, NULL),

(NULL, 0, 'Local Animation Project',
 NULL, NULL, NULL);