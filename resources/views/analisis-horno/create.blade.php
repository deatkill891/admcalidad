<x-app-layout>
  <x-slot name="header">
    <h2 class="h4 fw-semibold text-dark">
      {{ $titulo }}
    </h2>
  </x-slot>

  <div class="py-4">
    <div class="container-fluid">

      {{-- ================= FORMULARIO ================= --}}
      <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4 p-md-5">
          @if(session('success'))
          <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
          @endif

          @if ($errors->any())
          <div class="alert alert-danger">
            <h5 class="fw-bold">Errores encontrados:</h5>
            <ul class="mb-0">
              @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
          </div>
          @endif

          <form id="FormAnalisisHorno" method="POST" action="{{ route('analisis-horno.store') }}">
            @csrf
            <input type="hidden" id="tipoAnalisis" name="tipo" value="{{ $tipo }}">
            <h4 class="fw-bold mb-4 text-center text-success text-uppercase">
              Escorias ({{ $tipo }})
            </h4>

            {{-- === Fila 1 === --}}
            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <label for="Fecha" class="form-label fw-semibold">Fecha y Hora</label>
                <input type="datetime-local" class="form-control" id="Fecha" name="Fecha"
                  value="{{ old('Fecha', now()->format('Y-m-d\TH:i')) }}" required>
              </div>

              <div class="col-md-3">
                <label for="Tecnico" class="form-label fw-semibold">Técnico</label>
                <select id="Tecnico" name="Tecnico" class="form-select select2-enable" required>
                  <option value="" disabled selected>Seleccione un técnico...</option>
                  @foreach($tecnicos as $tecnico)
                  <option value="{{ $tecnico->NomTecnico }}">{{ $tecnico->NomTecnico }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-3"><label class="form-label">Horno</label><input type="text" class="form-control"
                  name="HORNO"></div>
              <div class="col-md-3"><label class="form-label">Turno</label><input type="text" class="form-control"
                  name="Turno"></div>
            </div>

            {{-- === Botón === --}}
            <div class="d-flex justify-content-end mt-4">
              <button type="submit" class="btn btn-primary fw-bold px-4 py-2"><i class="bi bi-save me-2"></i>
                Registrar</button>
            </div>
          </form>
        </div>
      </div>

      {{-- ================= TABLA ================= --}}
      <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4 p-md-5">
          <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-list-task me-2"></i>Listado de {{ $titulo }}</h5>
          <div class="table-responsive">
            <table id="tabla-analisis-horno" class="table table-striped table-hover w-100">
              <thead class="table-light">
                <tr>
                  <th>Acciones</th>
                  <th>ID</th>
                  <th>Fecha</th>
                  <th>Técnico</th>
                  <th>Horno</th>
                  <th>Turno</th>
                  <th>Colada</th>
                  <th>Grado</th>
                  <th>CaO</th>
                  <th>MgO</th>
                  <th>SiO2</th>
                  <th>Al2O3</th>
                  <th>MnO</th>
                  <th>FeO</th>
                  <th>S</th>
                  <th>IB3</th>
                  <th>IB4</th>
                  <th>TOTAL</th>
                  <th>Analista</th>
                </tr>
              </thead>
              @forelse($analisisRegistrados as $registro)
              <tr>
                {{-- ** INICIO: CAMBIO EN BOTONES DE ACCIÓN ** --}}
                @if(Auth::user() && Auth::user()->permiso && Auth::user()->permiso->Administrador == 1)
                <td class="text-center">
                  {{-- TODO: Añadir ruta de Edit --}}

                  {{-- Botón de Eliminar (ahora es un formulario) --}}
                  <form action="{{ route('analisis-horno.destroy', $registro) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('¿Estás seguro de que deseas eliminar el registro #{{ $registro->IdRegistro }}?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </form>
                </td>
                @endif
                {{-- ** FIN: CAMBIO EN BOTONES DE ACCIÓN ** --}}
                <td>{{ $registro->IdRegistro }}</td>
                <td>{{ $registro->Fecha ? Carbon\Carbon::parse($registro->Fecha)->format('d/m/Y H:i') : 'N/A' }}</td>
                <td>{{ $registro->Tecnico }}</td>
                <td>{{ $registro->HORNO }}</td>
                <td>{{ $registro->Turno }}</td>
                <td>{{ $registro->COLADA }}</td>
                <td>{{ $registro->GRADO }}</td>
                <td>{{ $registro->CaO }}</td>
                <td>{{ $registro->MgO }}</td>
                <td>{{ $registro->SiO2 }}</td>
                <td>{{ $registro->Al2O3 }}</td>
                <td>{{ $registro->MnO }}</td>
                <td>{{ $registro->FeO }}</td>
                <td>{{ $registro->S }}</td>
                <td>{{ $registro->IB3 }}</td>
                <td>{{ $registro->IB4 }}</td>
                <td>{{ $registro->TOTAL }}</td>
                <td>{{ $registro->NombreUsuario }}</td>
              </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ================= SCRIPTS ================= --}}
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- DataTables core + extensiones -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

  <script>
  $(document).ready(function() {
    // Inicializar Select2
    $('#Tecnico').select2({
      placeholder: "Seleccione un técnico...",
      theme: "bootstrap-5",
      width: '100%'
    });

    // Inicializar DataTable
    $('#tabla-analisis-horno').DataTable({
      responsive: true,
      scrollX: true,
      autoWidth: false,
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50, 100],
      dom: "<'row mb-3'<'col-md-6'l><'col-md-6'f>>" +
        "<'row mb-2'<'col-12'B>>" +
        "<'row'<'col-12'tr>>" +
        "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
      buttons: [{
          extend: 'copyHtml5',
          text: '<i class=\"bi bi-clipboard\"></i> Copiar',
          className: 'btn btn-secondary btn-sm'
        },
        {
          extend: 'excelHtml5',
          text: '<i class=\"bi bi-file-earmark-excel\"></i> Excel',
          className: 'btn btn-success btn-sm'
        },
        {
          extend: 'pdfHtml5',
          text: '<i class=\"bi bi-file-earmark-pdf\"></i> PDF',
          className: 'btn btn-danger btn-sm',
          orientation: 'landscape',
          pageSize: 'A4'
        },
        {
          extend: 'print',
          text: '<i class=\"bi bi-printer\"></i> Imprimir',
          className: 'btn btn-info btn-sm'
        }
      ],
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json",
        buttons: {
          copyTitle: 'Copiado al portapapeles',
          copySuccess: {
            _: '%d filas copiadas',
            1: '1 fila copiada'
          }
        }
      },
      order: [
        [1, 'desc']
      ]
    });
  });
  </script>
</x-app-layout>