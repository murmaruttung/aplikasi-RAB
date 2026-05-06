<?php
// includes/functions.php - Business Logic Functions

require_once __DIR__ . '/../config/database.php';
require_once 'auth.php';

// Helper functions for mysqli prepared statements
function query($sql, $params = [])
{
    global $conn;
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) return false;

    if (!empty($params)) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) $types .= 'i';
            elseif (is_float($param)) $types .= 'd';
            else $types .= 's';
        }
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    return $stmt;
}

function fetch($stmt)
{
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function fetchAll($stmt)
{
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function numRows($stmt)
{
    mysqli_stmt_store_result($stmt);
    return mysqli_stmt_num_rows($stmt);
}

// Get pagu by id
function get_pagu_by_id($id)
{
    $id = intval($id);

    $query = "SELECT pa.*, up.nama_unit, jp.nama_jenis
              FROM pagu_anggaran pa
              JOIN unit_pelaksana up ON pa.unit_id = up.id
              JOIN jenis_pagu jp ON pa.jenis_pagu_id = jp.id
              WHERE pa.id = ?";

    if (!is_admin()) {
        $query .= " AND pa.created_by = ?";
        $stmt = query($query, [$id, get_user_id()]);
    } else {
        $stmt = query($query, [$id]);
    }

    return $stmt ? fetch($stmt) : null;
}

// Get all pagu
function get_all_pagu()
{
    $query = "SELECT pa.*, up.nama_unit, jp.nama_jenis,
              (SELECT COALESCE(SUM(total_program), 0) FROM program_pagu WHERE pagu_id = pa.id) as total_program
              FROM pagu_anggaran pa
              JOIN unit_pelaksana up ON pa.unit_id = up.id
              JOIN jenis_pagu jp ON pa.jenis_pagu_id = jp.id";

    if (!is_admin()) {
        $query .= " WHERE pa.created_by = ?";
        $stmt = query($query . " ORDER BY pa.created_at DESC", [get_user_id()]);
    } else {
        $stmt = query($query . " ORDER BY pa.created_at DESC");
    }

    return $stmt ? $stmt : null;
}

// Get programs by pagu
function get_programs_by_pagu($pagu_id)
{
    $pagu_id = intval($pagu_id);

    $query = "SELECT pp.*,
              (SELECT COALESCE(SUM(jumlah), 0) FROM uraian_anggaran WHERE program_id = pp.id) as total_uraian
              FROM program_pagu pp
              WHERE pp.pagu_id = ?
              ORDER BY pp.id ASC";
    return query($query, [$pagu_id]);
}

// Get uraian by program
function get_uraian_by_program($program_id)
{
    $program_id = intval($program_id);

    $query = "SELECT ua.*, ab.kode_akun, ab.nama_akun
              FROM uraian_anggaran ua
              JOIN akun_belanja ab ON ua.akun_id = ab.id
              WHERE ua.program_id = ?
              ORDER BY ua.id ASC";
    return query($query, [$program_id]);
}

// Update total program
function update_total_program($program_id)
{
    $program_id = intval($program_id);

    query("UPDATE program_pagu SET
        total_program = (SELECT COALESCE(SUM(jumlah), 0) FROM uraian_anggaran WHERE program_id = ?),
        updated_at = NOW()
        WHERE id = ?", [$program_id, $program_id]);
}

// Update nominal pagu
function update_nominal_pagu($pagu_id)
{
    $pagu_id = intval($pagu_id);

    query("UPDATE pagu_anggaran SET
        nominal_pagu = (SELECT COALESCE(SUM(total_program), 0) FROM program_pagu WHERE pagu_id = ?),
        updated_at = NOW()
        WHERE id = ?", [$pagu_id, $pagu_id]);
}

// Get all units
function get_all_units()
{
    return query("SELECT * FROM unit_pelaksana ORDER BY nama_unit ASC");
}

// Get all jenis pagu
function get_all_jenis_pagu()
{
    return query("SELECT * FROM jenis_pagu ORDER BY nama_jenis ASC");
}

// Get all akun
function get_all_akun()
{
    return query("SELECT * FROM akun_belanja ORDER BY kode_akun ASC");
}

// Check unique value
function is_unique($table, $column, $value, $exclude_id = null)
{
    $query = "SELECT id FROM `$table` WHERE `$column` = ?";
    $params = [$value];

    if ($exclude_id) {
        $query .= " AND id != ?";
        $params[] = intval($exclude_id);
    }

    $stmt = query($query, $params);
    return $stmt ? numRows($stmt) === 0 : false;
}

// Check if data is used in other tables
function is_data_used($table, $column, $id)
{
    $stmt = query("SELECT COUNT(*) as total FROM `$table` WHERE `$column` = ?", [intval($id)]);
    if ($stmt) {
        $row = fetch($stmt);
        return $row['total'] ?? 0;
    }
    return 0;
}
