// Validasi form pendaftaran
function validateForm() {
    const nama = document.getElementById('nama');
    if (nama && nama.value.trim() === '') {
        alert('Nama lengkap harus diisi');
        nama.focus();
        return false;
    }
    
    const nohp = document.querySelector('input[name="no_hp"]');
    if (nohp) {
        const hpRegex = /^[0-9]{10,13}$/;
        if (!hpRegex.test(nohp.value)) {
            alert('Nomor HP tidak valid (10-13 digit angka)');
            return false;
        }
    }
    
    return true;
}

// Konfirmasi hapus
function confirmDelete() {
    return confirm('Apakah Anda yakin ingin menghapus data ini?');
}

// Preview gambar sebelum upload
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Live search
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName("tr");
    
    for (let i = 1; i < tr.length; i++) {
        let found = false;
        const td = tr[i].getElementsByTagName("td");
        for (let j = 0; j < td.length; j++) {
            if (td[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
                found = true;
            }
        }
        tr[i].style.display = found ? "" : "none";
    }
}