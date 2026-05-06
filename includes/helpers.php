<?php
// includes/helpers.php - Helper Functions

// Format Rupiah
function format_rupiah($angka)
{
    $angka = floatval($angka);
    if ($angka == 0) return 'Rp 0';
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Format Number
function format_number($angka)
{
    return number_format(floatval($angka), 0, ',', '.');
}

// Parse angka dari berbagai format
function parse_angka($str)
{
    $str = str_replace(['Rp', ' ', 'rp', 'RP'], '', trim($str));
    $str = str_replace('.', '', $str);
    $str = str_replace(',', '.', $str);
    return floatval($str);
}

// Get status blokir class
function get_blokir_class($uraian)
{
    if (!empty($uraian['is_blokir'])) {
        return ($uraian['nilai_blokir'] >= $uraian['jumlah_harga']) ? 'table-danger' : 'table-warning';
    }
    return '';
}

// Format tanggal Indonesia
function format_tanggal($date)
{
    if (empty($date)) return '-';
    return date('d/m/Y', strtotime($date));
}

// Alert HTML
function alert($type, $message)
{
    $icons = [
        'success' => 'check-circle',
        'danger' => 'exclamation-triangle',
        'warning' => 'exclamation-triangle',
        'info' => 'info-circle'
    ];
    $icon = $icons[$type] ?? 'info-circle';

    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
        <i class="bi bi-' . $icon . '"></i> ' . h($message) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}
