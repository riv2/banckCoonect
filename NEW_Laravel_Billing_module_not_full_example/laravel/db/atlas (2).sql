-- Adminer 4.7.7 PostgreSQL dump

DROP TABLE IF EXISTS "_billing_balance";
DROP SEQUENCE IF EXISTS _billing_balance_uid_seq;
CREATE SEQUENCE _billing_balance_uid_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 1 CACHE 1;

CREATE TABLE "public"."_billing_balance" (
    "uid" integer DEFAULT nextval('_billing_balance_uid_seq') NOT NULL,
    "uniq_member" character varying(20),
    "uniq_company" character varying(20),
    "balance" double precision NOT NULL,
    "date_updated" character varying(30),
    CONSTRAINT "_billing_balance_pkey" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_billing_currences";
CREATE TABLE "public"."_billing_currences" (
    "uniq" character varying(20) NOT NULL,
    "name" character varying(20) NOT NULL,
    "ratio" double precision NOT NULL,
    "code" character varying(3) NOT NULL,
    "date_created" character varying(30) NOT NULL,
    "date_updated" character varying(30) NOT NULL,
    CONSTRAINT "_billing_currences_pkey" PRIMARY KEY ("uniq")
) WITH (oids = false);


DROP TABLE IF EXISTS "_billing_discounts";
CREATE TABLE "public"."_billing_discounts" (
    "uniq" character varying(20) NOT NULL,
    "uniq_mamber" character varying(20) NOT NULL,
    "uniq_company" character varying(20) NOT NULL,
    "sum" double precision NOT NULL,
    "type" character varying(255) NOT NULL,
    "advanced" text NOT NULL,
    "date_created" character varying(30) NOT NULL,
    "date_updated" character varying(30) NOT NULL,
    CONSTRAINT "_billing_discounts_pkey" PRIMARY KEY ("uniq")
) WITH (oids = false);


DROP TABLE IF EXISTS "_billing_gateways";
CREATE TABLE "public"."_billing_gateways" (
    "uniq" character varying(20) NOT NULL,
    "name" character varying(255) NOT NULL,
    "date_created" character varying(30) NOT NULL,
    "date_updated" character varying(30) NOT NULL,
    "uniqs_currencies" text NOT NULL,
    "advanced" text NOT NULL,
    "settings" text NOT NULL,
    "enabled" character varying(1) NOT NULL,
    CONSTRAINT "_billing_gateways_pkey" PRIMARY KEY ("uniq")
) WITH (oids = false);


DROP TABLE IF EXISTS "_billing_logs";
DROP SEQUENCE IF EXISTS _billing_logs_uid_seq;
CREATE SEQUENCE _billing_logs_uid_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 1 CACHE 1;

CREATE TABLE "public"."_billing_logs" (
    "uid" integer DEFAULT nextval('_billing_logs_uid_seq') NOT NULL,
    "requester" character varying(255) NOT NULL,
    "uniqMember" character varying(255),
    "status" character varying(255) NOT NULL,
    "data" text NOT NULL,
    "advanced" text,
    "date_created" character varying(30) NOT NULL,
    CONSTRAINT "_billing_logs_pkey" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_billing_products";
DROP SEQUENCE IF EXISTS _billing_products_uid_seq;
CREATE SEQUENCE _billing_products_uid_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 1 CACHE 1;

CREATE TABLE "public"."_billing_products" (
    "uid" integer DEFAULT nextval('_billing_products_uid_seq') NOT NULL,
    "name" character varying(255) NOT NULL,
    "date_created" character varying(30) NOT NULL,
    "date_updated" character varying(30) NOT NULL,
    "article" character varying(255),
    "advanced" text,
    "type_product" character varying(20),
    "table" character varying(255),
    "uniq_table" character varying(255),
    "status" character varying(255),
    "code" character varying(255),
    "advanced_value" character varying(255),
    CONSTRAINT "_billing_products_pkey" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_billing_products_cost";
DROP SEQUENCE IF EXISTS _billing_products_cost_uid_seq;
CREATE SEQUENCE _billing_products_cost_uid_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 1 CACHE 1;

CREATE TABLE "public"."_billing_products_cost" (
    "uid" integer DEFAULT nextval('_billing_products_cost_uid_seq') NOT NULL,
    "uid_product" character varying(20),
    "uniq_currency" character varying(20) NOT NULL,
    "date_created" character varying(30) NOT NULL,
    "date_updated" character varying(30) NOT NULL,
    "article" character varying(255),
    "cost" double precision NOT NULL,
    "count" character varying(20) NOT NULL,
    "advanced" text,
    CONSTRAINT "_billing_products_cost_pkey" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_billing_transactions";
DROP SEQUENCE IF EXISTS _billing_transactions_uid_seq;
CREATE SEQUENCE _billing_transactions_uid_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 1 CACHE 1;

CREATE TABLE "public"."_billing_transactions" (
    "uid" integer DEFAULT nextval('_billing_transactions_uid_seq') NOT NULL,
    "uniq_member" character varying(20) NOT NULL,
    "uid_product" character varying(20) NOT NULL,
    "type_transaction" character varying(20) NOT NULL,
    "hide_transaction" character varying(20),
    "sum" double precision NOT NULL,
    "product_serialize" text,
    "signature" character varying(255),
    "date_created" character varying(30) NOT NULL,
    "date" character varying(30) NOT NULL,
    CONSTRAINT "_billing_transactions_pkey" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_catalogues";
DROP SEQUENCE IF EXISTS seq_catalogues_uid;
CREATE SEQUENCE seq_catalogues_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_catalogues" (
    "name" character varying(32),
    "type" character varying(16),
    "access" integer,
    "date_created" bigint,
    "uid" bigint DEFAULT nextval('seq_catalogues_uid') NOT NULL,
    "owner" character varying(7),
    CONSTRAINT "pkey_catalogues_uid" PRIMARY KEY ("uid")
) WITH (oids = false);

CREATE INDEX "idx_catalogues_access" ON "public"."_catalogues" USING btree ("access");

CREATE INDEX "idx_catalogues_created" ON "public"."_catalogues" USING btree ("date_created");

CREATE INDEX "idx_catalogues_name" ON "public"."_catalogues" USING btree ("name");

CREATE INDEX "idx_catalogues_owner" ON "public"."_catalogues" USING btree ("owner");


DROP TABLE IF EXISTS "_catalogues_groups";
CREATE TABLE "public"."_catalogues_groups" (
    "uniq" character varying(32) NOT NULL,
    "member_uniq" character varying(7),
    "date_modified" bigint,
    "date_deleted" bigint,
    "catalogue_uid" bigint,
    "product_type" integer,
    "hierarchy_uniq" character varying(32),
    CONSTRAINT "pkey_catalogues_articles_uniq" PRIMARY KEY ("uniq")
) WITH (oids = false);

CREATE INDEX "idx_catalogues_articles_catalogue" ON "public"."_catalogues_groups" USING btree ("catalogue_uid");

CREATE INDEX "idx_catalogues_articles_deleted" ON "public"."_catalogues_groups" USING btree ("date_deleted");

CREATE INDEX "idx_catalogues_articles_hierarchy" ON "public"."_catalogues_groups" USING btree ("hierarchy_uniq");

CREATE INDEX "idx_catalogues_articles_member" ON "public"."_catalogues_groups" USING btree ("member_uniq");

CREATE INDEX "idx_catalogues_articles_modified" ON "public"."_catalogues_groups" USING btree ("date_modified");

CREATE INDEX "idx_catalogues_articles_product_type" ON "public"."_catalogues_groups" USING btree ("product_type");


DROP TABLE IF EXISTS "_catalogues_hierarchy";
CREATE TABLE "public"."_catalogues_hierarchy" (
    "uniq" character varying(32) NOT NULL,
    "member_uniq" character varying(7),
    "product_type" integer,
    "name" character varying(128),
    "path" character varying(512),
    "parent_uniq" character varying(32),
    "date_modified" bigint,
    "date_deleted" bigint,
    "catalogue_uid" bigint,
    "company_uid" bigint,
    CONSTRAINT "pkey_catalogues_hierarchy_uniq" PRIMARY KEY ("uniq")
) WITH (oids = false);

CREATE INDEX "idx_catalogues_hierarchy_catalogue" ON "public"."_catalogues_hierarchy" USING btree ("catalogue_uid");

CREATE INDEX "idx_catalogues_hierarchy_company" ON "public"."_catalogues_hierarchy" USING btree ("company_uid");

CREATE INDEX "idx_catalogues_hierarchy_deleted" ON "public"."_catalogues_hierarchy" USING btree ("date_deleted");

CREATE INDEX "idx_catalogues_hierarchy_member" ON "public"."_catalogues_hierarchy" USING btree ("member_uniq");

CREATE INDEX "idx_catalogues_hierarchy_modified" ON "public"."_catalogues_hierarchy" USING btree ("date_modified");

CREATE INDEX "idx_catalogues_hierarchy_name" ON "public"."_catalogues_hierarchy" USING btree ("name");

CREATE INDEX "idx_catalogues_hierarchy_parent" ON "public"."_catalogues_hierarchy" USING btree ("parent_uniq");

CREATE INDEX "idx_catalogues_hierarchy_path" ON "public"."_catalogues_hierarchy" USING btree ("path");

CREATE INDEX "idx_catalogues_hierarchy_product_type" ON "public"."_catalogues_hierarchy" USING btree ("product_type");


DROP TABLE IF EXISTS "_catalogues_materials";
CREATE TABLE "public"."_catalogues_materials" (
    "uniq" character varying(40) NOT NULL,
    "diffuse" bigint,
    "specular" bigint,
    "reflection" bigint,
    "ior" bigint,
    "material_type" integer,
    "date_modified" bigint,
    "date_deleted" bigint,
    CONSTRAINT "pkey_catalogues_materials_uniq" PRIMARY KEY ("uniq")
) WITH (oids = false);

CREATE INDEX "idx_catalogues_materials_deleted" ON "public"."_catalogues_materials" USING btree ("date_deleted");

CREATE INDEX "idx_catalogues_materials_material_type" ON "public"."_catalogues_materials" USING btree ("material_type");

CREATE INDEX "idx_catalogues_materials_modified" ON "public"."_catalogues_materials" USING btree ("date_modified");


DROP TABLE IF EXISTS "_catalogues_materials_references";
DROP SEQUENCE IF EXISTS seq_catalogues_materials_references_uid;
CREATE SEQUENCE seq_catalogues_materials_references_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_catalogues_materials_references" (
    "material_uniq" character varying(80),
    "material_channel" integer,
    "reference_uniq" character varying(32),
    "reference_type" integer,
    "date_modified" bigint,
    "date_deleted" bigint,
    "dim_x" double precision,
    "dim_y" double precision,
    "offset_x" double precision,
    "offset_y" double precision,
    "uid" bigint DEFAULT nextval('seq_catalogues_materials_references_uid') NOT NULL,
    CONSTRAINT "pkey_catalogues_materials_references_uid" PRIMARY KEY ("uid")
) WITH (oids = false);

CREATE INDEX "idx_catalogues_metrials_references_channel" ON "public"."_catalogues_materials_references" USING btree ("material_channel");

CREATE INDEX "idx_catalogues_metrials_references_deleted" ON "public"."_catalogues_materials_references" USING btree ("date_deleted");

CREATE INDEX "idx_catalogues_metrials_references_material" ON "public"."_catalogues_materials_references" USING btree ("material_uniq");

CREATE INDEX "idx_catalogues_metrials_references_modified" ON "public"."_catalogues_materials_references" USING btree ("date_modified");

CREATE INDEX "idx_catalogues_metrials_references_reference_type" ON "public"."_catalogues_materials_references" USING btree ("reference_type");

CREATE INDEX "idx_catalogues_metrials_references_refernece_uniq" ON "public"."_catalogues_materials_references" USING btree ("reference_uniq");


DROP TABLE IF EXISTS "_catalogues_products";
CREATE TABLE "public"."_catalogues_products" (
    "uniq" character varying(40) NOT NULL,
    "member_uniq" character varying(7),
    "manufactorer" character varying(128),
    "dim_x" integer,
    "dim_y" integer,
    "dim_z" integer,
    "flags" bigint,
    "status" integer,
    "date_modified" bigint,
    "date_deleted" bigint,
    "catalogue_uid" bigint,
    "name" character varying(128),
    CONSTRAINT "pkey_catalogues_products_uniq" PRIMARY KEY ("uniq")
) WITH (oids = false);

CREATE INDEX "idx_catalogues_products_catalogue_uid" ON "public"."_catalogues_products" USING btree ("catalogue_uid");

CREATE INDEX "idx_catalogues_products_deleted" ON "public"."_catalogues_products" USING btree ("date_deleted");

CREATE INDEX "idx_catalogues_products_dim_x" ON "public"."_catalogues_products" USING btree ("dim_x");

CREATE INDEX "idx_catalogues_products_dim_y" ON "public"."_catalogues_products" USING btree ("dim_y");

CREATE INDEX "idx_catalogues_products_dim_z" ON "public"."_catalogues_products" USING btree ("dim_z");

CREATE INDEX "idx_catalogues_products_flags" ON "public"."_catalogues_products" USING btree ("flags");

CREATE INDEX "idx_catalogues_products_manufactorer" ON "public"."_catalogues_products" USING btree ("manufactorer");

CREATE INDEX "idx_catalogues_products_modified" ON "public"."_catalogues_products" USING btree ("date_modified");

CREATE INDEX "idx_catalogues_products_name" ON "public"."_catalogues_products" USING btree ("name");

CREATE INDEX "idx_catalogues_products_owner" ON "public"."_catalogues_products" USING btree ("member_uniq");

CREATE INDEX "idx_catalogues_products_status" ON "public"."_catalogues_products" USING btree ("status");


DROP TABLE IF EXISTS "_catalogues_products_groups";
DROP SEQUENCE IF EXISTS seq_catalogues_products_groups_uid;
CREATE SEQUENCE seq_catalogues_products_groups_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_catalogues_products_groups" (
    "uid" bigint DEFAULT nextval('seq_catalogues_products_groups_uid') NOT NULL,
    "member_uniq" character varying(7),
    "group_uniq" character varying(32),
    "product_uniq" character varying(80),
    "date_modified" bigint,
    "date_deleted" bigint,
    CONSTRAINT "pkey_catalogues_products_groups_uid" PRIMARY KEY ("uid")
) WITH (oids = false);

CREATE INDEX "idx_catalogues_products_groups_deleted" ON "public"."_catalogues_products_groups" USING btree ("date_deleted");

CREATE INDEX "idx_catalogues_products_groups_group" ON "public"."_catalogues_products_groups" USING btree ("group_uniq");

CREATE INDEX "idx_catalogues_products_groups_member" ON "public"."_catalogues_products_groups" USING btree ("member_uniq");

CREATE INDEX "idx_catalogues_products_groups_modified" ON "public"."_catalogues_products_groups" USING btree ("date_modified");

CREATE INDEX "idx_catalogues_products_groups_product" ON "public"."_catalogues_products_groups" USING btree ("product_uniq");


DROP TABLE IF EXISTS "_catalogues_properties";
DROP SEQUENCE IF EXISTS seq_catalogues_properties_uid;
CREATE SEQUENCE seq_catalogues_properties_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_catalogues_properties" (
    "uid" bigint DEFAULT nextval('seq_catalogues_properties_uid') NOT NULL,
    "catalogue_uid" bigint,
    "code" character varying(32),
    "name" character varying(32),
    "value" character varying(32),
    CONSTRAINT "pkey_catalogues_properties_uid" PRIMARY KEY ("uid")
) WITH (oids = false);

CREATE INDEX "idx_catalogues_properties_catalogue_uid" ON "public"."_catalogues_properties" USING btree ("catalogue_uid");

CREATE INDEX "idx_catalogues_properties_code" ON "public"."_catalogues_properties" USING btree ("code");

CREATE INDEX "idx_catalogues_properties_name" ON "public"."_catalogues_properties" USING btree ("name");

CREATE INDEX "idx_catalogues_properties_value" ON "public"."_catalogues_properties" USING btree ("value");


DROP TABLE IF EXISTS "_catalogues_resources";
CREATE TABLE "public"."_catalogues_resources" (
    "uniq" character varying(32) NOT NULL,
    "path_source" character varying(256),
    "checksum" character varying(128),
    "date_modified" bigint,
    "size_factor" bigint,
    CONSTRAINT "pkey_catalogues_resources_uniq" PRIMARY KEY ("uniq")
) WITH (oids = false);

CREATE INDEX "idx_catalogues_resources_checksum" ON "public"."_catalogues_resources" USING btree ("checksum");

CREATE INDEX "idx_catalogues_resources_modified" ON "public"."_catalogues_resources" USING btree ("date_modified");

CREATE INDEX "idx_catalogues_resources_source" ON "public"."_catalogues_resources" USING btree ("path_source");


DROP TABLE IF EXISTS "_companies";
DROP SEQUENCE IF EXISTS seq_companies_uid;
CREATE SEQUENCE seq_companies_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_companies" (
    "uid" bigint DEFAULT nextval('seq_companies_uid') NOT NULL,
    "name" character varying(128),
    "hq_address" character varying(256),
    "logo" character varying(64),
    "corporate_id" character varying(20),
    "followers" character varying(256),
    "country" character varying(32),
    "city" character varying(32),
    "owner" character varying,
    "hidden" smallint,
    "allow" smallint,
    "phone" character varying,
    CONSTRAINT "pkey_companies_uid" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_companies_branches";
DROP SEQUENCE IF EXISTS seq_companies_branches_uid;
CREATE SEQUENCE seq_companies_branches_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_companies_branches" (
    "uid" bigint DEFAULT nextval('seq_companies_branches_uid') NOT NULL,
    "company_uid" bigint,
    "name" character varying(128),
    "address" character varying(256)
) WITH (oids = false);


