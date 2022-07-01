CREATE TABLE be_groups
(
    identifier varchar(255) DEFAULT '' NOT NULL,
    code_managed_group int(1) DEFAULT 0,
    deploy_processing varchar(255) DEFAULT '' NOT NULL
);

CREATE TABLE cache_bepermissions_apiroutes
(
    id int(11) unsigned NOT NULL auto_increment,
    identifier varchar(250) DEFAULT '' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    content mediumblob,
    lifetime int(11) unsigned DEFAULT '0' NOT NULL,
    PRIMARY KEY (id),
    KEY cache_id (identifier)
) ENGINE=InnoDB;