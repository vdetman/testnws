CREATE TABLE IF NOT EXISTS news (
  news_id int(11) UNSIGNED AUTO_INCREMENT,
  header varchar(255) NOT NULL,
  preview text NOT NULL,
  content text NOT NULL,
  hash char(32) DEFAULT NULL,
  status enum ('active', 'hidden') NOT NULL DEFAULT 'hidden',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE INDEX UK_news_hash (hash),
  PRIMARY KEY (news_id)
) ENGINE = INNODB CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS news_rubrics (
  rubric_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  rubric_sort int(11) UNSIGNED NOT NULL DEFAULT 0,
  rubric_name varchar(50) NOT NULL,
  PRIMARY KEY (rubric_id)
) ENGINE = INNODB CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS news_relations (
  news_id int(11) UNSIGNED NOT NULL,
  rubric_id int(11) UNSIGNED NOT NULL,
  UNIQUE INDEX UK_news_relations (rubric_id, news_id)
) ENGINE = INNODB CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS news_rubrics_tree (
  parent_rubric_id int(11) UNSIGNED NOT NULL,
  child_rubric_id int(11) UNSIGNED NOT NULL
) ENGINE = INNODB CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

# foreign keys
ALTER TABLE news_relations ADD CONSTRAINT FK_news_relations_news_id FOREIGN KEY (news_id) REFERENCES news (news_id) ON DELETE CASCADE;
ALTER TABLE news_relations ADD CONSTRAINT FK_news_relations_rubric_id FOREIGN KEY (rubric_id) REFERENCES news_rubrics (rubric_id) ON DELETE CASCADE;
ALTER TABLE news_rubrics_tree ADD CONSTRAINT FK_news_rubrics_tree_rubric_id_c FOREIGN KEY (child_rubric_id) REFERENCES news_rubrics (rubric_id) ON DELETE CASCADE;
ALTER TABLE news_rubrics_tree ADD CONSTRAINT FK_news_rubrics_tree_rubric_id_p FOREIGN KEY (parent_rubric_id) REFERENCES news_rubrics (rubric_id) ON DELETE CASCADE;