DROP TABLE IF EXISTS "_companies_catalogues";
DROP SEQUENCE IF EXISTS seq_companies_catalogues_uid;
CREATE SEQUENCE seq_companies_catalogues_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_companies_catalogues" (
    "uid" bigint DEFAULT nextval('seq_companies_catalogues_uid') NOT NULL,
    "company_uid" bigint,
    "catalogue_uid" bigint,
    "hierarchy_uniq" character varying(32),
    "path" character varying(256),
    CONSTRAINT "pkey_companies_catalogues_uid" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_companies_members";
DROP SEQUENCE IF EXISTS seq_companies_members_uid;
CREATE SEQUENCE seq_companies_members_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_companies_members" (
    "company_uid" bigint,
    "member_uniq" character varying(7),
    "uid" bigint DEFAULT nextval('seq_companies_members_uid') NOT NULL,
    "branch_uid" bigint,
    "is_default" integer,
    "is_owner" integer,
    "is_admin" integer,
    CONSTRAINT "pkey_companies_members_uid" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_companies_store";
DROP SEQUENCE IF EXISTS seq_global_store_uid;
CREATE SEQUENCE seq_global_store_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_companies_store" (
    "uid" bigint DEFAULT nextval('seq_global_store_uid') NOT NULL,
    "company_uid" bigint,
    "group_uniq" character varying(32),
    "article" character varying(32),
    "currency" character varying(3),
    "calculation" integer,
    "units" integer,
    "price" double precision,
    "available" double precision,
    "date_modified" bigint,
    CONSTRAINT "pkey_companies_store_uid" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_contact_info";
