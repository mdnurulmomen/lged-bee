-- 20-10-2021
ALTER TABLE `ac_queries` ADD `team_leader_name_en` VARCHAR(255) NOT NULL AFTER `team_id`, ADD `team_leader_name_bn` VARCHAR(255) NOT NULL AFTER `team_leader_name_en`;
