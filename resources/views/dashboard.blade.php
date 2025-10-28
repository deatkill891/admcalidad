<x-app-layout>
  {{-- Incluir Font Awesome --}}
  <script src="https://kit.fontawesome.com/9d1bcc908a.js" crossorigin="anonymous"></script>

  {{-- ** Select2 CSS y jQuery ** --}}
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  {{-- Encabezado de la página --}}
  <x-slot name="header">
    <h2 class="h4 fw-semibold text-dark">
      {{ __('Inicio y Registro de Muestras') }}
    </h2>
  </x-slot>

  <div class="py-4">
    <div class="container-fluid">

      {{-- TABLA: MUESTRAS EN LISTA DE ESPERA --}}
      <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4 p-md-5">
          <h5 class="fw-bold mb-3 text-dark">
            <i class="fas fa-tasks me-2 text-success"></i> Muestras en Lista de Espera
          </h5>
          <div class="table-responsive">
            <table class="table table-hover align-middle rounded-4 overflow-hidden">
              <thead style="background-color: #e8f5e9; color: #212529; font-weight: 600;">
                <tr>
                  <th>Folio</th>
                  <th>Material</th>
                  <th>Fecha de Registro</th>
                  <th>Proveedor / Solicitante</th>
                  <th>Registró</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="tabla-espera-body">
                @forelse($muestrasEnEspera as $muestra)
                <tr>
                  <td>{{ $muestra->IdMuestra }}</td>
                  <td>{{ $muestra->material->Material ?? 'N/A' }}</td>
                  <td>
                    {{ $muestra->FechaRegistro ? \Carbon\Carbon::parse($muestra->FechaRegistro)->format('d/m/Y, h:i a') : 'N/A' }}
                  </td>
                  <td>{{ $muestra->Proveedor ?: ($muestra->Solicitante ?: 'N/A') }}</td>
                  <td>{{ $muestra->usuarioOper->username ?? 'N/A' }}</td>
                  <td>
                    <a href="{{ route('muestras.analizar.form', $muestra) }}"
                      class="btn btn-sm btn-outline-success px-3 me-1" title="Iniciar Análisis">
                      <i class="fas fa-play me-1"></i> Analizar
                    </a>
                    <form action="{{ route('muestras.rechazar', $muestra) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('¿Estás seguro de que deseas rechazar esta muestra?');">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm btn-outline-danger px-3" title="Rechazar Muestra">
                        <i class="fas fa-times"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-5">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <p class="mb-0">¡Excelente! No hay muestras pendientes por analizar.</p>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- CONDICIONAL: Mostrar tarjeta de registro solo si el usuario tiene permiso --}}
      @if($puedeRegistrar)
      <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4 p-md-5">
          <h5 class="fw-bold mb-3 text-dark border-bottom pb-3">
            <i class="fas fa-plus-circle me-2 text-primary"></i> Registrar Nueva Muestra
          </h5>

          @if(session('success'))
          <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
          </div>
          @endif

          <form method="POST" action="{{ route('muestras.store') }}">
            @csrf
            <div class="row g-3">
              {{-- Selector de Material (MODIFICADO PARA SELECT2) --}}
              <div class="col-md-12">
                <label for="IdMaterial" class="form-label fw-semibold">Material <span
                    class="text-danger">*</span></label>
                {{-- Añadimos la clase 'select2-enable' para identificarlo fácilmente en JS --}}
                <select id="IdMaterial" name="IdMaterial" class="form-select select2-enable" required
                  style="width: 100%;"> {{-- Style width es útil para Select2 --}}
                  <option value="" disabled selected>Seleccione un material...</option>
                  @foreach($materiales as $material)
                  <option value="{{ $material->IdMaterial }}"
                    {{ old('IdMaterial') == $material->IdMaterial ? 'selected' : '' }}>
                    {{ $material->Material }}
                  </option>
                  @endforeach
                </select>
              </div>

              {{-- Contenedor para campos dinámicos --}}
              <div id="campos-dinamicos" class="row g-3 m-0 p-0">
                {{-- Grupo Vehicular --}}
                <div id="grupo-vehicular" class="row g-3 m-0 p-0" style="display: none;">
                  <div class="col-md-4"> <label class="form-label fw-semibold">Proveedor</label> <input type="text"
                      class="form-control" name="Proveedor" value="{{ old('Proveedor') }}"> </div>
                  <div class="col-md-4"><label class="form-label fw-semibold">Remisión</label><input type="text"
                      class="form-control" name="Remision" value="{{ old('Remision') }}"></div>
                  <div class="col-md-4"><label class="form-label fw-semibold">Placa Tractor</label><input type="text"
                      class="form-control" name="PlacaTractor" value="{{ old('PlacaTractor') }}"></div>
                  <div class="col-md-4"><label class="form-label fw-semibold">Placa Tolva</label><input type="text"
                      class="form-control" name="PlacaTolva" value="{{ old('PlacaTolva') }}"></div>
                  <div class="col-md-4"><label class="form-label fw-semibold">Tonelaje</label><input type="text"
                      class="form-control" name="Tonelaje" value="{{ old('Tonelaje') }}"></div>
                </div>
                {{-- Grupo Interno --}}
                <div id="grupo-interno" class="row g-3 m-0 p-0" style="display: none;">
                  <div class="col-md-4"><label class="form-label fw-semibold">Solicitante</label><input type="text"
                      class="form-control" name="Solicitante" value="{{ old('Solicitante') }}"></div>
                  <div class="col-md-4"><label class="form-label fw-semibold">Área</label><input type="text"
                      class="form-control" name="Area" value="{{ old('Area') }}"></div>
                  <div class="col-md-4"><label class="form-label fw-semibold">Identificación</label><input type="text"
                      class="form-control" name="Identificacion" value="{{ old('Identificacion') }}"></div>
                  <div class="col-md-12"><label class="form-label fw-semibold">Análisis a realizar</label><input
                      type="text" class="form-control" name="Analisis" value="{{ old('Analisis') }}"></div>
                </div>
                {{-- Grupo Insumos --}}
                <div id="grupo-insumos" class="row g-3 m-0 p-0" style="display: none;">
                  <div class="col-md-6"> <label class="form-label fw-semibold">Proveedor</label> <input type="text"
                      class="form-control" name="Proveedor" value="{{ old('Proveedor') }}"> </div>
                  <div class="col-md-6"><label class="form-label fw-semibold">Remisión</label><input type="text"
                      class="form-control" name="Remision" value="{{ old('Remision') }}"></div>
                </div>
              </div>

            </div>
            <div class="d-flex justify-content-end mt-4">
              <button type="submit" class="btn btn-primary fw-bold px-4 py-2">
                <i class="fas fa-save me-2"></i> Registrar Muestra
              </button>
            </div>
          </form>
        </div>
      </div>
      @endif {{-- Fin @if($puedeRegistrar) --}}

    </div>
  </div>

  {{-- ** MODAL PARA IMPRIMIR ETIQUETA ** --}}
  <div class="modal fade" id="modalImprimirEtiqueta" tabindex="-1" aria-labelledby="modalImprimirLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 rounded-4 shadow-lg">
        <div class="modal-header border-bottom-0">
          <h5 class="modal-title fw-bold" id="modalImprimirLabel">
            <i class="fas fa-print me-2 text-primary"></i> Impresión de Etiqueta
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body py-4">
          <p class="text-center fs-5">¿Deseas imprimir la etiqueta para la muestra registrada?</p>
        </div>
        <div class="modal-footer border-top-0 d-flex justify-content-center">
          <button type="button" class="btn btn-secondary fw-bold px-4 py-2" data-bs-dismiss="modal"
            id="btn-no-imprimir">
            No, cerrar
          </button>
          <button type="button" class="btn btn-primary fw-bold px-4 py-2" id="btn-si-imprimir">
            <i class="fas fa-check me-2"></i> Sí, imprimir
          </button>
        </div>
      </div>
    </div>
  </div>


  {{-- ** Select2 JS ** --}}
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  {{-- SCRIPT JAVASCRIPT CORREGIDO Y ROBUSTO --}}
  <script>
  $(document).ready(function() {

    // === INICIALIZACIÓN DE SELECT2 ===
    $('.select2-enable').select2({
      placeholder: "Seleccione un material...",
      allowClear: true
    });

    // === Lógica para el formulario dinámico (MAXIMIZANDO LA COMPATIBILIDAD) ===
    const materialSelect = $('#IdMaterial');
    const grupoVehicular = $('#grupo-vehicular');
    const grupoInterno = $('#grupo-interno');
    const grupoInsumos = $('#grupo-insumos');
    const idsVehicular = ['1', '2', '3', '14', '15', '16', '17', '18'];
    const idsInterno = ['13'];
    const allGroups = [grupoVehicular, grupoInterno, grupoInsumos];

    function toggleFields() {
      // 1. Validar que el elemento exista
      if (!materialSelect.length) return;

      const selectedId = materialSelect.val();
      let grupoActivo = null;

      // 2. Determinar el grupo activo
      if (idsVehicular.includes(selectedId)) {
        grupoActivo = grupoVehicular;
      } else if (idsInterno.includes(selectedId)) {
        grupoActivo = grupoInterno;
      } else if (selectedId) {
        // Cualquier otro material usa Insumos
        grupoActivo = grupoInsumos;
      }

      // 3. Iterar sobre todos los grupos para aplicar el estado
      allGroups.forEach(group => {
        if (group.length) {
          // Comparamos el ID del grupo con el ID del grupo activo
          const isActive = (group.attr('id') === grupoActivo?.attr('id'));

          if (isActive) {
            // Mostrar el grupo activo
            group.css('display', 'flex');
            // HABILITAR sus inputs para que se envíen
            group.find('input').prop('disabled', false);
          } else {
            // Ocultar el grupo inactivo
            group.hide();
            // DESHABILITAR sus inputs para que NO se envíen (esto es crucial)
            group.find('input').prop('disabled', true);
          }
        }
      });
    }

    materialSelect.on('change', toggleFields);

    // Llamada inicial para aplicar la lógica al cargar con old()
    toggleFields();
    // === FIN Lógica formulario dinámico ===


    // === Lógica para la actualización en vivo de la lista de espera ===
    const tablaEsperaBody = document.getElementById('tabla-espera-body');
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

    async function actualizarListaEspera() {
      // ... (Tu código de actualización en vivo sin cambios) ...
      try {
        const scrollParent = tablaEsperaBody?.parentElement;
        const scrollPos = scrollParent ? scrollParent.scrollTop : 0;
        const response = await fetch("{{ route('api.muestras.pendientes') }}");
        if (!response.ok) throw new Error(`Error en la respuesta del servidor: ${response.statusText}`);
        const muestras = await response.json();
        if (tablaEsperaBody) tablaEsperaBody.innerHTML = '';
        if (!tablaEsperaBody) return;

        if (muestras.length === 0) {
          tablaEsperaBody.innerHTML =
            `<tr><td colspan="6" class="text-center text-muted py-5"><i class="fas fa-check-circle fa-2x mb-2"></i><p class="mb-0">¡Excelente! No hay muestras pendientes por analizar.</p></td></tr>`;
        } else {
          muestras.forEach(muestra => {
            const fechaRegistro = muestra.FechaRegistro ? new Date(muestra.FechaRegistro).toLocaleString(
              'es-MX', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
              }) : 'N/A';
            const materialNombre = muestra.material?.Material ?? 'N/A';
            const proveedorSolicitante = muestra.Proveedor || muestra.Solicitante || 'N/A';
            const usuarioNombre = muestra.usuario_oper?.username ?? 'N/A';
            const urlAnalizar = `/muestras/${muestra.IdMuestra}/analizar`;
            const urlRechazar = `/muestras/${muestra.IdMuestra}/rechazar`;
            const newRowHTML = `
               <tr>
                 <td><strong>${muestra.IdMuestra}</strong></td> <td>${materialNombre}</td> <td>${fechaRegistro}</td>
                 <td>${proveedorSolicitante}</td> <td>${usuarioNombre}</td>
                 <td>
                   <a href="${urlAnalizar}" class="btn btn-sm btn-outline-success px-3 me-1" title="Iniciar Análisis"><i class="fas fa-play me-1"></i> Analizar</a>
                   <form action="${urlRechazar}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas rechazar esta muestra?');">
                     <input type="hidden" name="_token" value="${csrfToken}"> <input type="hidden" name="_method" value="PATCH">
                     <button type="submit" class="btn btn-sm btn-outline-danger px-3" title="Rechazar Muestra"><i class="fas fa-times"></i></button>
                   </form>
                 </td>
               </tr>`;
            tablaEsperaBody.insertAdjacentHTML('beforeend', newRowHTML);
          });
        }
        if (scrollParent) {
          scrollParent.scrollTop = scrollPos;
        }
      } catch (error) {
        console.error('Error al actualizar la lista de espera:', error);
        if (tablaEsperaBody) {
          tablaEsperaBody.innerHTML =
            `<tr><td colspan="6" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle me-2"></i> No se pudo cargar la información.</td></tr>`;
        }
      }
    }

    if (tablaEsperaBody && csrfToken) {
      actualizarListaEspera();
      setInterval(actualizarListaEspera, 15000);
    } else {
      console.warn("Actualización en vivo desactivada (tabla o token no encontrados).");
    }
    // === FIN Lógica actualización en vivo ===


    // === LÓGICA MODAL IMPRESIÓN ===
    @if(session('show_print_modal') && session('print_label_url'))

    const printModalElement = document.getElementById('modalImprimirEtiqueta');
    // Asegúrate de que 'bootstrap' esté definido (asumiendo que se carga en app-layout)
    const printModal = new bootstrap.Modal(printModalElement);
    const printUrl = '{{ session('
    print_label_url ') }}';

    const btnSiImprimir = document.getElementById('btn-si-imprimir');
    if (btnSiImprimir) {
      btnSiImprimir.addEventListener('click', function() {
        window.open(printUrl, '_blank');
        printModal.hide();
      });
    }

    printModal.show();

    @endif
    // === FIN: LÓGICA MODAL IMPRESIÓN ===
  }); // Fin de $(document).ready
  </script>

</x-app-layout>