<x-app-layout>
  <script src="https://kit.fontawesome.com/9d1bcc908a.js" crossorigin="anonymous"></script>

  <x-slot name="header">
    <h2 class="h4 fw-semibold text-dark">
      {{ __('Inicio y Registro de Muestras') }}
    </h2>
  </x-slot>

  <div class="py-4">
    <div class="container-fluid">

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

              <div id="campos-dinamicos" class="row g-3 m-0 p-0">

                <div id="grupo-vehicular" class="row g-3 m-0 p-0" style="display: none;">
                  <div class="col-md-4">
                    <label for="proveedor_vehicular" class="form-label fw-semibold">Proveedor</label>
                    <select id="proveedor_vehicular" name="Proveedor" class="form-select proveedor-select" disabled>
                      <option value="" selected disabled>Seleccione un material primero...</option>
                    </select>
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

                <div id="grupo-insumos" class="row g-3 m-0 p-0" style="display: none;">
                  <div class="col-md-6">
                    <label for="proveedor_insumos" class="form-label fw-semibold">Proveedor</label>
                    <select id="proveedor_insumos" name="Proveedor" class="form-select proveedor-select" disabled>
                      <option value="" selected disabled>Seleccione un material primero...</option>
                    </select>
                  </div>
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
                    <span
                      class="badge rounded-pill bg-{{ $muestra->IdEstatusAnalisis == 1 ? 'warning text-dark' : ($muestra->IdEstatusAnalisis == 2 ? 'success' : 'secondary') }}">
                      {{ $muestra->estatusAnalisis->EstatusAnalisis ?? 'N/A' }}
                    </span>
                  </td>
                  <td>{{ $muestra->usuarioOper->username ?? 'N/A' }}</td>
                  <td><a href="#" class="btn btn-sm btn-info" title="Ver Detalles"><i class="fas fa-eye"></i></a></td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">No hay muestras registradas recientemente.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {

    // === Lógica para el formulario dinámico (MODIFICADA) ===

    // 1. Pasamos los datos de Laravel (con proveedores) a JavaScript
    // Usamos keyBy para un acceso más fácil (ej: materialesData['1'])
    const materialesData = @json($materiales - > keyBy('IdMaterial'));

    // 2. Referencias a los elementos del DOM
    const materialSelect = document.getElementById('IdMaterial');
    const grupoVehicular = document.getElementById('grupo-vehicular');
    const grupoInterno = document.getElementById('grupo-interno');
    const grupoInsumos = document.getElementById('grupo-insumos');
    // Obtenemos AMBOS selects de proveedor
    const proveedorSelects = document.querySelectorAll('.proveedor-select');

    // 3. IDs para la lógica de visualización
    const idsVehicular = ['1', '2', '3', '14', '15', '16', '17', '18'];
    const idsInterno = ['13'];

    function toggleFields() {
      const selectedId = materialSelect.value;

      // Ocultar todos los grupos
      grupoVehicular.style.display = 'none';
      grupoInterno.style.display = 'none';
      grupoInsumos.style.display = 'none';

      // Deshabilitar TODOS los campos (inputs Y selects) en los grupos dinámicos
      document.querySelectorAll('#campos-dinamicos input, #campos-dinamicos select').forEach(field => field
        .disabled = true);

      // Limpiar y reiniciar todos los selects de proveedor
      proveedorSelects.forEach(select => {
        select.innerHTML = '<option value="" selected disabled>Seleccione un material primero...</option>';
      });

      // Encontrar el grupo que debe estar activo
      let grupoActivo = null;
      if (idsVehicular.includes(selectedId)) {
        grupoActivo = grupoVehicular;
      } else if (idsInterno.includes(selectedId)) {
        grupoActivo = grupoInterno;
      } else if (selectedId) { // Cualquier otro material que no sea vehicular o interno
        grupoActivo = grupoInsumos;
      }

      // Si encontramos un grupo para activar
      if (grupoActivo) {
        // Mostrar el grupo
        grupoActivo.style.display = 'flex';

        // Habilitar todos los 'input' dentro de ese grupo
        grupoActivo.querySelectorAll('input').forEach(input => input.disabled = false);

        // --- INICIO NUEVA LÓGICA DE PROVEEDORES ---

        // Buscar si este grupo activo tiene un select de proveedor
        const activeProveedorSelect = grupoActivo.querySelector('.proveedor-select');

        if (activeProveedorSelect) {
          const material = materialesData[selectedId];

          // Verificar si el material tiene proveedores y si la lista no está vacía
          if (material && material.proveedores && material.proveedores.length > 0) {

            activeProveedorSelect.innerHTML =
              '<option value="" selected disabled>Seleccione un proveedor...</option>';
            activeProveedorSelect.disabled = false;

            // Poblar el select con los proveedores
            material.proveedores.forEach(proveedor => {
              const option = document.createElement('option');

              // Usamos la columna 'Proveedor' (el varchar) como valor Y como texto
              // según la estructura de tu tabla CatProveedores
              option.value = proveedor.Proveedor;
              option.textContent = proveedor.Proveedor;

              activeProveedorSelect.appendChild(option);
            });

          } else {
            // El material no tiene proveedores, mostrar mensaje y mantener deshabilitado
            activeProveedorSelect.innerHTML =
              '<option value="" selected disabled>Material sin proveedores...</option>';
            activeProveedorSelect.disabled = true;
          }
        } else {
          // Si el grupo no tiene .proveedor-select (ej: grupo-interno),
          // habilitamos cualquier otro select que pudiera tener.
          grupoActivo.querySelectorAll('select').forEach(select => select.disabled = false);
        }
        // --- FIN NUEVA LÓGICA DE PROVEEDORES ---
      }
    }

    // Asignar el evento y ejecutar al cargar
    materialSelect.addEventListener('change', toggleFields);
    toggleFields();
    // === FIN LÓGICA FORMULARIO DINÁMICO ===


    // === Lógica para la actualización en vivo de la lista de espera (código original del usuario) ===
    const tablaEsperaBody = document.getElementById('tabla-espera-body');
    // Asegúrate de tener el meta tag CSRF en tu layout (ej: <meta name="csrf-token" content="{{ csrf_token() }}">)
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function actualizarListaEspera() {
      try {
        // Guardar posición de scroll actual
        const scrollPos = tablaEsperaBody.parentElement.scrollTop;

        const response = await fetch("{{ route('api.muestras.pendientes') }}");
        if (!response.ok) throw new Error('Error en la respuesta del servidor.');

        const muestras = await response.json();
        tablaEsperaBody.innerHTML = '';

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
            const fechaRegistro = new Date(muestra.FechaRegistro).toLocaleString('es-MX', {
              day: '2-digit',
              month: '2-digit',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
              hour12: true
            });

            const newRowHTML = `
            <tr>
              <td><strong>${muestra.IdMuestra}</strong></td>
              <td>${muestra.material ? muestra.material.Material : 'N/A'}</td>
              <td>${fechaRegistro}</td>
              <td>${muestra.Proveedor || muestra.Solicitante || 'N/A'}</td>
              <td>${muestra.usuario_oper ? muestra.usuario_oper.username : 'N/A'}</td>
              <td>
                <a href="/muestras/${muestra.IdMuestra}/analizar" class="btn btn-sm btn-primary" title="Iniciar Análisis">
                  <i class="fas fa-play me-1"></i> Analizar
                </a>
                <form action="/muestras/${muestra.IdMuestra}/rechazar" method="POST" class="d-inline"
                  onsubmit="return confirm('¿Estás seguro de que deseas rechazar esta muestra?');">
                  <input type="hidden" name="_token" value="${csrfToken}">
                  <input type="hidden" name="_method" value="PATCH">
                  <button type="submit" class="btn btn-sm btn-danger" title="Rechazar Muestra">
                    <i class="fas fa-times"></i>
                  </button>
                </form>
              </td>
            </tr>`;
            tablaEsperaBody.insertAdjacentHTML('beforeend', newRowHTML);
          });
        }

        // Restaurar posición del scroll
        tablaEsperaBody.parentElement.scrollTop = scrollPos;

      } catch (error) {
        console.error('Error al actualizar la lista de espera:', error);
        tablaEsperaBody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center text-danger py-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No se pudo cargar la información.
          </td>
        </tr>`;
      }
    }

    // Ejecutar una vez al cargar
    actualizarListaEspera();

    // Actualizar cada 15 segundos
    setInterval(actualizarListaEspera, 15000);
  });
  </script>
</x-app-layout>