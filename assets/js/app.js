/**
 * SIPER v2.0 - Application JavaScript
 */

// Inisialisasi saat DOM siap
document.addEventListener('DOMContentLoaded', function () {
    initTooltips();
    initSidebar();
});

// Inisialisasi Bootstrap Tooltips
function initTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) {
        return new bootstrap.Tooltip(el);
    });
}

// Inisialisasi Sidebar
function initSidebar() {
    var sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    // Tutup sidebar saat klik link di dalam sidebar (mobile)
    var sidebarLinks = sidebar.querySelectorAll('.nav-link');
    sidebarLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                var bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebar);
                if (bsOffcanvas) {
                    bsOffcanvas.hide();
                }
            }
        });
    });

    // Tutup sidebar saat klik di luar (mobile)
    document.addEventListener('click', function (e) {
        if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
            if (!sidebar.contains(e.target) && !e.target.closest('[data-bs-toggle="offcanvas"]')) {
                var bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebar);
                if (bsOffcanvas) {
                    bsOffcanvas.hide();
                }
            }
        }
    });
}

// Toggle Import Form
function toggleImport(programId) {
    var form = document.getElementById('import-form-' + programId);
    if (form) {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
}

// Modal: Edit Program
function editProgram(id, name) {
    document.getElementById('edit_program_id').value = id;
    document.getElementById('edit_program_name').value = name;
    document.getElementById('editProgramModal').style.display = 'flex';
}
function closeEditProgram() {
    document.getElementById('editProgramModal').style.display = 'none';
}

// Modal: Edit Uraian
function editUraian(id, akunId, uraian, volume, satuan, harga, programId) {
    document.getElementById('edit_uraian_id').value = id;
    document.getElementById('edit_uraian_program_id').value = programId;
    document.getElementById('edit_akun_id').value = akunId;
    document.getElementById('edit_uraian_kegiatan').value = uraian;
    document.getElementById('edit_volume').value = volume;
    document.getElementById('edit_satuan').value = satuan;
    document.getElementById('edit_harga_satuan').value = harga;
    document.getElementById('editUraianModal').style.display = 'flex';
}
function closeEditUraian() {
    document.getElementById('editUraianModal').style.display = 'none';
}

// Modal: Blokir
function blokirUraian(id, jumlah) {
    document.getElementById('blokir_uraian_id').value = id;
    document.getElementById('blokir_nilai').value = jumlah;
    document.getElementById('blokir_info').innerHTML = 'Jumlah tersedia: ' + formatRupiah(jumlah);
    document.getElementById('blokirModal').style.display = 'flex';
}
function closeBlokir() {
    document.getElementById('blokirModal').style.display = 'none';
}

// Format Rupiah
function formatRupiah(angka) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
}

// Konfirmasi Hapus
function confirmDelete(message) {
    return confirm(message || 'Apakah Anda yakin ingin menghapus data ini?');
}

// Auto-hide alert setelah 5 detik
setTimeout(function () {
    var alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
