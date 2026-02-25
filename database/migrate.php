<?php

/**
 * Database Migration Runner
 * Thomson Membership Dashboard
 *
 * Run via CLI:
 *   php migrate.php
 *
 * Options presented interactively:
 *   1) Fresh Install  – drop & recreate all tables, then run seeder
 *   2) Alter Table    – apply ADD COLUMN / MODIFY COLUMN changes to existing tables
 *   3) Seed Only      – insert / upsert seed data without touching table structure
 */

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------
define('MIGRATION_RUNNER', true);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("Migration must be run from the command line.\n");
}

// ---------------------------------------------------------------------------
// Read credentials from config WITHOUT triggering its live mysqli connection
// (config.inc.php calls die() on connect failure, so we parse it manually)
// ---------------------------------------------------------------------------
$_configSrc = file_get_contents(__DIR__ . '/../config/config.inc.php');

function _readConfigVar(string $src, string $var): string
{
    // Matches both double-quoted and single-quoted values, including empty strings
    if (preg_match('/^\s*\$' . preg_quote($var, '/') . '\s*=\s*"([^"]*)"\s*;/m', $src, $m)) {
        return $m[1];
    }
    if (preg_match("/^\s*\\\${$var}\s*=\s*'([^']*)'\s*;/m", $src, $m)) {
        return $m[1];
    }
    return '';
}

$_srv  = _readConfigVar($_configSrc, 'servername');
$_user = _readConfigVar($_configSrc, 'username');
$_pass = _readConfigVar($_configSrc, 'password');
$_db   = _readConfigVar($_configSrc, 'database');

if (empty($_srv) || empty($_db)) {
    exit("[Migration][ERROR] Could not parse database credentials from config.inc.php.\n");
}

// ---------------------------------------------------------------------------
// Step 1 – Connect to MySQL SERVER only (no database selected yet)
// ---------------------------------------------------------------------------
$conn = @new mysqli($_srv, $_user, $_pass);
if ($conn->connect_error) {
    exit("[Migration][ERROR] Cannot connect to MySQL server ({$_srv}): " . $conn->connect_error . "\n");
}
echo "[Migration] Connected to MySQL server ({$_srv}).\n";

// ---------------------------------------------------------------------------
// Step 2 – Check whether the target database exists; offer to create it
// ---------------------------------------------------------------------------
$_res = $conn->query(
    "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA
     WHERE SCHEMA_NAME = '" . $conn->real_escape_string($_db) . "'"
);

