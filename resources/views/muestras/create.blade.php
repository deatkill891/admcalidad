<x-app-layout>
  <script src="https://kit.fontawesome.com/9d1bcc908a.js" crossorigin="anonymous"></script>

  <x-slot name="header">
    <h2 class="h4 fw-semibold text-dark">
      {{ __('Registro de Muestras') }}
    </h2>
  </x-slot>

  <div class="py-4">
    <div class="container-fluid">

      <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4 p-md-5">
          <h5 class="fw-bold mb-3 text-dark border-bottom pb-3">
            <i class="fas fa-plus-circle me-2 text-primary"></i> Nueva Muestra
          </h5>

          @if(session('success'))
          <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
          </div>
          @endif

          <form method="POST" action="{{ route('muestras.store') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-4">
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

              <div class="col-md-4">
                <label for="Proveedor" class="form-label fw-semibold">Proveedor</label>
                <input type="text" class="form-control" id="Proveedor" name="Proveedor" value="{{ old('Proveedor') }}">
              </div>

              <div class="col-md-4">
                <label for="Remision" class="form-label fw-semibold">Remisión</label>
                <input type="text" class="form-control" id="Remision" name="Remision" value="{{ old('Remision') }}">
              </div>

              <div class="col-md-4">
                <label for="FechaRecibo" class="form-label fw-semibold">Fecha y Hora de Recibo <span
                    class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" id="FechaRecibo" name="FechaRecibo"
                  value="{{ old('FechaRecibo', now()->format('Y-m-d\TH:i')) }}" required>
              </div>

              <div class="col-md-4">
                <label for="PlacaTractor" class="form-label fw-semibold">Placa Tractor</label>
                <input type="text" class="form-control" id="PlacaTractor" name="PlacaTractor"
                  value="{{ old('PlacaTractor') }}">
              </div>

              <div class="col-md-4">
                <label for="PlacaTolva" class="form-label fw-semibold">Placa Tolva</label>
                <input type="text" class="form-control" id="PlacaTolva" name="PlacaTolva"
                  value="{{ old('PlacaTolva') }}">
              </div>

              <div class="col-md-4">
                <label for="Tonelaje" class="form-label fw-semibold">Tonelaje</label>
                <input type="text" class="form-control" id="Tonelaje" name="Tonelaje" value="{{ old('Tonelaje') }}">
              </div>

              <div class="col-md-4">
                <label for="Solicitante" class="form-label fw-semibold">Solicitante</label>
                <input type="text" class="form-control" id="Solicitante" name="Solicitante"
                  value="{{ old('Solicitante') }}">
              </div>

              <div class="col-md-4">
                <label for="Area" class="form-label fw-semibold">Área</label>
                <input type="text" class="form-control" id="Area" name="Area" value="{{ old('Area') }}">
              </div>

              <div class="col-md-4">
                <label for="Identificacion" class="form-label fw-semibold">Identificación</label>
                <input type="text" class="form-control" id="Identificacion" name="Identificacion"
                  value="{{ old('Identificacion') }}">
              </div>

              <div class="col-md-8">
                <label for="Analisis" class="form-label fw-semibold">Análisis a realizar</label>
                <input type="text" class="form-control" id="Analisis" name="Analisis" value="{{ old('Analisis') }}">
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
          <h5 class="fw-bold mb-3 text-dark">
            <i class="fas fa-history me-2"></i> Muestras Recientes
          </h5>
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
                @forelse($muestras as $muestra)
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
                  <td>
                    <a href="#" class="btn btn-sm btn-info" title="Ver Detalles"><i class="fas fa-eye"></i></a>
                  </td>
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
</x-app-layout>