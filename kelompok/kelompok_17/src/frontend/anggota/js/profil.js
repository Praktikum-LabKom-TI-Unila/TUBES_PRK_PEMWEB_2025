/* =========================================================
   SIMORA - PROFIL ANGGOTA
   Menangani pengambilan dan update data profil
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {

    /* -----------------------------------------------------
       Dummy data — nanti diganti API
    ----------------------------------------------------- */
    const userData = {
        name: "Naufal Akbar",
        email: "naufal@example.com",
        phone: "081234567890",
        address: "Jl. Merdeka No. 45, Bandung",
        joinDate: "12 Januari 2024",
        status: "Aktif",
        membership: "Anggota Tetap"
    };

    /* -----------------------------------------------------
       Elemen DOM
    ----------------------------------------------------- */
    const elName = document.getElementById("profile-name");
    const elRole = document.getElementById("profile-role");
    const elEmail = document.getElementById("detail-email");
    const elPhone = document.getElementById("detail-phone");
    const elAddress = document.getElementById("detail-address");
    const elJoin = document.getElementById("detail-join");
    const elStatus = document.getElementById("detail-status");
    const elAvatar = document.getElementById("profile-avatar");

    /* Modal */
    const modal = document.getElementById("modal-edit");
    const modalBackdrop = document.getElementById("modal-backdrop");
    const btnOpenModal = document.getElementById("btn-edit-profil");
    const btnCloseModal = document.getElementById("close-modal");
    const btnCancel = document.getElementById("cancel-edit");
    const btnSave = document.getElementById("save-edit");

    /* Input Fields */
    const editName = document.getElementById("edit-name");
    const editEmail = document.getElementById("edit-email");
    const editPhone = document.getElementById("edit-phone");
    const editAddress = document.getElementById("edit-address");
    const editStatus = document.getElementById("edit-status");


    /* -----------------------------------------------------
       Fungsi untuk load data ke tampilan
    ----------------------------------------------------- */
    function loadProfile() {
        elName.textContent = userData.name;
        elRole.textContent = userData.membership;

        elEmail.textContent = userData.email;
        elPhone.textContent = userData.phone;
        elAddress.textContent = userData.address;
        elJoin.textContent = userData.joinDate;
        elStatus.textContent = userData.status;

        // Avatar awal nama
        elAvatar.textContent = getInitials(userData.name);
    }

    function getInitials(name) {
        return name.split(" ").map(n => n[0]).join("").toUpperCase();
    }

    loadProfile();


    /* -----------------------------------------------------
       MODAL: buka & tutup
    ----------------------------------------------------- */
    function openModal() {
        modal.classList.add("show");
        modalBackdrop.classList.add("show");

        // Pre-fill form
        editName.value = userData.name;
        editEmail.value = userData.email;
        editPhone.value = userData.phone;
        editAddress.value = userData.address;
        editStatus.value = userData.status;
    }

    function closeModal() {
        modal.classList.remove("show");
        modalBackdrop.classList.remove("show");
    }

    btnOpenModal.addEventListener("click", openModal);
    btnCloseModal.addEventListener("click", closeModal);
    btnCancel.addEventListener("click", closeModal);

    modalBackdrop.addEventListener("click", closeModal);


    /* -----------------------------------------------------
       SIMPAN PERUBAHAN
    ----------------------------------------------------- */
    btnSave.addEventListener("click", () => {

        userData.name = editName.value;
        userData.email = editEmail.value;
        userData.phone = editPhone.value;
        userData.address = editAddress.value;
        userData.status = editStatus.value;

        loadProfile();   // Refresh UI
        closeModal();    // Tutup modal

    });

});