DROP SEQUENCE IF EXISTS seq_contact_info_uid;
CREATE SEQUENCE seq_contact_info_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_contact_info" (
    "uid" integer DEFAULT nextval('seq_contact_info_uid'),
    "ref_type" character varying(32),
    "ref_uid" integer,
    "type" character varying(16),
    "value" character varying(32)
) WITH (oids = false);


DROP TABLE IF EXISTS "_members";
CREATE TABLE "public"."_members" (
    "uniq" character varying(7) NOT NULL,
    "email" character varying(255) NOT NULL,
    "password_salt" character varying(64) NOT NULL,
    "password" character varying(64),
    "access_group" integer,
    "activation_key" character varying(32),
    "date_activate" bigint,
    "currency_uniq" character varying(255),
    CONSTRAINT "_members_pkey" PRIMARY KEY ("uniq")
) WITH (oids = false);


DROP TABLE IF EXISTS "_members_catalogues";
DROP SEQUENCE IF EXISTS seq_members_catalogues_uid;
CREATE SEQUENCE seq_members_catalogues_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_members_catalogues" (
    "uid" integer DEFAULT nextval('seq_members_catalogues_uid') NOT NULL,
    "hierarchy_uniq" character varying(32),
    "member_uniq" character varying(7),
    "catalogue_uid" bigint,
    "path" character varying(256),
    CONSTRAINT "pkey_members_catalogues_uid" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_members_devices";
