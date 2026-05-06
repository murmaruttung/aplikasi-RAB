<?php
// modules/export/program.php - Export Per Program
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/helpers.php';

check_login();

$program_id = validate_int($_GET['program_id'] ?? 0);

$query = "SELECT pp.*, pa.judul_kegiatan, pa.nama_pagu, up.nama_unit, jp.nama_jenis
          FROM program_pagu pp
          JOIN pagu_anggaran pa ON pp.pagu_id = pa.id
          JOIN unit_pelaksana up ON pa.unit_id = up.id
          JOIN jenis_pagu jp ON pa.jenis_pagu_id = jp.id
          WHERE pp.id = $program_id";

if (!is_admin()) $query .= " AND pa.created_by = " . get_user_id();

$result = mysqli_query($conn, $query);
$program = mysqli_fetch_assoc($result);

if (!$program) safe_redirect('../../index.php');

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="Program_' . preg_replace('/[^a-zA-Z0-9]/', '_', $program['nama_program']) . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

$uraians = get_uraian_by_program($program_id);

echo '<html><head><meta charset="utf-8"><style>table{border-collapse:collapse;width:100%}th,td{border:1px solid #000;padding:8px;font-size:12px}.center{text-align:center}.right{text-align:right}.bold{font-weight:bold}.header{background:#d9e1f2}</style></head><body>';
echo '<h3>POK - ' . h($program['nama_program']) . '</h3>';
echo '<table><tr><td width="20%">Unit</td><td>: ' . h($program['nama_unit']) . '</td></tr>';
echo '<tr><td>Jenis Pagu</td><td>: ' . h($program['nama_jenis']) . '</td></tr>';
echo '<tr><td>Kegiatan</td><td>: ' . h($program['judul_kegiatan']) . '</td></tr>';
echo '<tr><td>Program</td><td>: ' . h($program['nama_program']) . '</td></tr>';
echo '<tr><td>Total</td><td>: ' . format_rupiah($program['total_jumlah']) . '</td></tr></table><br>';

echo '<table><tr class="header"><th class="center">No</th><th>Kode Akun</th><th>Nama Akun</th><th>Uraian Kegiatan</th><th class="right">Volume</th><th>Satuan</th><th class="right">Harga Satuan</th><th class="right">Jumlah Harga</th></tr>';

$no = 1;
$total = 0;
while ($uraian = mysqli_fetch_assoc($uraians)) {
    $total += $uraian['jumlah_harga'];
    echo '<tr><td class="center">' . $no++ . '</td><td>' . h($uraian['kode_akun']) . '</td><td>' . h($uraian['nama_akun']) . '</td><td>' . h($uraian['uraian_kegiatan']) . '</td><td class="right">' . format_number($uraian['volume']) . '</td><td>' . h($uraian['satuan']) . '</td><td class="right">' . format_rupiah($uraian['harga_satuan']) . '</td><td class="right">' . format_rupiah($uraian['jumlah_harga']) . '</td></tr>';
}
echo '<tr class="bold"><td colspan="7" class="right">TOTAL</td><td class="right">' . format_rupiah($total) . '</td></tr></table>';
echo '</body></html>';
exit();
