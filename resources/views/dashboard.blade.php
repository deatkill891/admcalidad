<x-app-layout>
  {{-- Incluir Font Awesome --}}
  <script src="https://kit.fontawesome.com/9d1bcc908a.js" crossorigin="anonymous"></script>

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
                {{-- Iterar sobre las muestras en espera --}}
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
                    {{-- Botón Analizar --}}
                    <a href="{{ route('muestras.analizar.form', $muestra) }}"
                      class="btn btn-sm btn-outline-success px-3 me-1" title="Iniciar Análisis">
                      <i class="fas fa-play me-1"></i> Analizar
                    </a>
                    {{-- Botón Rechazar (Formulario) --}}
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
                {{-- Mensaje si no hay muestras en espera --}}
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
      {{-- TARJETA: REGISTRAR NUEVA MUESTRA --}}
      <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4 p-md-5">
          <h5 class="fw-bold mb-3 text-dark border-bottom pb-3">
            <i class="fas fa-plus-circle me-2 text-primary"></i> Registrar Nueva Muestra
          </h5>

          {{-- Mensaje de éxito (si existe) --}}
          @if(session('success'))
          <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
          </div>
          @endif

          {{-- Formulario de registro --}}
          <form method="POST" action="{{ route('muestras.store') }}">
            @csrf
            <div class="row g-3">
              {{-- Selector de Material (ya filtrado por el controlador) --}}
              <div class="col-md-12">
                <label for="IdMaterial" class="form-label fw-semibold">Material <span
                    class="text-danger">*</span></label>
                <select id="IdMaterial" name="IdMaterial" class="form-select" required>
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

                {{-- Grupo Vehicular (Proveedor como input text) --}}
                <div id="grupo-vehicular" class="row g-3 m-0 p-0" style="display: none;">
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Proveedor</label>
                    <input type="text" class="form-control" name="Proveedor" value="{{ old('Proveedor') }}">
                  </div>
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

                {{-- Grupo Insumos (Proveedor como input text) --}}
                <div id="grupo-insumos" class="row g-3 m-0 p-0" style="display: none;">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Proveedor</label>
                    <input type="text" class="form-control" name="Proveedor" value="{{ old('Proveedor') }}">
                  </div>
                  <div class="col-md-6"><label class="form-label fw-semibold">Remisión</label><input type="text"
                      class="form-control" name="Remision" value="{{ old('Remision') }}"></div>
                </div>

              </div> {{-- Fin campos-dinamicos --}}
            </div> {{-- Fin row g-3 principal --}}

            {{-- Botón de Guardar --}}
            <div class="d-flex justify-content-end mt-4">
              <button type="submit" class="btn btn-primary fw-bold px-4 py-2">
                <i class="fas fa-save me-2"></i> Registrar Muestra
              </button>
            </div>
          </form>
        </div>
      </div>
      @endif {{-- Fin del @if($puedeRegistrar) --}}

      {{-- TABLA: MUESTRAS RECIENTES --}}
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
          <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-history me-2"></i> Muestras Recientes (Últimas 10)</h5>
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
              <thead class="table-dark">
                <tr>
                  <th>Folio</th>
                  <th>Material</th>
                  <th>Fecha Registro</th>
                  <th>Fecha Recibo</th>
                  <th>Estatus</th>
                  <th>Registró</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                {{-- Iterar sobre las muestras recientes --}}
                @forelse($muestrasRecientes as $muestra)
                <tr>
                  <td><strong>{{ $muestra->IdMuestra }}</strong></td>
                  <td>{{ $muestra->material->Material ?? 'N/A' }}</td>
                  <td>
                    {{ $muestra->FechaRegistro ? \Carbon\Carbon::parse($muestra->FechaRegistro)->format('d/m/Y H:i') : 'N/A' }}
                  </td>
                  <td>
                    {{ $muestra->FechaRecibo ? \Carbon\Carbon::parse($muestra->FechaRecibo)->format('d/m/Y H:i') : 'N/A' }}
                  </td>
                  <td>
                    {{-- Badge de estatus con color dinámico --}}
                    <span
                      class="badge rounded-pill bg-{{ $muestra->IdEstatusAnalisis == 1 ? 'warning text-dark' : ($muestra->IdEstatusAnalisis == 2 ? 'success' : 'secondary') }}">
                      {{ $muestra->estatusAnalisis->EstatusAnalisis ?? 'N/A' }}
                    </span>
                  </td>
                  <td>{{ $muestra->usuarioOper->username ?? 'N/A' }}</td>
                  {{-- Botón Ver Detalles (ejemplo) --}}
                  <td><a href="#" class="btn btn-sm btn-info" title="Ver Detalles"><i class="fas fa-eye"></i></a></td>
                </tr>
                @empty
                {{-- Mensaje si no hay muestras recientes --}}
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">No hay muestras registradas recientemente.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div> {{-- Fin container-fluid --}}
  </div> {{-- Fin py-4 --}}

  {{-- SCRIPT JAVASCRIPT --}}
  <script>
  document.addEventListener('DOMContentLoaded', function() {

    // === Lógica para el formulario dinámico (Simplificada para input text) ===
    const materialSelect = document.getElementById('IdMaterial'); // Puede ser null si el form está oculto
    const grupoVehicular = document.getElementById('grupo-vehicular');
    const grupoInterno = document.getElementById('grupo-interno');
    const grupoInsumos = document.getElementById('grupo-insumos');
    // IDs que activan el grupo vehicular
    const idsVehicular = ['1', '2', '3', '14', '15', '16', '17', '18'];
    // IDs que activan el grupo interno
    const idsInterno = ['13'];

    function toggleFields() {
      // Si el select de material no existe (porque el usuario no tiene permisos), no hacer nada.
      if (!materialSelect) return;

      const selectedId = materialSelect.value;

      // Ocultar siempre todos los grupos primero para empezar limpio
      if (grupoVehicular) grupoVehicular.style.display = 'none';
      if (grupoInterno) grupoInterno.style.display = 'none';
      if (grupoInsumos) grupoInsumos.style.display = 'none';

      // Deshabilitar todos los inputs dentro de los grupos dinámicos
      const dynamicInputs = document.querySelectorAll('#campos-dinamicos input');
      if (dynamicInputs.length > 0) {
        dynamicInputs.forEach(input => input.disabled = true);
      }

      let grupoActivo = null;
      // Determinar qué grupo mostrar
      if (idsVehicular.includes(selectedId)) {
        grupoActivo = grupoVehicular;
      } else if (idsInterno.includes(selectedId)) {
        grupoActivo = grupoInterno;
      } else if (selectedId) { // Si hay algo seleccionado y no es de los anteriores
        grupoActivo = grupoInsumos;
      }

      // Si se encontró un grupo para activar
      if (grupoActivo) {
        grupoActivo.style.display = 'flex'; // Mostrar el grupo usando flex
        // Habilitar solo los inputs DENTRO del grupo activo
        grupoActivo.querySelectorAll('input').forEach(input => input.disabled = false);
      }
    }

    // Añadir el listener solo si el select existe
    if (materialSelect) {
      materialSelect.addEventListener('change', toggleFields);
      // Ejecutar al cargar por si hay valores 'old' o preseleccionados
      toggleFields();
    }
    // === FIN Lógica formulario dinámico ===


    // === Lógica para la actualización en vivo de la lista de espera (Sin cambios) ===
    const tablaEsperaBody = document.getElementById('tabla-espera-body');
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

    async function actualizarListaEspera() {
      try {
        // Intentar obtener la posición del scroll (si el elemento padre existe)
        const scrollParent = tablaEsperaBody?.parentElement;
        const scrollPos = scrollParent ? scrollParent.scrollTop : 0;

        const response = await fetch("{{ route('api.muestras.pendientes') }}");
        if (!response.ok) throw new Error(`Error en la respuesta del servidor: ${response.statusText}`);

        const muestras = await response.json();
        if (tablaEsperaBody) tablaEsperaBody.innerHTML = ''; // Limpiar tabla solo si existe

        if (!tablaEsperaBody) return; // Salir si la tabla no existe en el DOM

        if (muestras.length === 0) {
          tablaEsperaBody.innerHTML = `
           <tr>
             <td colspan="6" class="text-center text-muted py-5">
               <i class="fas fa-check-circle fa-2x mb-2"></i>
               <p class="mb-0">¡Excelente! No hay muestras pendientes por analizar.</p>
             </td>
           </tr>`;
        } else {
          muestras.forEach(muestra => {
            // Formateo de fecha
            const fechaRegistro = muestra.FechaRegistro ?
              new Date(muestra.FechaRegistro).toLocaleString('es-MX', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
              }) :
              'N/A';

            // Obtener datos con valores por defecto seguros
            const materialNombre = muestra.material?.Material ?? 'N/A';
            const proveedorSolicitante = muestra.Proveedor || muestra.Solicitante || 'N/A';
            const usuarioNombre = muestra.usuario_oper?.username ?? 'N/A';
            // Construir URLs (asegúrate que las rutas coincidan con tu web.php)
            const urlAnalizar = `/muestras/${muestra.IdMuestra}/analizar`;
            const urlRechazar = `/muestras/${muestra.IdMuestra}/rechazar`;

            // Crear HTML de la fila
            const newRowHTML = `
             <tr>
               <td><strong>${muestra.IdMuestra}</strong></td>
               <td>${materialNombre}</td>
               <td>${fechaRegistro}</td>
               <td>${proveedorSolicitante}</td>
               <td>${usuarioNombre}</td>
               <td>
                 <a href="${urlAnalizar}" class="btn btn-sm btn-primary" title="Iniciar Análisis">
                   <i class="fas fa-play me-1"></i> Analizar
                 </a>
                 <form action="${urlRechazar}" method="POST" class="d-inline"
                   onsubmit="return confirm('¿Estás seguro de que deseas rechazar esta muestra?');">
                   <input type="hidden" name="_token" value="${csrfToken}">
                   <input type="hidden" name="_method" value="PATCH">
                   <button type="submit" class="btn btn-sm btn-danger" title="Rechazar Muestra">
                     <i class="fas fa-times"></i>
                   </button>
                 </form>
               </td>
             </tr>`;
            // Añadir fila a la tabla
            tablaEsperaBody.insertAdjacentHTML('beforeend', newRowHTML);
          });
        }

        // Restaurar scroll si el elemento padre existía
        if (scrollParent) {
          scrollParent.scrollTop = scrollPos;
        }

      } catch (error) {
        console.error('Error al actualizar la lista de espera:', error);
        // Mostrar error en la tabla solo si existe
        if (tablaEsperaBody) {
          tablaEsperaBody.innerHTML = `
            <tr>
              <td colspan="6" class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No se pudo cargar la información. Verifica la consola para más detalles.
              </td>
            </tr>`;
        }
      }
    }

    // Iniciar actualización solo si los elementos necesarios existen
    if (tablaEsperaBody && csrfToken) {
      actualizarListaEspera(); // Llamada inicial
      setInterval(actualizarListaEspera, 15000); // Repetir cada 15 segundos
    } else {
      if (!tablaEsperaBody) console.error("Elemento 'tabla-espera-body' no encontrado.");
      if (!csrfToken) console.error("Token CSRF no encontrado. Asegúrate que <meta name='csrf-token'> existe.");
      console.warn("La actualización en vivo de la lista de espera está desactivada.");
    }
    // === FIN Lógica actualización en vivo ===
  });
  </script>

</x-app-layout>