DROP SEQUENCE IF EXISTS seq_members_devices_uid;
CREATE SEQUENCE seq_members_devices_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_members_devices" (
    "device_uniq" character varying(40) NOT NULL,
    "member_uniq" character varying(7),
    "ip_address" character varying(16),
    "device_name" character varying(32),
    "date_registered" bigint,
    "date_updated" bigint,
    "corporate" integer,
    "uid" integer DEFAULT nextval('seq_members_devices_uid'),
    CONSTRAINT "devices_binds_pkey" PRIMARY KEY ("device_uniq")
) WITH (oids = false);

CREATE INDEX "devices_binds_registered" ON "public"."_members_devices" USING btree ("date_registered");

CREATE INDEX "devices_binds_uniq" ON "public"."_members_devices" USING btree ("device_uniq");

CREATE INDEX "devices_binds_user" ON "public"."_members_devices" USING btree ("member_uniq");


DROP TABLE IF EXISTS "_members_profiles";
CREATE TABLE "public"."_members_profiles" (
    "member_uniq" character varying(7) NOT NULL,
    "first_name" character varying(32),
    "last_name" character varying(32),
    "registered" bigint,
    "last_logged_in" bigint,
    CONSTRAINT "_profiles_pkey" PRIMARY KEY ("member_uniq")
) WITH (oids = false);


