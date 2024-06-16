function validateImage() {
    const imageInput = document.querySelector('input[type="file"]');
    const file = imageInput.files[0];
    const validTypes = ['image/jpeg', 'image/png', 'image/jfif'];

    if (file.size > 3 * 1024 * 1024) {
         alert('File size must not exceed 3MB.');
         return false;
     }
 
     if (!validTypes.includes(file.type)) {
         alert('Only JPEG, PNG, and JFIF formats are allowed.');
         return false;
     }
 
     const img = new Image();
     img.onload = function() {
         if (this.width < 225 || this.height < 224 || this.width > 300 || this.height > 300) {
             alert('Image dimensions should be between 225x224 and 300x300 pixels.');
             return false;
         }
     };
     img.src = URL.createObjectURL(file);
 
     return true;
 }

 function toggleSidebar() {
     const sidebar = document.getElementById('sidebar');
     sidebar.classList.toggle('minimized');
     const menuHeader = document.getElementById('menuHeader');
     menuHeader.style.display = sidebar.classList.contains('minimized') ? 'none' : 'inline';
 }

 document.getElementById('logoutLink').addEventListener('click', function(event) {
     event.preventDefault();
     if (confirm('Are you sure you want to sign out?')) {
         window.location.href = this.href;
     }
 });

 document.addEventListener('click', function(event) {
     const sidebar = document.getElementById('sidebar');
     const toggleBtn = document.querySelector('.toggle-btn');

     if (window.innerWidth <= 768 && !sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
         sidebar.classList.remove('active');
     }
 });

 // Hide elements when update button is clicked
 document.querySelectorAll('.update-btn').forEach(button => {
     button.addEventListener('click', function(event) {
         event.preventDefault();
         window.location.href = this.href;
         document.getElementById('addProductForm').classList.add('hidden');
         document.getElementById('productListHeader').classList.add('hidden');
         document.getElementById('productTable').classList.add('hidden');
         document.getElementById('updateFormContainer').classList.remove('hidden');
     });
 });

 // Show elements when cancel button is clicked
 document.getElementById('closeEdit').addEventListener('click', function() {
     document.getElementById('addProductForm').classList.remove('hidden');
     document.getElementById('productListHeader').classList.remove('hidden');
     document.getElementById('productTable').classList.remove('hidden');
     document.getElementById('updateFormContainer').classList.add('hidden');
 });

 // Automatically show update form if edit parameter is present in URL
 document.addEventListener('DOMContentLoaded', function() {
     const urlParams = new URLSearchParams(window.location.search);
     if (urlParams.has('edit')) {
         document.getElementById('addProductForm').classList.add('hidden');
         document.getElementById('productListHeader').classList.add('hidden');
         document.getElementById('productTable').classList.add('hidden');
         document.getElementById('updateFormContainer').classList.remove('hidden');
     }
 });

function toggleSizeFields(mode = 'add') {
    var category = document.getElementById(mode === 'add' ? "productCategory" : "updateProductCategory").value;
    var sizeFields = document.querySelectorAll('.size-field');
    var accessoriesStockField = document.getElementById(mode === 'add' ? "accessoriesStock" : "updateAccessoriesStock");

    if (category === "Accessories") {
        sizeFields.forEach(function(field) {
            field.disabled = true;
            field.value = 0; // Set value to 0 when disabled
        });
        accessoriesStockField.disabled = false;
        accessoriesStockField.required = true;
    } else {
        sizeFields.forEach(function(field) {
            field.disabled = false;
        });
        accessoriesStockField.disabled = true;
        accessoriesStockField.value = 0; // Set accessories stock to 0 when disabled
        accessoriesStockField.required = false;
    }
}

// Call the function initially to set the initial state of fields
toggleSizeFields();

// reserved form
function showReservedForm() {
    document.getElementById('reservedFormModal').style.display = "block";
}

function closeReservedForm() {
    document.getElementById('reservedFormModal').style.display = "none";
}

window.onclick = function(event) {
    const reservedModal = document.getElementById('reservedFormModal');
    if (event.target == reservedModal) {
        reservedModal.style.display = "none";
    }

    const orderModal = document.getElementById('orderFormModal');
    if (event.target == orderModal) {
        orderModal.style.display = "none";
    }
}

// order form
function showOrderForm(merchandiseId) {
    document.getElementById('orderFormModal').style.display = 'block';
    document.getElementById('orderMerchandiseId').value = merchandiseId;
}

function closeOrderForm() {
    document.getElementById('orderFormModal').style.display = 'none';
}
