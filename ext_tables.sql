#
# Table structure for table 'tx_news_domain_model_news'
#
CREATE TABLE tx_news_domain_model_news (
  tx_yoastseo_focuskeyword varchar(100) DEFAULT '' NOT NULL,
  og_title varchar(255) DEFAULT '' NOT NULL,
  og_description text,
  twitter_title varchar(255) DEFAULT '' NOT NULL,
  twitter_description text,
  twitter_card varchar(30) DEFAULT 'summary' NOT NULL,
  og_image int(11) unsigned DEFAULT '0' NOT NULL,
  twitter_image int(11) unsigned DEFAULT '0' NOT NULL,
);

CREATE TABLE tx_yoastnews_preview (
  token_hash varchar(64) DEFAULT '' NOT NULL,
  news_uid int(11) unsigned DEFAULT '0' NOT NULL,
  page_uid int(11) unsigned DEFAULT '0' NOT NULL,
  language_uid int(11) DEFAULT '0' NOT NULL,
  expires int(11) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (token_hash),
  KEY news_uid (news_uid)
);
