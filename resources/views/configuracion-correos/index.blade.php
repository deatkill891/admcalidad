{{-- resources/views/configuracion-correos/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="h4 fw-semibold text-dark">
      {{ __('Administración de Notificaciones por Correo') }}
    </h2>
  </x-slot>

  <div class="py-4">
    <div class="container-fluid">

      {{-- Alertas --}}
      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif
      @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Errores:</h5>
        <ul>
          {{-- Mostrar solo errores generales o los específicos del formulario principal --}}
          @foreach ($errors->getBag('default')->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif
      @if(session('error_modal'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error_modal') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      <div class="row g-4">
        <div class="col-lg-4">
          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4 p-md-5">
              <h5 class="fw-bold mb-3 text-dark border-bottom pb-3">
                <i class="fas fa-address-book me-2 text-primary"></i> Catálogo de Correos
              </h5>

              {{-- Formulario para agregar correo --}}
              <form method="POST" action="{{ route('config.correos.store.correo') }}" class="mb-4 needs-validation"
                novalidate>
                @csrf
                <div class="mb-3">
                  <label for="NombreDestinatario" class="form-label fw-semibold">Nombre <span
                      class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('NombreDestinatario') is-invalid @enderror"
                    id="NombreDestinatario" name="NombreDestinatario" value="{{ old('NombreDestinatario') }}" required>
                  @error('NombreDestinatario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="Correo" class="form-label fw-semibold">Correo Electrónico <span
                      class="text-danger">*</span></label>
                  <input type="email" class="form-control @error('Correo') is-invalid @enderror" id="Correo"
                    name="Correo" value="{{ old('Correo') }}" required>
                  @error('Correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="d-grid">
                  <button type="submit" class="btn btn-primary fw-bold">
                    <i class="fas fa-plus me-2"></i> Agregar Correo
                  </button>
                </div>
              </form>

              {{-- Lista de correos existentes con filtro --}}
              <h6 class="fw-bold mt-4 border-top pt-3">Correos Registrados ({{ $correosCatalogo->count() }})</h6>
              {{-- Campo de Búsqueda --}}
              <div class="mb-3 mt-2 position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ps-2 text-muted">
                  <i class="fas fa-search"></i>
                </span>
                <input type="text" id="filtroCorreos" class="form-control form-control-sm ps-4"
                  placeholder="Buscar por nombre o correo...">
              </div>

              @if($correosCatalogo->isNotEmpty())
              {{-- Contenedor de la lista con scroll --}}
              <div id="listaCorreosContainer" style="max-height: 400px; overflow-y: auto;">
                <ul class="list-group list-group-flush mt-2" id="listaCorreosUl">
                  @foreach($correosCatalogo as $correo)
                  {{-- Item de la lista con atributo data-filter-text --}}
                  <li
                    class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 {{ !$correo->Activo ? 'opacity-50' : '' }}"
                    data-filter-text="{{ strtolower($correo->NombreDestinatario . ' ' . $correo->Correo) }}">
                    <div>
                      <span class="fw-semibold">{{ $correo->NombreDestinatario }}</span>
                      <small class="d-block text-muted">{{ $correo->Correo }}</small>
                      @if(!$correo->Activo) <span class="badge bg-secondary rounded-pill small">Inactivo</span> @endif
                    </div>
                    {{-- Formulario para activar/desactivar --}}
                    <form action="{{ route('config.correos.destroy.correo', $correo->IdCorreo) }}" method="POST"
                      onsubmit="return confirm('¿Seguro que deseas {{ $correo->Activo ? 'DESACTIVAR' : 'ACTIVAR' }} este correo?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                        class="btn btn-sm {{ $correo->Activo ? 'btn-outline-warning' : 'btn-outline-success' }}"
                        title="{{ $correo->Activo ? 'Desactivar' : 'Activar' }}">
                        <i class="fas {{ $correo->Activo ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                      </button>
                    </form>
                  </li>
                  @endforeach
                </ul>
              </div>
              {{-- Mensaje si el filtro no encuentra resultados --}}
              <p id="noResultadosFiltro" class="text-muted mt-2 d-none small">No se encontraron correos que coincidan
                con la búsqueda.</p>
              @else
              <p class="text-muted mt-2">No hay correos registrados.</p>
              @endif
            </div>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-md-5">
              <h5 class="fw-bold mb-3 text-dark border-bottom pb-3">
                <i class="fas fa-cogs me-2 text-success"></i> Configuración por Proceso
              </h5>
              <p class="text-muted small">Gestiona qué correos (activos) recibirán notificaciones para cada proceso
                haciendo clic en "Gestionar".</p>

              @if($procesos->isEmpty())
              <div class="alert alert-info"><i class="fas fa-info-circle me-1"></i> No hay tipos de proceso definidos en
                la base de datos.</div>
              @elseif($correos->isEmpty()) {{-- $correos aquí son los activos --}}
              <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-1"></i> No hay correos activos
                registrados. <a href="{{ route('config.correos.index') }}#listaCorreosContainer">Agrega o activa
                  correos</a> para poder asignarlos.</div>
              @else
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th class="fw-semibold">Proceso</th>
                      <th class="fw-semibold text-center">Destinatarios Activos</th>
                      <th class="fw-semibold text-end">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($procesos as $proceso)
                    <tr>
                      <td>
                        <span class="fw-bold">{{ $proceso->Nombre }}</span>
                        @if($proceso->Descripcion)<small
                          class="d-block text-muted">{{ $proceso->Descripcion }}</small>@endif
                      </td>
                      <td class="text-center">
                        {{-- Conteos de asignaciones activas --}}
                        @php
                        $countTo = $asignaciones[$proceso->IdTipoProceso]['to_count'] ?? 0;
                        $countCc = $asignaciones[$proceso->IdTipoProceso]['cc_count'] ?? 0;
                        @endphp
                        <span class="badge bg-primary rounded-pill me-1"
                          title="{{ $countTo }} destinatario(s) principal(es) activo(s)">To: {{ $countTo }}</span>
                        <span class="badge bg-info rounded-pill"
                          title="{{ $countCc }} destinatario(s) en copia activo(s)">Cc: {{ $countCc }}</span>
                      </td>
                      <td class="text-end">
                        {{-- Botón para abrir el modal --}}
                        <button type="button" class="btn btn-sm btn-outline-primary manage-recipients-btn"
                          data-bs-toggle="modal" data-bs-target="#manageRecipientsModal"
                          data-process-id="{{ $proceso->IdTipoProceso }}" data-process-name="{{ $proceso->Nombre }}">
                          <i class="fas fa-users-cog me-1"></i> Gestionar
                        </button>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="modal fade" id="manageRecipientsModal" tabindex="-1" aria-labelledby="manageRecipientsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      {{-- Formulario que envía los datos del modal --}}
      <form id="modalRecipientsForm" method="POST" action="{{ route('config.correos.store.asignaciones.process') }}">
        @csrf
        <input type="hidden" name="IdTipoProceso" id="modalProcessId">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="manageRecipientsModalLabel">Gestionar Destinatarios para: <span
                id="modalProcessName" class="fw-bold"></span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small">Selecciona el tipo de destinatario para cada correo activo en este proceso.</p>
            {{-- ** Añadido: Filtro dentro del modal ** --}}
            <div class="mb-3 position-relative">
              <span class="position-absolute top-50 start-0 translate-middle-y ps-2 text-muted">
                <i class="fas fa-search"></i>
              </span>
              <input type="text" id="modalFiltroCorreos" class="form-control form-control-sm ps-4"
                placeholder="Buscar correo en esta lista...">
            </div>
            {{-- Contenedor para la lista de correos (se llenará con JS) --}}
            <div id="modalCorreoList" style="max-height: 55vh; overflow-y: auto;">
              {{-- Indicador de carga --}}
              <div class="text-center my-5" id="modalLoadingIndicator">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando correos...</p>
              </div>
              {{-- Placeholder si no hay correos activos --}}
              <div id="modalNoCorreos" class="alert alert-secondary d-none">No hay correos activos registrados para
                asignar.</div>
              {{-- Placeholder si el filtro del modal no encuentra resultados --}}
              <p id="modalNoResultadosFiltro" class="text-muted mt-2 d-none small">No se encontraron correos que
                coincidan con la búsqueda en esta lista.</p>
              {{-- Contenedor para errores AJAX --}}
              <div id="modalAjaxError" class="alert alert-danger d-none"></div>
              {{-- La lista de correos (list-group) se insertará aquí vía JS --}}
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success" id="modalSaveChangesBtn"><i class="fas fa-save me-1"></i>
              Guardar Cambios</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Font Awesome v6 (si no está globalmente) - Reemplaza 'YOUR-KIT-ID' --}}
  {{-- <script src="https://kit.fontawesome.com/YOUR-KIT-ID.js" crossorigin="anonymous"></script> --}}
  {{-- O usa la versión gratuita v5 si prefieres --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


  {{-- Scripts --}}
  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // --- Filtro para el Catálogo de Correos ---
    const filtroInput = document.getElementById('filtroCorreos');
    const listaCorreosUl = document.getElementById('listaCorreosUl');
    const noResultadosFiltroP = document.getElementById('noResultadosFiltro');

    if (filtroInput && listaCorreosUl) {
      filtroInput.addEventListener('input', function() {
        const filtroTexto = this.value.toLowerCase().trim();
        const items = listaCorreosUl.getElementsByTagName('li');
        let hayResultados = false;

        for (let i = 0; i < items.length; i++) {
          const item = items[i];
          const textoItem = item.getAttribute('data-filter-text');

          if (textoItem && textoItem.includes(filtroTexto)) {
            item.style.display = ''; // Usar '' en lugar de 'flex' para respetar el display original
            hayResultados = true;
          } else {
            item.style.display = 'none';
          }
        }
        if (noResultadosFiltroP) {
          noResultadosFiltroP.classList.toggle('d-none', hayResultados || items.length === 0);
        }
      });
    }

    // --- Lógica del Modal ---
    const manageRecipientsModal = document.getElementById('manageRecipientsModal');
    const modalProcessIdInput = document.getElementById('modalProcessId');
    const modalProcessNameSpan = document.getElementById('modalProcessName');
    const modalCorreoListDiv = document.getElementById('modalCorreoList');
    const modalLoadingIndicator = document.getElementById('modalLoadingIndicator');
    const modalNoCorreos = document.getElementById('modalNoCorreos');
    const modalAjaxError = document.getElementById('modalAjaxError');
    const modalSaveChangesBtn = document.getElementById('modalSaveChangesBtn');
    const modalForm = document.getElementById('modalRecipientsForm');
    // ** Elementos del filtro del modal **
    const modalFiltroInput = document.getElementById('modalFiltroCorreos');
    const modalNoResultadosFiltroP = document.getElementById('modalNoResultadosFiltro');


    // --- Event Listener para abrir el modal ---
    manageRecipientsModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const processId = button.getAttribute('data-process-id');
      const processName = button.getAttribute('data-process-name');

      modalProcessNameSpan.textContent = processName;
      modalProcessIdInput.value = processId;

      // Resetear estado del modal (incluyendo filtro)
      modalCorreoListDiv.innerHTML = '';
      modalCorreoListDiv.appendChild(modalLoadingIndicator); // Mostrar carga
      modalLoadingIndicator.classList.remove('d-none');
      modalNoCorreos.classList.add('d-none');
      modalAjaxError.classList.add('d-none');
      modalNoResultadosFiltroP.classList.add('d-none'); // Ocultar msg filtro
      if (modalFiltroInput) modalFiltroInput.value = ''; // Limpiar filtro
      modalSaveChangesBtn.disabled = true;

      // Petición AJAX (Fetch API)
      const fetchUrl = `/configuracion-correos/asignaciones/${processId}`; // Ruta definida en web.php

      fetch(fetchUrl)
        .then(response => {
          if (!response.ok) { // Chequear si la respuesta HTTP fue exitosa
            return response.text().then(text => { // Intentar leer el cuerpo del error
              throw new Error(`Error ${response.status}: ${response.statusText}. ${text}`);
            });
          }
          return response.json(); // Convertir respuesta a JSON
        })
        .then(data => {
          modalLoadingIndicator.classList.add('d-none'); // Ocultar indicador de carga

          if (data.error) { // Chequear si el JSON contiene un error de la aplicación
            throw new Error(data.error);
          }

          if (!data.correos || data.correos.length === 0) {
            modalNoCorreos.classList.remove('d-none'); // Mostrar mensaje "No hay correos"
            return; // Salir si no hay correos activos
          }

          // Construir HTML para la lista de correos
          const correoListHtml = data.correos.map(correo => {
            const currentAssignment = data.asignaciones[correo.IdCorreo] ||
              'none'; // 'to', 'cc', o 'none' por defecto
            const isCheckedNone = currentAssignment === 'none' ? 'checked' : '';
            const isCheckedTo = currentAssignment === 'to' ? 'checked' : '';
            const isCheckedCc = currentAssignment === 'cc' ? 'checked' : '';
            // Texto para el filtro del modal
            const filterTextModal = `${correo.NombreDestinatario} ${correo.Correo}`.toLowerCase();

            // Plantilla HTML para cada item de la lista
            return `
                                <div class="list-group-item d-sm-flex justify-content-between align-items-center" data-filter-text-modal="${filterTextModal}">
                                    <div class="mb-2 mb-sm-0 me-sm-2">
                                        <span class="fw-semibold">${correo.NombreDestinatario}</span>
                                        <small class="d-block text-muted">${correo.Correo}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm flex-shrink-0" role="group" aria-label="Tipo para ${correo.Correo}">
                                        <input type="radio" class="btn-check" name="modal_asignaciones[${correo.IdCorreo}]" id="modal-tipo-ninguno-${processId}-${correo.IdCorreo}" value="none" ${isCheckedNone}>
                                        <label class="btn btn-outline-secondary px-2 py-1" for="modal-tipo-ninguno-${processId}-${correo.IdCorreo}" title="No enviar"><i class="bi bi-slash-circle"></i></label>

                                        <input type="radio" class="btn-check" name="modal_asignaciones[${correo.IdCorreo}]" id="modal-tipo-to-${processId}-${correo.IdCorreo}" value="to" ${isCheckedTo}>
                                        <label class="btn btn-outline-primary px-2 py-1" for="modal-tipo-to-${processId}-${correo.IdCorreo}" title="Enviar Para (To)">To</label>

                                        <input type="radio" class="btn-check" name="modal_asignaciones[${correo.IdCorreo}]" id="modal-tipo-cc-${processId}-${correo.IdCorreo}" value="cc" ${isCheckedCc}>
                                        <label class="btn btn-outline-info px-2 py-1" for="modal-tipo-cc-${processId}-${correo.IdCorreo}" title="Enviar Copia (Cc)">Cc</label>
                                    </div>
                                </div>`;
          }).join('');

          // Insertar la lista completa en el div y habilitar el botón de guardar
          modalCorreoListDiv.innerHTML =
            `<div class="list-group" id="modalListaCorreosUl">${correoListHtml}</div>`;
          modalSaveChangesBtn.disabled = false;
        })
        .catch(error => {
          // Manejo de errores de la petición fetch o del procesamiento JSON
          console.error('Error al cargar asignaciones para el modal:', error);
          modalLoadingIndicator.classList.add('d-none'); // Ocultar carga
          modalAjaxError.textContent =
            `Error al cargar datos: ${error.message}. Recarga la página e intenta de nuevo.`;
          modalAjaxError.classList.remove('d-none'); // Mostrar mensaje de error
          modalSaveChangesBtn.disabled = true; // Mantener deshabilitado
        });
    });

    // --- Event Listener para el Filtro DENTRO del Modal ---
    if (modalFiltroInput) {
      modalFiltroInput.addEventListener('input', function() {
        const filtroTextoModal = this.value.toLowerCase().trim();
        const listaUlModal = document.getElementById('modalListaCorreosUl'); // El UL dentro del div
        const itemsModal = listaUlModal ? listaUlModal.children : []; // Los DIVs .list-group-item
        let hayResultadosModal = false;

        for (let i = 0; i < itemsModal.length; i++) {
          const itemModal = itemsModal[i];
          const textoItemModal = itemModal.getAttribute('data-filter-text-modal');

          if (textoItemModal && textoItemModal.includes(filtroTextoModal)) {
            itemModal.style.display = ''; // Usar '' para respetar el display original (flex en sm)
            hayResultadosModal = true;
          } else {
            itemModal.style.display = 'none';
          }
        }
        if (modalNoResultadosFiltroP) {
          modalNoResultadosFiltroP.classList.toggle('d-none', hayResultadosModal || itemsModal.length === 0);
        }
      });
    }


    // --- Limpiar modal al cerrarse ---
    manageRecipientsModal.addEventListener('hidden.bs.modal', function() {
      modalCorreoListDiv.innerHTML = ''; // Limpiar contenido
      modalProcessNameSpan.textContent = ''; // Limpiar título
      modalProcessIdInput.value = ''; // Limpiar ID oculto
      if (modalFiltroInput) modalFiltroInput.value = ''; // Limpiar filtro
      modalLoadingIndicator.classList.remove('d-none'); // Mostrar carga para la próxima vez
      modalNoCorreos.classList.add('d-none');
      modalAjaxError.classList.add('d-none');
      modalNoResultadosFiltroP.classList.add('d-none');
    });

    // --- Validación de formularios Bootstrap ---
    (() => {
      'use strict'
      const forms = document.querySelectorAll('.needs-validation')
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })
    })()

  }); // Fin DOMContentLoaded
  </script>
  @endpush

</x-app-layout>