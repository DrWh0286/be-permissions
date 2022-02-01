CREATE TABLE be_groups
(
    identifier varchar(255) DEFAULT '' NOT NULL,
    bulk_export int(1) DEFAULT 0,
    deploy_processing varchar(255) DEFAULT '' NOT NULL
);