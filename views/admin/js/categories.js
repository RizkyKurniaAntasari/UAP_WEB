const categoryTableBody = document.getElementById('categoryTableBody');
const categoryModal = document.getElementById('categoryModal');
const modalTitle = document.getElementById('modalTitle');
const categoryForm = document.getElementById('categoryForm');
const categoryId = document.getElementById('categoryId');
const categoryName = document.getElementById('categoryName');
const categoryDescription = document.getElementById('categoryDescription'); 

function openAddModal() {
    modalTitle.textContent = 'Tambah Kategori Baru';
    categoryId.value = ''; 
    categoryName.value = '';
    categoryDescription.value = '';

    categoryModal.classList.remove('hidden');
    categoryModal.classList.add('flex');     
}

function openEditModal(data) {
    modalTitle.textContent = 'Edit Kategori';
    categoryId.value = data.id; 
    categoryName.value = data.nama_kategori;
    categoryDescription.value = data.deskripsi;

    categoryModal.classList.remove('hidden');
    categoryModal.classList.add('flex');     
}

function closeModal() {
    categoryModal.classList.add('hidden');    
    categoryModal.classList.remove('flex');   
}