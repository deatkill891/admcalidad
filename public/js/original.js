document.addEventListener('DOMContentLoaded', function() {
    // 1. Obtener los elementos clave del DOM
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const sidebarToggle = document.getElementById('sidebarToggle');

    // 2. Definir la función de alternancia (toggle)
    function toggleSidebar() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    }

    // 3. Agregar el listener de evento al icono
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    // Opcional: Si deseas que el sidebar permanezca expandido por defecto, no hagas nada aquí.
    // Si deseas que inicie colapsado, descomenta la siguiente línea:
    // toggleSidebar(); 
});