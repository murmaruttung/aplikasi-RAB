<?php
// modules/export/all.php - Export Semua Program per Pagu
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/helpers.php';

check_login();

$pagu_id = validate_int($_GET['id'] ?? 0);
$pagu = get_pagu_by_id($pagu_id);

if (!$pagu) safe_redirect('../../index.php');

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="POK_' . preg_replace('/[^a-zA-Z0-9]/', '_', $pagu['judul_kegiatan']) . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

$programs = get_programs_by_pagu($pagu_id);

echo '<html><head><meta charset="utf-8"><style>table{border-collapse:collapse;width:100%}th,td{border:1px solid #000;padding:8px;font-size:12px}.center{text-align:center}.right{text-align:right}.bold{font-weight:bold}.header{background:#d9e1f2}h3,h4{margin:5px 0}</style></head><body>';
echo '<h3>PETUNJUK OPERASIONAL KEGIATAN (POK)</h3>';
echo '<table><tr><td width="20%">Unit Pelaksana</td><td>: ' . h($pagu['nama_unit']) . '</td></tr>';
echo '<tr><td>Jenis Pagu</td><td>: ' . h($pagu['nama_jenis']) . '</td></tr>';
echo '<tr><td>Judul Kegiatan</td><td>: ' . h($pagu['judul_kegiatan']) . '</td></tr>';
echo '<tr><td>Nama Pagu</td><td>: ' . h($pagu['nama_pagu']) . '</td></tr>';
echo '<tr><td>Total Pagu</td><td>: ' . format_rupiah($pagu['nominal_pagu']) . '</td></tr></table><br>';

$no_program = 1;
while ($program = mysqli_fetch_assoc($programs)) {
    echo '<h4>Program ' . $no_program . ': ' . h($program['nama_program']) . '</h4>';
    echo '<table><tr class="header"><th class="center">No</th><th>Kode Akun</th><th>Uraian Kegiatan</th><th class="right">Volume</th><th>Satuan</th><th class="right">Harga Satuan</th><th class="right">Jumlah Harga</th></tr>';

    $uraians = get_uraian_by_program($program['id']);
    $no = 1;
    $total = 0;
    while ($uraian = mysqli_fetch_assoc($uraians)) {
        $total += $uraian['jumlah_harga'];
        echo '<tr><td class="center">' . $no++ . '</td><td>' . h($uraian['kode_akun']) . '</td><td>' . h($uraian['uraian_kegiatan']) . '</td><td class="right">' . format_number($uraian['volume']) . '</td><td>' . h($uraian['satuan']) . '</td><td class="right">' . format_rupiah($uraian['harga_satuan']) . '</td><td class="right">' . format_rupiah($uraian['jumlah_harga']) . '</td></tr>';
    }
    echo '<tr class="bold"><td colspan="6" class="right">Total Program</td><td class="right">' . format_rupiah($total) . '</td></tr></table><br>';
    $no_program++;
}
echo '</body></html>';
exit();