DROP TABLE IF EXISTS "_members_sessions";
DROP SEQUENCE IF EXISTS seq_members_sessions_uid;
CREATE SEQUENCE seq_members_sessions_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_members_sessions" (
    "member_uniq" character varying(7) NOT NULL,
    "session_uniq" character varying(64),
    "date_expires" bigint,
    "uid" integer DEFAULT nextval('seq_members_sessions_uid'),
    CONSTRAINT "user_session_pkey" PRIMARY KEY ("member_uniq")
) WITH (oids = false);


DROP TABLE IF EXISTS "_members_settings";
DROP SEQUENCE IF EXISTS seq_members_settings_uid;
CREATE SEQUENCE seq_members_settings_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_members_settings" (
    "member_uniq" character varying(7),
    "name" character varying(64),
    "value" character varying(64),
    "owner" character varying(7),
    "uid" bigint DEFAULT nextval('seq_members_settings_uid') NOT NULL,
    CONSTRAINT "pkey_settings_uid" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_members_store";
DROP SEQUENCE IF EXISTS seq_global_store_uid;
CREATE SEQUENCE seq_global_store_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_members_store" (
    "member_uniq" character(7),
    "group_uniq" character varying(32),
    "currency" character varying(3),
    "units" integer,
    "price" double precision,
    "available" double precision,
    "date_modified" bigint,
    "article" character varying(32),
    "calculation" integer,
    "uid" bigint DEFAULT nextval('seq_global_store_uid') NOT NULL,
    CONSTRAINT "pkey_members_store_uid" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_projects";
CREATE TABLE "public"."_projects" (
    "global_uniq" character varying(64) NOT NULL,
    "project_name" character varying(64),
    "project_uniq" character varying(64),
    "project_version" character varying(32),
    "project_type" integer,
    "customer_uniq" character varying(64),
    "creator_type" integer,
    "creator_uniq" character varying(64),
    "creator_name" character varying(64),
    "company_uid" bigint,
    "branch_uid" bigint,
    "destination_fields" character varying(256),
    "date_created" bigint,
    "date_modified" bigint,
    CONSTRAINT "atlas_project_pkey" PRIMARY KEY ("global_uniq")
) WITH (oids = false);

CREATE INDEX "atlas_project_branch_uniq" ON "public"."_projects" USING btree ("branch_uid");

CREATE INDEX "atlas_project_creator_name" ON "public"."_projects" USING btree ("creator_name");

CREATE INDEX "atlas_project_creator_type" ON "public"."_projects" USING btree ("creator_type");

CREATE INDEX "atlas_project_creator_uniq" ON "public"."_projects" USING btree ("creator_uniq");

CREATE INDEX "atlas_project_customer_uniq" ON "public"."_projects" USING btree ("customer_uniq");

CREATE INDEX "atlas_project_date_created" ON "public"."_projects" USING btree ("date_created");

CREATE INDEX "atlas_project_date_modified" ON "public"."_projects" USING btree ("date_modified");

CREATE INDEX "atlas_project_destination_fields" ON "public"."_projects" USING btree ("destination_fields");

CREATE INDEX "atlas_project_global_uniq" ON "public"."_projects" USING btree ("global_uniq");

CREATE INDEX "atlas_project_organization_uniq" ON "public"."_projects" USING btree ("company_uid");

CREATE INDEX "atlas_project_project_name" ON "public"."_projects" USING btree ("project_name");

CREATE INDEX "atlas_project_project_type" ON "public"."_projects" USING btree ("project_type");

CREATE INDEX "atlas_project_project_uniq" ON "public"."_projects" USING btree ("project_uniq");

CREATE INDEX "atlas_project_project_version" ON "public"."_projects" USING btree ("project_version");


DROP TABLE IF EXISTS "_projects_info";
CREATE TABLE "public"."_projects_info" (
    "project_uniq" character varying(64) NOT NULL,
    "area" double precision,
    "rnjb_uniq" character varying(64),
    "rnjb_state" integer,
    "webgl_uniq" character varying(64),
    "webgl_shortlink" character varying(64),
    "webgl_email" character varying(64),
    "time_elapsed" double precision,
    "date_rendered" bigint,
    CONSTRAINT "atlas_project_info_pkey" PRIMARY KEY ("project_uniq")
) WITH (oids = false);

