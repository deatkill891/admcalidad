document.addEventListener('DOMContentLoaded', function() {
  
  const sidebar = document.querySelector('.sidebar');
  const desktopToggle = document.getElementById('sidebarToggle');
  const mobileToggle = document.getElementById('mobileSidebarToggle');
  const overlay = document.getElementById('sidebarOverlay');

  // --- ESTADO INICIAL Y MANEJO DE RESIZE ---
  function initializeSidebar() {
    const isMobile = window.innerWidth <= 768;
    
    document.querySelectorAll('.sidebar .collapse.show').forEach(openMenu => {
        openMenu.classList.remove('show');
    });

    if (isMobile) {
      sidebar.classList.remove('toggled'); 
      overlay.classList.add('d-none');
    } else {
      const savedState = localStorage.getItem('sidebarState');
      if (savedState === 'toggled') {
        sidebar.classList.add('toggled');
      } else {
        sidebar.classList.remove('toggled');
      }
      overlay.classList.add('d-none');
    }
  }

  // --- MANEJADORES DE CLIC ---

  // 1. Toggle de Escritorio
  if (desktopToggle) {
    desktopToggle.addEventListener('click', function() {
      if (window.innerWidth > 768) {
        sidebar.classList.toggle('toggled');
        const state = sidebar.classList.contains('toggled') ? 'toggled' : 'expanded';
        localStorage.setItem('sidebarState', state);
      }
    });
  }

  // 2. Toggle de Móvil
  if (mobileToggle) {
    mobileToggle.addEventListener('click', function() {
      sidebar.classList.add('toggled'); 
      overlay.classList.remove('d-none');
    });
  }

  // 3. Clic en Overlay (cierra sidebar móvil)
  if (overlay) {
    overlay.addEventListener('click', function() {
      sidebar.classList.remove('toggled');
      overlay.classList.add('d-none');
    });
  }

  // 4. Lógica para menús desplegables (flotantes o colapsables)
  const dropdownTriggers = sidebar.querySelectorAll('a[data-bs-toggle="collapse"]');

  dropdownTriggers.forEach(trigger => {
    
    const submenu = document.querySelector(trigger.getAttribute('href'));
    if (!submenu) return;
    
    const parentLi = trigger.closest('.nav-item'); 
    if (!parentLi) return;

    // ABRIR en hover (solo en escritorio colapsado)
    parentLi.addEventListener('mouseenter', function() {
      const isMobile = window.innerWidth <= 768;
      const isSidebarToggled = sidebar.classList.contains('toggled');

      if (isSidebarToggled && !isMobile) {
        // MODO FLOTANTE
        document.querySelectorAll('.sidebar .collapse.show').forEach(openMenu => {
          if (openMenu !== submenu) {
            openMenu.classList.remove('show');
          }
        });
        
        // CSS se encarga de la posición, solo mostramos
        submenu.classList.add('show');
      }
    });

    // CERRAR al quitar el cursor (solo en escritorio colapsado)
    parentLi.addEventListener('mouseleave', function() {
      const isMobile = window.innerWidth <= 768;
      const isSidebarToggled = sidebar.classList.contains('toggled');

      if (isSidebarToggled && !isMobile) {
        submenu.classList.remove('show');
      }
    });

    // Controlar el CLIC
    trigger.addEventListener('click', function(e) {
      const isMobile = window.innerWidth <= 768;
      const isSidebarToggled = sidebar.classList.contains('toggled');

      if (isSidebarToggled && !isMobile) {
        // MODO FLOTANTE: Prevenir que el clic haga algo
        e.preventDefault();
        e.stopPropagation();
      }
      // En modo EXPANDIDO o MÓVIL: Dejar que Bootstrap maneje el 'data-bs-toggle'
    });
  });

  // --- ** INICIO: Lógica Cierre Móvil (Solicitud 2 Modificada) ** ---
  // Esta lógica AHORA solo cierra el menú en MÓVIL.
  const allNavLinks = sidebar.querySelectorAll('.nav-link');

  allNavLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      
      // No hacer nada si el link es un trigger de colapso
      if (link.hasAttribute('data-bs-toggle')) {
        return; 
      }

      // Si es un link final Y estamos en MÓVIL
      const isMobile = window.innerWidth <= 768;
      const isSidebarToggled = sidebar.classList.contains('toggled');

      if (isMobile && isSidebarToggled) {
        // MÓVIL: Ocultar el sidebar (off-canvas) y el overlay
        sidebar.classList.remove('toggled');
        overlay.classList.add('d-none');
      }
      // En modo escritorio (colapsado o expandido), ya no hacemos nada al hacer clic.
      // El menú se quedará abierto (expandido) o se cerrará por 'mouseleave' (flotante).
    });
  });
  // --- ** FIN LÓGICA CIERRE MÓVIL ** ---


  // --- INICIALIZACIÓN ---
  window.addEventListener('resize', initializeSidebar);
  initializeSidebar();

});