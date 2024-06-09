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