CREATE INDEX "atlas_project_info_project_uniq" ON "public"."_projects_info" USING btree ("project_uniq");


DROP TABLE IF EXISTS "_projects_renders";
CREATE TABLE "public"."_projects_renders" (
    "global_uniq" character varying(64) NOT NULL,
    "render_job_uniq" character varying(64),
    "render_job_type" integer,
    "render_quality" integer,
    "project_uniq" character varying(64),
    "company_uid" bigint,
    "branch_uid" bigint,
    "project_version" character varying(64),
    "sender_type" integer,
    "sender_uniq" character varying(64),
    "sender_name" character varying(64),
    "webgl_uniq" character varying(64),
    "webgl_shortlink" character varying(64),
    "date_created" bigint,
    "date_finished" bigint,
    "date_canceled" bigint,
    "views_count" integer,
    "date_viewed" bigint,
    CONSTRAINT "atlas_project_render_pkey" PRIMARY KEY ("global_uniq")
) WITH (oids = false);

CREATE INDEX "atlas_project_render_branch_uniq" ON "public"."_projects_renders" USING btree ("branch_uid");

CREATE INDEX "atlas_project_render_date_canceled" ON "public"."_projects_renders" USING btree ("date_canceled");

CREATE INDEX "atlas_project_render_date_created" ON "public"."_projects_renders" USING btree ("date_created");

CREATE INDEX "atlas_project_render_date_finished" ON "public"."_projects_renders" USING btree ("date_finished");

CREATE INDEX "atlas_project_render_date_viewed" ON "public"."_projects_renders" USING btree ("date_viewed");

CREATE INDEX "atlas_project_render_global_uniq" ON "public"."_projects_renders" USING btree ("global_uniq");

CREATE INDEX "atlas_project_render_organization_uniq" ON "public"."_projects_renders" USING btree ("company_uid");

CREATE INDEX "atlas_project_render_project_uniq" ON "public"."_projects_renders" USING btree ("project_uniq");

CREATE INDEX "atlas_project_render_project_version" ON "public"."_projects_renders" USING btree ("project_version");

CREATE INDEX "atlas_project_render_render_job_type" ON "public"."_projects_renders" USING btree ("render_job_type");

CREATE INDEX "atlas_project_render_render_job_uniq" ON "public"."_projects_renders" USING btree ("render_job_uniq");

CREATE INDEX "atlas_project_render_render_quality" ON "public"."_projects_renders" USING btree ("render_quality");

CREATE INDEX "atlas_project_render_sender_name" ON "public"."_projects_renders" USING btree ("sender_name");

CREATE INDEX "atlas_project_render_sender_type" ON "public"."_projects_renders" USING btree ("sender_type");

CREATE INDEX "atlas_project_render_sender_uniq" ON "public"."_projects_renders" USING btree ("sender_uniq");

CREATE INDEX "atlas_project_render_views_count" ON "public"."_projects_renders" USING btree ("views_count");

CREATE INDEX "atlas_project_render_webgl_shortlink" ON "public"."_projects_renders" USING btree ("webgl_shortlink");

CREATE INDEX "atlas_project_render_webgl_uniq" ON "public"."_projects_renders" USING btree ("webgl_uniq");


DROP TABLE IF EXISTS "_projects_usage";
CREATE TABLE "public"."_projects_usage" (
    "global_uniq" character varying,
    "company_uid" bigint,
    "branch_uid" bigint,
    "project_uniq" character varying,
    "usage_type" integer,
    "usage_time" double precision,
    "trigger_action" character varying,
    "creator_type" integer,
    "creator_uniq" character varying(64),
    "creator_name" character varying(64),
    "date_created" bigint
) WITH (oids = false);

CREATE INDEX "atlas_project_usage_branch_uniq" ON "public"."_projects_usage" USING btree ("branch_uid");

CREATE INDEX "atlas_project_usage_creator_name" ON "public"."_projects_usage" USING btree ("creator_name");

CREATE INDEX "atlas_project_usage_creator_type" ON "public"."_projects_usage" USING btree ("creator_type");

CREATE INDEX "atlas_project_usage_creator_uniq" ON "public"."_projects_usage" USING btree ("creator_uniq");

CREATE INDEX "atlas_project_usage_date_created" ON "public"."_projects_usage" USING btree ("date_created");

CREATE INDEX "atlas_project_usage_global_uniq" ON "public"."_projects_usage" USING btree ("global_uniq");

CREATE INDEX "atlas_project_usage_organization_uniq" ON "public"."_projects_usage" USING btree ("company_uid");

CREATE INDEX "atlas_project_usage_project_uniq" ON "public"."_projects_usage" USING btree ("project_uniq");

CREATE INDEX "atlas_project_usage_trigger_action" ON "public"."_projects_usage" USING btree ("trigger_action");

CREATE INDEX "atlas_project_usage_usage_time" ON "public"."_projects_usage" USING btree ("usage_time");

CREATE INDEX "atlas_project_usage_usage_type" ON "public"."_projects_usage" USING btree ("usage_type");


