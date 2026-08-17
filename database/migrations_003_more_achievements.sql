-- Migration: expand achievements beyond "complete N quests" — reward posting
-- quests and leveling up too, so both requesters and helpers have goals.
-- Idempotent (INSERT IGNORE by unique `code`).

INSERT IGNORE INTO achievements (code, title, description, icon, requirement_type, requirement_value, xp_bonus, coins_bonus) VALUES
-- more completed-quest milestones
('quest_hattrick', 'Hat Trick', 'Complete 3 quests.', 'medal_bronze', 'completed_quests', 3, 15, 5),
('quest_dozen', 'Dozen Deeds', 'Complete 10 quests.', 'medal_silver', 'completed_quests', 10, 75, 35),
('quest_score', 'Score of Contracts', 'Complete 20 quests.', 'medal_silver', 'completed_quests', 20, 150, 75),
('quest_century', 'Century Club', 'Complete 50 quests.', 'medal_gold', 'completed_quests', 50, 400, 200),
('quest_master', 'Board Legend', 'Complete 100 quests.', 'medal_platinum', 'completed_quests', 100, 1000, 500),

-- posting quests (for requesters)
('post_first', 'First Contract', 'Post your first quest request.', 'scroll_bronze', 'quests_posted', 1, 10, 5),
('post_patron', 'Patron of the Board', 'Post 5 quest requests.', 'scroll_silver', 'quests_posted', 5, 40, 20),
('post_benefactor', 'Benefactor', 'Post 20 quest requests.', 'scroll_gold', 'quests_posted', 20, 120, 60),

-- leveling up
('level_5', 'Novice Adventurer', 'Reach level 5.', 'star_bronze', 'level_reached', 5, 0, 25),
('level_10', 'Seasoned Hunter', 'Reach level 10.', 'star_silver', 'level_reached', 10, 0, 50),
('level_25', 'Veteran of the Path', 'Reach level 25.', 'star_gold', 'level_reached', 25, 0, 150),
('level_50', 'Living Legend', 'Reach level 50.', 'star_platinum', 'level_reached', 50, 0, 400);
