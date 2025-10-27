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

          {{-- Mensaje de éxito --}}
          @if(session('success'))
          <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
          </div>
          @endif

          {{-- ** INICIO: Bloque para mostrar TODOS los errores de validación ** --}}
          @if ($errors->any())
          <div class="alert alert-danger mb-4" role="alert">
            <h6 class="alert-heading fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> ¡Error de Validación!
            </h6>
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif
          {{-- ** FIN: Bloque para mostrar TODOS los errores de validación ** --}}

          <form method="POST" action="{{ route('muestras.store') }}">
            @csrf
            <div class="row g-3">

              {{-- Campo Material --}}
              <div class="col-md-4">
                <label for="IdMaterial" class="form-label fw-semibold">Material <span
                    class="text-danger">*</span></label>
                {{-- ** Añadida clase @error ** --}}
                <select id="IdMaterial" name="IdMaterial" class="form-select @error('IdMaterial') is-invalid @enderror"
                  required>
                  <option value="" disabled {{ old('IdMaterial') ? '' : 'selected' }}>Seleccione un material...</option>
                  @foreach($materiales as $material)
                  <option value="{{ $material->IdMaterial }}"
                    {{ old('IdMaterial') == $material->IdMaterial ? 'selected' : '' }}>
                    {{ $material->Material }} {{-- Asume que la columna se llama Material --}}
                  </option>
                  @endforeach
                </select>
                {{-- ** Añadido bloque @error ** --}}
                @error('IdMaterial')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>

              {{-- Campo Proveedor --}}
              <div class="col-md-4">
                <label for="Proveedor" class="form-label fw-semibold">Proveedor</label>
                {{-- ** Añadida clase @error ** --}}
                <input type="text" class="form-control @error('Proveedor') is-invalid @enderror" id="Proveedor"
                  name="Proveedor" value="{{ old('Proveedor') }}">
                {{-- ** Añadido bloque @error ** --}}
                @error('Proveedor')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>

              {{-- Campo Remisión --}}
              <div class="col-md-4">
                <label for="Remision" class="form-label fw-semibold">Remisión</label>
                {{-- ** Añadida clase @error ** --}}
                <input type="text" class="form-control @error('Remision') is-invalid @enderror" id="Remision"
                  name="Remision" value="{{ old('Remision') }}">
                {{-- ** Añadido bloque @error ** --}}
                @error('Remision')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>

              {{-- Campo Fecha Recibo --}}
              <div class="col-md-4">
                <label for="FechaRecibo" class="form-label fw-semibold">Fecha y Hora de Recibo <span
                    class="text-danger">*</span></label>
                {{-- ** Añadida clase @error ** --}}
                <input type="datetime-local" class="form-control @error('FechaRecibo') is-invalid @enderror"
                  id="FechaRecibo" name="FechaRecibo" value="{{ old('FechaRecibo', now()->format('Y-m-d\TH:i')) }}"
                  required>
                {{-- ** Añadido bloque @error (Aunque no tienes regla específica, es buena práctica si la añades después) ** --}}
                @error('FechaRecibo')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>

              {{-- Campo Placa Tractor --}}
              <div class="col-md-4">
                <label for="PlacaTractor" class="form-label fw-semibold">Placa Tractor</label>
                {{-- ** Añadida clase @error ** --}}
                <input type="text" class="form-control @error('PlacaTractor') is-invalid @enderror" id="PlacaTractor"
                  name="PlacaTractor" value="{{ old('PlacaTractor') }}">
                {{-- ** Añadido bloque @error ** --}}
                @error('PlacaTractor')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>

              {{-- Campo Placa Tolva --}}
              <div class="col-md-4">
                <label for="PlacaTolva" class="form-label fw-semibold">Placa Tolva</label>
                {{-- ** Añadida clase @error ** --}}
                <input type="text" class="form-control @error('PlacaTolva') is-invalid @enderror" id="PlacaTolva"
                  name="PlacaTolva" value="{{ old('PlacaTolva') }}">
                {{-- ** Añadido bloque @error ** --}}
                @error('PlacaTolva')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>

              {{-- Campo Tonelaje --}}
              <div class="col-md-4">
                <label for="Tonelaje" class="form-label fw-semibold">Tonelaje</label>
                {{-- ** Añadida clase @error ** --}}
                <input type="text" class="form-control @error('Tonelaje') is-invalid @enderror" id="Tonelaje"
                  name="Tonelaje" value="{{ old('Tonelaje') }}">
                {{-- ** Añadido bloque @error ** --}}
                @error('Tonelaje')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>

              {{-- Campo Solicitante --}}
              <div class="col-md-4">
                <label for="Solicitante" class="form-label fw-semibold">Solicitante</label>
                {{-- ** Añadida clase @error ** --}}
                <input type="text" class="form-control @error('Solicitante') is-invalid @enderror" id="Solicitante"
                  name="Solicitante" value="{{ old('Solicitante') }}">
                {{-- ** Añadido bloque @error ** --}}
                @error('Solicitante')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>

              {{-- Campo Área --}}
              <div class="col-md-4">
                <label for="Area" class="form-label fw-semibold">Área</label>
                {{-- ** Añadida clase @error ** --}}
                <input type="text" class="form-control @error('Area') is-invalid @enderror" id="Area" name="Area"
                  value="{{ old('Area') }}">
                {{-- ** Añadido bloque @error ** --}}
                @error('Area')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>

              {{-- Campo Identificación --}}
              <div class="col-md-4">
                <label for="Identificacion" class="form-label fw-semibold">Identificación</label>
                {{-- ** Añadida clase @error ** --}}
                <input type="text" class="form-control @error('Identificacion') is-invalid @enderror"
                  id="Identificacion" name="Identificacion" value="{{ old('Identificacion') }}">
                {{-- ** Añadido bloque @error ** --}}
                @error('Identificacion')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>

              {{-- Campo Análisis a realizar --}}
              <div class="col-md-8">
                <label for="Analisis" class="form-label fw-semibold">Análisis a realizar</label>
                {{-- ** Añadida clase @error ** --}}
                <input type="text" class="form-control @error('Analisis') is-invalid @enderror" id="Analisis"
                  name="Analisis" value="{{ old('Analisis') }}">
                {{-- ** Añadido bloque @error ** --}}
                @error('Analisis')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>
            </div> {{-- Fin .row --}}

            <div class="d-flex justify-content-end mt-4">
              <button type="submit" class="btn btn-primary fw-bold px-4 py-2">
                <i class="fas fa-save me-2"></i> Registrar Muestra
              </button>
            </div>
          </form>
        </div> {{-- Fin .card-body --}}
      </div> {{-- Fin .card (Formulario) --}}

      {{-- Tabla de Muestras Recientes (sin cambios) --}}
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
                    {{-- Lógica para mostrar badge según estatus --}}
                    @php
                    $badgeClass = 'bg-secondary'; // Default
                    if ($muestra->IdEstatusAnalisis == 1) $badgeClass = 'bg-warning text-dark';
                    elseif ($muestra->IdEstatusAnalisis == 2) $badgeClass = 'bg-success';
                    // Añade más casos si tienes otros estatus
                    @endphp
                    <span class="badge rounded-pill {{ $badgeClass }}">
                      {{ $muestra->estatusAnalisis->EstatusAnalisis ?? 'N/A' }}
                      {{-- Asume relación estatusAnalisis y campo EstatusAnalisis --}}
                    </span>
                  </td>
                  {{-- Verifica que la relación se llame usuarioOper y el campo sea username --}}
                  <td>{{ $muestra->usuarioOper->username ?? 'N/A' }}</td>
                  <td>
                    {{-- Enlace de ejemplo, ajusta la ruta si tienes una vista de detalles --}}
                    {{-- <a href="{{ route('muestras.show', $muestra->IdMuestra) }}" class="btn btn-sm btn-info"
                    title="Ver Detalles"><i class="fas fa-eye"></i></a> --}}
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
        </div> {{-- Fin .card-body --}}
      </div> {{-- Fin .card (Tabla) --}}

    </div> {{-- Fin .container-fluid --}}
  </div> {{-- Fin .py-4 --}}
</x-app-layout>