DROP TABLE IF EXISTS "_projects_versions";
CREATE TABLE "public"."_projects_versions" (
    "global_uniq" character varying(64) NOT NULL,
    "project_uniq" character varying(64),
    "version_uniq" character varying(32),
    "software_version" integer,
    "creator_type" integer,
    "creator_uniq" character varying(64),
    "creator_name" character varying(64),
    "sync_status" integer,
    "company_uid" bigint,
    "branch_uid" bigint,
    "date_created" bigint,
    CONSTRAINT "atlas_project_version_pkey" PRIMARY KEY ("global_uniq")
) WITH (oids = false);

CREATE INDEX "atlas_project_version_creator_name" ON "public"."_projects_versions" USING btree ("creator_name");

CREATE INDEX "atlas_project_version_creator_type" ON "public"."_projects_versions" USING btree ("creator_type");

CREATE INDEX "atlas_project_version_creator_uniq" ON "public"."_projects_versions" USING btree ("creator_uniq");

CREATE INDEX "atlas_project_version_global_uniq" ON "public"."_projects_versions" USING btree ("global_uniq");

CREATE INDEX "atlas_project_version_project_uniq" ON "public"."_projects_versions" USING btree ("project_uniq");

CREATE INDEX "atlas_project_version_software_version" ON "public"."_projects_versions" USING btree ("software_version");

CREATE INDEX "atlas_project_version_sync_status" ON "public"."_projects_versions" USING btree ("sync_status");

CREATE INDEX "atlas_project_version_version_uniq" ON "public"."_projects_versions" USING btree ("version_uniq");


DROP TABLE IF EXISTS "_rights_access";
DROP SEQUENCE IF EXISTS seq_rights_access_uid;
CREATE SEQUENCE seq_rights_access_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_rights_access" (
    "uid" bigint DEFAULT nextval('seq_rights_access_uid') NOT NULL,
    "owner" character varying(7),
    "permission_uid" bigint,
    "role_uid" bigint,
    "member_uniq" character varying(7),
    "action" character varying(8),
    CONSTRAINT "pkey_access_uniq" PRIMARY KEY ("uid")
) WITH (oids = false);

CREATE INDEX "idx_access_owner" ON "public"."_rights_access" USING btree ("owner");

CREATE INDEX "idx_access_permission" ON "public"."_rights_access" USING btree ("permission_uid");

CREATE INDEX "idx_access_role" ON "public"."_rights_access" USING btree ("role_uid");


DROP TABLE IF EXISTS "_rights_permissions";
DROP SEQUENCE IF EXISTS seq_rights_permissions_uid;
CREATE SEQUENCE seq_rights_permissions_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_rights_permissions" (
    "uid" bigint DEFAULT nextval('seq_rights_permissions_uid') NOT NULL,
    "name" character varying(64),
    "code" character varying(32),
    "date_deleted" bigint,
    CONSTRAINT "_permissions_pkey" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_rights_roles";
DROP SEQUENCE IF EXISTS seq_rights_roles_uid;
CREATE SEQUENCE seq_rights_roles_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_rights_roles" (
    "uid" bigint DEFAULT nextval('seq_rights_roles_uid') NOT NULL,
    "parent_uid" bigint,
    "owner" character varying(7),
    "name" character varying(64),
    CONSTRAINT "_groups_pkey" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_subscribes";
DROP SEQUENCE IF EXISTS seq_subscribes_uid;
CREATE SEQUENCE seq_subscribes_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_subscribes" (
    "uid" bigint DEFAULT nextval('seq_subscribes_uid') NOT NULL,
    "name" character varying(64),
    "status" character varying(8),
    CONSTRAINT "pkey_subscribes_uid" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_subscribes_members";
DROP SEQUENCE IF EXISTS seq_subscribes_members_uid;
CREATE SEQUENCE seq_subscribes_members_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_subscribes_members" (
    "uid" bigint DEFAULT nextval('seq_subscribes_members_uid') NOT NULL,
    "member_uniq" character varying(7),
    "subscribe_uid" bigint,
    "date_started" bigint,
    "date_expires" bigint,
    "grantor_uniq" character varying(7),
    "payment_uid" bigint,
    CONSTRAINT "pkey_subscribes_members_uid" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_subscribes_options";
DROP SEQUENCE IF EXISTS seq_subscribes_options_uid;
CREATE SEQUENCE seq_subscribes_options_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_subscribes_options" (
    "uid" bigint DEFAULT nextval('seq_subscribes_options_uid'),
    "subscribe_uid" bigint,
    "code" character varying(32),
    "name" character varying(32),
    "limitation" character varying(53),
    "refresh_period" bigint
) WITH (oids = false);


DROP TABLE IF EXISTS "_subscribes_prices";
DROP SEQUENCE IF EXISTS seq_subscribes_prices_uid;
CREATE SEQUENCE seq_subscribes_prices_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_subscribes_prices" (
    "uid" bigint DEFAULT nextval('seq_subscribes_prices_uid'),
    "subscribe_uid" bigint,
    "currency" character varying(8),
    "value" double precision
) WITH (oids = false);


DROP TABLE IF EXISTS "_subscribes_usage";
DROP SEQUENCE IF EXISTS seq_subscribes_usage_uid;
CREATE SEQUENCE seq_subscribes_usage_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_subscribes_usage" (
    "uid" bigint DEFAULT nextval('seq_subscribes_usage_uid'),
    "option_uid" bigint,
    "member_uniq" character varying(7),
    "amount" double precision,
    "date_refreshed" bigint
) WITH (oids = false);


