DROP TABLE IF EXISTS "user";
CREATE TABLE user (id_user INTEGER PRIMARY KEY, username TEXT, email TEXT, dn TEXT);

DROP TABLE IF EXISTS "user_notify";
CREATE TABLE user_notify (id_user NUMERIC, id_vm NUMERIC);

DROP TABLE IF EXISTS "vm";
CREATE TABLE "vm" ("created_by" NUMERIC, "type" NUMERIC, "name" TEXT, "comment" TEXT,"delete_dt" datetime,"update_dt" datetime,"id_vm" INTEGER PRIMARY KEY ,"status" NUMERIC,"create_dt" Datetime,"id_integ" INTEGER,"expired_dt" DATETIME DEFAULT (null) );