if (!$_res || $_res->num_rows === 0) {
    echo "\n[Migration] Database `{$_db}` does not exist.\n";
    echo "  Would you like to create it now? [yes/no]: ";
    $_ans = trim(fgets(STDIN));

    if (strtolower($_ans) !== 'yes') {
        echo "[Migration] Aborted. No changes were made.\n";
        $conn->close();
        exit(0);
    }

    if (!$conn->query("CREATE DATABASE `{$_db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
        exit("[Migration][ERROR] Failed to create database: " . $conn->error . "\n");
    }
    echo "[Migration] Database `{$_db}` created successfully.\n";
} else {
    echo "[Migration] Database `{$_db}` found.\n";
}
$_res && $_res->free();

// ---------------------------------------------------------------------------
// Step 3 – Select the database
// ---------------------------------------------------------------------------
if (!$conn->select_db($_db)) {
    exit("[Migration][ERROR] Cannot select database `{$_db}`: " . $conn->error . "\n");
}

// ---------------------------------------------------------------------------
// Menu
// ---------------------------------------------------------------------------
echo "\n";
echo "=============================================================\n";
echo "  Thomson Membership Dashboard – Database Migration Runner   \n";
echo "=============================================================\n";
echo "\n";
echo "  Select an option:\n";
echo "    [1] Fresh Install  (DROP + CREATE all tables, then seed)\n";
echo "    [2] Alter Table Only  (ADD / MODIFY columns on live DB)\n";
echo "    [3] Seed Only         (Upsert seed data, no schema change)\n";
echo "\n";
echo "  Enter choice [1/2/3]: ";

$choice = trim(fgets(STDIN));

switch ($choice) {
    case '1':
        freshInstall($conn);
        break;
    case '2':
        alterTables($conn);
        break;
    case '3':
        seedOnly($conn);
        break;
    default:
        echo "[Migration] Invalid choice. Exiting.\n";
        exit(1);
}

$conn->close();
echo "[Migration] Done.\n";

// ===========================================================================
// Option 1 – Fresh Install
// ===========================================================================
function freshInstall(mysqli $conn): void
{
    echo "\n[Migration] Option 1: Fresh Install\n";
    echo "  WARNING: This will DROP all existing tables and their data! Please backup your data before proceeding.\n";
    echo "  Type 'yes' to confirm: ";
    $confirm = trim(fgets(STDIN));

    if (strtolower($confirm) !== 'yes') {
        echo "[Migration] Aborted.\n";
        exit(0);
    }

    echo "[Migration] Disabling foreign key checks...\n";
    execSQL($conn, "SET FOREIGN_KEY_CHECKS = 0");

    // Drop tables in reverse dependency order
    $drops = ['child', 'otp', 'id_generate', 'pat_tgp', 'pat_tkc', 'user'];
    foreach ($drops as $table) {
        echo "[Migration] Dropping table `{$table}`...\n";
        execSQL($conn, "DROP TABLE IF EXISTS `{$table}`");
    }

    echo "[Migration] Re-enabling foreign key checks...\n";
    execSQL($conn, "SET FOREIGN_KEY_CHECKS = 1");

    createTables($conn);

    echo "[Migration] Running seeder...\n";
    require_once __DIR__ . '/seeder.php';
    runSeeder($conn);
}

// ===========================================================================
// Option 2 – Alter Table Only
// ===========================================================================
function alterTables(mysqli $conn): void
{
    echo "\n[Migration] Option 2: Alter Table Only\n";

    // -------------------------------------------------------------------------
    // Helpers – only add a column when it does not already exist
    // -------------------------------------------------------------------------
    $addColumnIfMissing = function (
        mysqli $conn,
        string $table,
        string $column,
        string $definition,
        string $after = ''
    ) use (&$addColumnIfMissing): void {
        $check = $conn->query(
            "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME   = '{$table}'
               AND COLUMN_NAME  = '{$column}'"
        );
        if ($check && $check->num_rows > 0) {
            echo "[Migration] Column `{$table}`.`{$column}` already exists – skipped.\n";
            return;
        }
        $afterClause = $after ? " AFTER `{$after}`" : '';
        execSQL($conn, "ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}{$afterClause}");
        echo "[Migration] Added column `{$table}`.`{$column}`.\n";
    };

    $modifyColumnIfExists = function (
        mysqli $conn,
        string $table,
        string $column,
        string $newDefinition
    ): void {
        $check = $conn->query(
            "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME   = '{$table}'
               AND COLUMN_NAME  = '{$column}'"
        );
        if (!$check || $check->num_rows === 0) {
            echo "[Migration] Column `{$table}`.`{$column}` not found – skipped.\n";
            return;
        }
        execSQL($conn, "ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` {$newDefinition}");
        echo "[Migration] Modified column `{$table}`.`{$column}`.\n";
    };

    // -------------------------------------------------------------------------
    // pat_tgp – ensure all expected columns exist
    // -------------------------------------------------------------------------
    $addColumnIfMissing($conn, 'pat_tgp', 'marketing_consent', "varchar(5) DEFAULT NULL", 'pat_memb');

    // -------------------------------------------------------------------------
    // pat_tkc – ensure all expected columns exist
    // -------------------------------------------------------------------------
    $addColumnIfMissing($conn, 'pat_tkc', 'marketing_consent', "varchar(5) DEFAULT NULL", 'pat_memb');

    // -------------------------------------------------------------------------
    // user – session / security columns
    // -------------------------------------------------------------------------
    $addColumnIfMissing($conn, 'user', 'session_id',      "varchar(255) DEFAULT NULL",   'last_login');
    $addColumnIfMissing($conn, 'user', 'ip_address',      "varchar(45) NOT NULL DEFAULT ''", 'session_id');
    $addColumnIfMissing($conn, 'user', 'user_agent',      "text NOT NULL",               'ip_address');
    $addColumnIfMissing($conn, 'user', 'failed_attempts', "int(10) DEFAULT NULL",        'user_agent');
    $addColumnIfMissing($conn, 'user', 'lockout_time',    "timestamp NULL DEFAULT NULL", 'failed_attempts');
    $addColumnIfMissing($conn, 'user', 'initial',         "enum('1','0') NOT NULL DEFAULT '1'", 'lockout_time');

    // -------------------------------------------------------------------------
    // child – born column
    // -------------------------------------------------------------------------
    $addColumnIfMissing($conn, 'child', 'child_born', "varchar(100) DEFAULT NULL", 'child_dob');

    // -------------------------------------------------------------------------
    // Unique key guards
    // -------------------------------------------------------------------------
    addUniqueKeyIfMissing($conn, 'user', 'staff_email',  '`username`');
    addUniqueKeyIfMissing($conn, 'user', 'session_id',   '`session_id`');

    echo "[Migration] Alter Table complete.\n";
}

// ===========================================================================
// Option 3 – Seed Only
// ===========================================================================
function seedOnly(mysqli $conn): void
{
    echo "\n[Migration] Option 3: Seed Only\n";
    require_once __DIR__ . '/seeder.php';
    runSeeder($conn);
}

// ===========================================================================
// Schema – CREATE TABLE statements
// ===========================================================================
function createTables(mysqli $conn): void
{
    echo "[Migration] Creating tables...\n";

    // ---- child ----
    execSQL($conn, "
        CREATE TABLE `child` (
            `child_id`           varchar(255) NOT NULL,
            `pat_id`             varchar(255) NOT NULL,
            `child_mrn`          varchar(50)  DEFAULT NULL,
            `child_name`         varchar(50)  NOT NULL,
            `child_gen`          varchar(10)  NOT NULL,
            `child_age`          varchar(50)  NOT NULL,
            `child_dob`          varchar(10)  NOT NULL,
            `child_born`         varchar(100) DEFAULT NULL,
            `child_status`       varchar(30)  NOT NULL,
            `child_timestamp`    timestamp    NOT NULL DEFAULT current_timestamp(),
            `user_update`        varchar(30)  DEFAULT NULL,
            `last_update_child`  timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`child_id`),
            KEY `parents_id` (`pat_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "[Migration]   + table `child` created.\n";

    // ---- id_generate ----
    execSQL($conn, "
        CREATE TABLE `id_generate` (
            `id`             int(10)      NOT NULL AUTO_INCREMENT,
            `running_number` varchar(255) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "[Migration]   + table `id_generate` created.\n";

    // ---- otp ----
    execSQL($conn, "
        CREATE TABLE `otp` (
            `otp_id` int(10)     NOT NULL AUTO_INCREMENT,
            `id`     varchar(50) NOT NULL,
            `email`  text        NOT NULL,
            `otp`    varchar(6)  NOT NULL,
            `expiry` datetime    NOT NULL,
            PRIMARY KEY (`otp_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "[Migration]   + table `otp` created.\n";

    // ---- pat_tgp ----
    execSQL($conn, "
        CREATE TABLE `pat_tgp` (
            `pat_id`             varchar(255) NOT NULL,
            `pat_mrn`            varchar(30)  DEFAULT NULL,
            `pat_title`          varchar(10)  NOT NULL,
            `pat_name`           varchar(40)  NOT NULL,
            `pat_nric`           varchar(30)  NOT NULL,
            `pat_nat`            varchar(20)  NOT NULL,
            `pat_dob`            date         NOT NULL,
            `pat_race`           varchar(10)  NOT NULL,
            `pat_phone`          varchar(20)  NOT NULL,
            `pat_gender`         varchar(10)  NOT NULL,
            `pat_addr`           varchar(200) NOT NULL,
            `pat_state`          varchar(50)  NOT NULL,
            `pat_postcode`       varchar(50)  NOT NULL,
            `pat_age`            varchar(11)  NOT NULL,
            `pat_city`           varchar(20)  NOT NULL,
            `pat_email`          varchar(50)  NOT NULL,
            `pat_register`       timestamp    NOT NULL DEFAULT current_timestamp(),
            `last_update`        timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `user_update`        varchar(30)  DEFAULT NULL,
            `status`             varchar(50)  NOT NULL,
            `pat_memb`           varchar(30)  NOT NULL,
            `marketing_consent`  varchar(5)   DEFAULT NULL,
            PRIMARY KEY (`pat_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "[Migration]   + table `pat_tgp` created.\n";

    // ---- pat_tkc ----
    execSQL($conn, "
        CREATE TABLE `pat_tkc` (
            `pat_id`             varchar(255) NOT NULL,
            `pat_mrn`            varchar(30)  DEFAULT NULL,
            `pat_title`          varchar(10)  NOT NULL,
            `pat_name`           varchar(40)  NOT NULL,
            `pat_nric`           varchar(30)  NOT NULL,
            `pat_nat`            varchar(100) NOT NULL,
            `pat_dob`            date         NOT NULL,
            `pat_race`           varchar(100) NOT NULL,
            `pat_phone`          varchar(20)  NOT NULL,
            `pat_gender`         varchar(10)  NOT NULL,
            `pat_addr`           varchar(200) NOT NULL,
            `pat_state`          varchar(50)  NOT NULL,
            `pat_postcode`       varchar(50)  NOT NULL,
            `pat_age`            varchar(11)  NOT NULL,
            `pat_city`           varchar(20)  NOT NULL,
            `pat_email`          varchar(50)  NOT NULL,
            `pat_register`       timestamp    NOT NULL DEFAULT current_timestamp(),
            `last_update`        timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `user_update`        varchar(30)  DEFAULT NULL,
            `status`             varchar(50)  NOT NULL,
            `pat_memb`           varchar(30)  NOT NULL,
            `marketing_consent`  varchar(5)   DEFAULT NULL,
            PRIMARY KEY (`pat_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "[Migration]   + table `pat_tkc` created.\n";

    // ---- user ----
    execSQL($conn, "
        CREATE TABLE `user` (
            `userid`          int(11)       NOT NULL AUTO_INCREMENT,
            `name`            varchar(100)  NOT NULL,
            `username`        varchar(50)   NOT NULL,
            `department`      varchar(20)   NOT NULL,
            `datecreate`      varchar(20)   NOT NULL,
            `status`          varchar(10)   NOT NULL,
            `role`            varchar(5)    NOT NULL,
            `password`        varchar(255)  NOT NULL,
            `last_login`      timestamp     NOT NULL DEFAULT current_timestamp(),
            `session_id`      varchar(255)  DEFAULT NULL,
            `ip_address`      varchar(45)   NOT NULL,
            `user_agent`      text          NOT NULL,
            `failed_attempts` int(10)       DEFAULT NULL,
            `lockout_time`    timestamp     NULL DEFAULT NULL,
            `initial`         enum('1','0') NOT NULL DEFAULT '1',
            PRIMARY KEY (`userid`),
            UNIQUE KEY `staff_email` (`username`),
            UNIQUE KEY `session_id` (`session_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "[Migration]   + table `user` created.\n";

    // ---- foreign key: child -> pat_tkc ----
    execSQL($conn, "
        ALTER TABLE `child`
            ADD CONSTRAINT `child_ibfk_1`
            FOREIGN KEY (`pat_id`) REFERENCES `pat_tkc` (`pat_id`)
            ON DELETE CASCADE ON UPDATE CASCADE
    ");
    echo "[Migration]   + foreign key `child`.`pat_id` -> `pat_tkc`.`pat_id` added.\n";
}

// ===========================================================================
// Helpers
// ===========================================================================
function execSQL(mysqli $conn, string $sql): void
{
    $sql = trim($sql);
    if (!$conn->query($sql)) {
        echo "[Migration][ERROR] " . $conn->error . "\n";
        echo "  SQL: " . substr($sql, 0, 120) . "...\n";
    }
}

function addUniqueKeyIfMissing(mysqli $conn, string $table, string $keyName, string $columns): void
{
    $check = $conn->query(
        "SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME   = '{$table}'
           AND INDEX_NAME   = '{$keyName}'"
    );
    if ($check && $check->num_rows > 0) {
        echo "[Migration] Unique key `{$keyName}` on `{$table}` already exists – skipped.\n";
        return;
    }
    execSQL($conn, "ALTER TABLE `{$table}` ADD UNIQUE KEY `{$keyName}` ({$columns})");
    echo "[Migration] Added unique key `{$keyName}` on `{$table}`.\n";
}
