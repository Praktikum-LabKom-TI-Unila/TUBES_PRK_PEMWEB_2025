document.getElementById('petugasLoginForm').addEventListener('submit', function(event) {
    event.preventDefault(); 
    
    // Logika Login...
    alert('Login Petugas berhasil! Mengarahkan ke daftar tugas...');
    window.location.href = 'petugas-task-list.php';
});