DROP TABLE IF EXISTS "_tokens";
DROP SEQUENCE IF EXISTS seq_tokens_uid;
CREATE SEQUENCE seq_tokens_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_tokens" (
    "value" character varying(64) NOT NULL,
    "type" character varying(16),
    "uid" bigint DEFAULT nextval('seq_tokens_uid'),
    "date_deleted" bigint,
    "description" character varying(128),
    CONSTRAINT "atlas_webgl_config_pkey" PRIMARY KEY ("value")
) WITH (oids = false);


DROP TABLE IF EXISTS "_tokens_catalogues";
DROP SEQUENCE IF EXISTS seq_tokens_catalogues_uid;
CREATE SEQUENCE seq_tokens_catalogues_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_tokens_catalogues" (
    "uid" bigint DEFAULT nextval('seq_tokens_catalogues_uid') NOT NULL,
    "token_uid" bigint,
    "catalogue_uid" bigint,
    "hierarchy_uniq" character varying(32),
    "path" character varying(256),
    CONSTRAINT "pkey_tokens_catalogues_uid" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_tokens_companies";
DROP SEQUENCE IF EXISTS seq_tokens_companies_uid;
CREATE SEQUENCE seq_tokens_companies_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_tokens_companies" (
    "uid" bigint DEFAULT nextval('seq_tokens_companies_uid') NOT NULL,
    "token_uid" bigint,
    "company_uid" bigint,
    CONSTRAINT "_tokens_companies_pkey" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "_tokens_variables";
DROP SEQUENCE IF EXISTS seq_tokens_variables_uid;
CREATE SEQUENCE seq_tokens_variables_uid INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."_tokens_variables" (
    "uid" bigint DEFAULT nextval('seq_tokens_variables_uid') NOT NULL,
    "token_uid" bigint,
    "name" character varying(32),
    "value" character varying(32),
    "owner" character varying(7),
    CONSTRAINT "_tokens_settings_pkey" PRIMARY KEY ("uid")
) WITH (oids = false);


DROP TABLE IF EXISTS "atlas_api";
CREATE TABLE "public"."atlas_api" (
    "organization_uniq" integer,
    "api_key" character varying(64)
) WITH (oids = false);


DROP TABLE IF EXISTS "atlas_webgl_data_catalogues";
CREATE TABLE "public"."atlas_webgl_data_catalogues" (
    "webgl_key" character varying(64),
    "catalogue_uniq" character varying(64)
) WITH (oids = false);

CREATE INDEX "idx_atlas_webgl_data_catalogues_webgl_key" ON "public"."atlas_webgl_data_catalogues" USING btree ("webgl_key");


DROP TABLE IF EXISTS "data_catalogues";
CREATE TABLE "public"."data_catalogues" (
    "catalogue_uniq" character varying(64),
    "user_uniq" character varying(7),
    "access" character varying(16)
) WITH (oids = false);


DROP TABLE IF EXISTS "migrations";
DROP SEQUENCE IF EXISTS migrations_id_seq;
CREATE SEQUENCE migrations_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 1 CACHE 1;

CREATE TABLE "public"."migrations" (
    "id" integer DEFAULT nextval('migrations_id_seq') NOT NULL,
    "migration" character varying(255) NOT NULL,
    "batch" integer NOT NULL,
    CONSTRAINT "migrations_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "permissions";
CREATE TABLE "public"."permissions" (
    "uniq" character varying(7) NOT NULL,
    "delegate" character varying(16) NOT NULL,
    "permission" character varying(32) NOT NULL
) WITH (oids = false);


DROP TABLE IF EXISTS "promocodes";
CREATE TABLE "public"."promocodes" (
    "uniq" character varying(7) NOT NULL,
    "organization_uniq" integer,
    "permnissions" character varying(200),
    CONSTRAINT "promocodes_pkey" PRIMARY KEY ("uniq")
) WITH (oids = false);


DROP TABLE IF EXISTS "server_log";
CREATE TABLE "public"."server_log" (
    "uniq" integer NOT NULL,
    "level" integer,
    "message" character varying(8192),
    "timestamp" bigint,
    CONSTRAINT "server_log_pkey" PRIMARY KEY ("uniq")
) WITH (oids = false);


DROP TABLE IF EXISTS "server_render";
CREATE TABLE "public"."server_render" (
    "uniq" integer NOT NULL,
    "member" character varying(7),
    "job" character varying(16),
    "rgp_amount" integer,
    "capacity" integer,
    "accepted" bigint,
    "started" bigint,
    "finished" bigint,
    "canceled" bigint,
    CONSTRAINT "server_render_pkey" PRIMARY KEY ("uniq")
) WITH (oids = false);


DROP TABLE IF EXISTS "server_state";
CREATE TABLE "public"."server_state" (
    "uniq" character varying(32),
    "rnjb_state" character varying(16),
    "wljb_state" character varying(16),
    "last_ping" bigint,
    CONSTRAINT "server_state_uniq_key" UNIQUE ("uniq")
) WITH (oids = false);


DROP TABLE IF EXISTS "webgl_projects";
CREATE TABLE "public"."webgl_projects" (
    "uniq" character varying(7) NOT NULL,
    "owner" character varying(7),
    "create" bigint,
    "update" bigint,
    CONSTRAINT "webgl_projects_pkey" PRIMARY KEY ("uniq")
) WITH (oids = false);


-- 2020-09-06 17:18:16.944359+00
