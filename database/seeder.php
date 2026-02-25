<?php

/**
 * Database Seeder
 * Thomson Membership Dashboard
 *
 * Seeds initial data into the database tables.
 * Called by migrate.php or can be run standalone via CLI:
 *   php seeder.php
 */

require_once __DIR__ . '/../config/config.inc.php';

function runSeeder(mysqli $conn): void
{
    echo "\n[Seeder] Starting seed process...\n";

    seedUsers($conn);

    echo "[Seeder] Seed complete.\n";
}

// ---------------------------------------------------------------------------
// Table: user
// ---------------------------------------------------------------------------
function seedUsers(mysqli $conn): void
{
    echo "[Seeder] Seeding table: user\n";

    $users = [
        [1,  'Admin',                                         'admin',       'IT',                    '22/06/23',            'Active', 'IT',   'c75f28325cfa028ea13872f977a29e0e87c99a4f390fe260f24d7e1f05fb8d75', '2026-02-25 09:17:07', NULL,                       '',   '',              1, NULL,                  0],
    ];

    $sql = "INSERT INTO `user`
        (`userid`, `name`, `username`, `department`, `datecreate`, `status`, `role`, `password`,
         `last_login`, `session_id`, `ip_address`, `user_agent`, `failed_attempts`, `lockout_time`, `initial`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            `name`            = VALUES(`name`),
            `username`        = VALUES(`username`),
            `department`      = VALUES(`department`),
            `datecreate`      = VALUES(`datecreate`),
            `status`          = VALUES(`status`),
            `role`            = VALUES(`role`),
            `password`        = VALUES(`password`),
            `last_login`      = VALUES(`last_login`),
            `session_id`      = VALUES(`session_id`),
            `ip_address`      = VALUES(`ip_address`),
            `user_agent`      = VALUES(`user_agent`),
            `failed_attempts` = VALUES(`failed_attempts`),
            `lockout_time`    = VALUES(`lockout_time`),
            `initial`         = VALUES(`initial`)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "[Seeder][ERROR] Prepare failed: " . $conn->error . "\n";
        return;
    }

    $inserted = 0;
    foreach ($users as $u) {
        // Normalize last_login: replace MySQL zero-date with a valid timestamp
        $lastLogin = ($u[8] === '0000-00-00 00:00:00') ? '2000-01-01 00:00:00' : $u[8];

        $stmt->bind_param(
            'isssssssssssiis',
            $u[0],   // userid        INT
            $u[1],   // name
            $u[2],   // username
            $u[3],   // department
            $u[4],   // datecreate
            $u[5],   // status
            $u[6],   // role
            $u[7],   // password
            $lastLogin,
            $u[9],   // session_id    (NULL OK)
            $u[10],  // ip_address
            $u[11],  // user_agent
            $u[12],  // failed_attempts
            $u[13],  // lockout_time  (NULL OK)
            $u[14]   // initial       INT (0 or 1)
        );

        if ($stmt->execute()) {
            $inserted++;
        } else {
            echo "[Seeder][WARN] Row userid={$u[0]} failed: " . $stmt->error . "\n";
        }
    }

    $stmt->close();
    echo "[Seeder] user: {$inserted} row(s) processed.\n Login using username 'admin' and password 'Welcome@123' (hashed in DB) for initial access.\n";
}

// ---------------------------------------------------------------------------
// CLI entry-point
// ---------------------------------------------------------------------------
if (php_sapi_name() === 'cli') {
    runSeeder($conn);
}
