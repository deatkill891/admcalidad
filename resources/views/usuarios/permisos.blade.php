<x-app-layout>
  <script src="https://kit.fontawesome.com/9d1bcc908a.js" crossorigin="anonymous"></script>

  <x-slot name="header">
    <h2 class="h4 fw-semibold text-dark">
      {{ __('Permisos para: ') . $usuario->username }}
    </h2>
  </x-slot>

  <div class="py-4">
    <div class="container-fluid">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4 p-md-5">

          {{-- Botón Volver --}}
          <div class="mb-4">
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary fw-bold">
              <i class="fas fa-arrow-left me-2"></i> Volver a Usuarios
            </a>
          </div>

          {{-- Tabla Información del Usuario --}}
          <h5 class="fw-bold mb-3 text-dark border-top pt-3">
            <i class="fas fa-info-circle me-2"></i> Información del Usuario
          </h5>
          <div class="table-responsive mb-5">
            <table class="table table-striped table-borderless align-middle shadow-sm rounded-3 overflow-hidden">
              <thead class="table-success text-white">
                <tr>
                  <th style="width: 50px;" class="fw-bold">#</th>
                  <th class="fw-bold">Nombre</th>
                  <th class="fw-bold">Correo</th>
                  <th class="fw-bold">Fecha de Alta</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{ $usuario->IdUsuario ?? '#' }}</td>
                  <td>{{ $usuario->username ?? 'N/A' }}</td>
                  <td>{{ $usuario->email ?? 'N/A' }}</td>
                  <td>
                    {{ $usuario->FechaRegistro ? \Carbon\Carbon::parse($usuario->FechaRegistro)->format('d-m-Y') : 'N/A' }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          {{-- Formulario de Permisos --}}
          <h5 class="fw-bold mb-3 text-dark">
            <i class="fas fa-lock me-2"></i> Permisos del Usuario
          </h5>

          <form method="POST" action="{{ route('usuarios.permissions.update', $usuario) }}">
            @csrf
            @method('PUT')

            {{-- Define la estructura y nombres de los permisos --}}
            @php
            // Corrección: Usar la relación en singular 'permiso'
            $permisoActual = $usuario->permiso;

            // Estructura de permisos para construir la tabla
            $grupos_permisos = [
            'Administración' => [
            ['columna_db' => 'Administrador', 'display' => 'Administrador'],
            ],
            'Operación' => [
            ['columna_db' => 'Metrologia', 'display' => 'Metrología'],
            ['columna_db' => 'Analisis', 'display' => 'Análisis'],
            ['columna_db' => 'Muestreo', 'display' => 'Muestreo Insumos'],
            ['columna_db' => 'Insumos', 'display' => 'Insumos'],
            ['columna_db' => 'PolvoZn', 'display' => 'Polvo Zn'],
            ['columna_db' => 'Chatarra', 'display' => 'Chatarra'],
            ],
            'Consultas' => [
            ['columna_db' => 'CqElementos', 'display' => 'CQ Elementos'],
            ['columna_db' => 'CqOxidos', 'display' => 'CQ Óxidos'],
            ['columna_db' => 'Eads', 'display' => 'Eads'],
            ['columna_db' => 'Evidencias', 'display' => 'Evidencias'],
            ]
            ];
            @endphp

            {{-- Tabla de Permisos con Checkboxes --}}
            <div class="table-responsive mb-3">
              <table class="table table-bordered align-middle text-center" style="min-width: 800px;">
                <thead>
                  {{-- Encabezados de Grupos --}}
                  <tr>
                    <th class="bg-warning text-white" colspan="{{ count($grupos_permisos['Administración']) }}">
                      <i class="fas fa-user-shield me-1"></i> Administración
                    </th>
                    <th style="background-color: #f7ff97;" colspan="{{ count($grupos_permisos['Operación']) }}">
                      <i class="fas fa-cogs me-1"></i> Operación
                    </th>
                    <th class="bg-success text-white" colspan="{{ count($grupos_permisos['Consultas']) }}">
                      <i class="fas fa-search me-1"></i> Consultas
                    </th>
                  </tr>
                  {{-- Encabezados de Permisos Individuales --}}
                  <tr class="fw-semibold text-muted text-uppercase">
                    @foreach($grupos_permisos as $grupo)
                    @foreach($grupo as $permiso)
                    <th scope="col">{{ $permiso['display'] }}</th>
                    @endforeach
                    @endforeach
                  </tr>
                </thead>
                <tbody>
                  {{-- Fila de Checkboxes --}}
                  <tr>
                    {{-- Iterar sobre los grupos y permisos para crear cada celda --}}
                    @foreach($grupos_permisos as $grupo)
                    @foreach($grupo as $permiso)
                    @php
                    // Nombre de la columna en la BD (ej. 'Muestreo')
                    $nombre_columna = $permiso['columna_db'];
                    // Verificar si el permiso está activo (es 1)
                    // Usamos $permisoActual (la relación corregida)
                    $isChecked = $permisoActual && ($permisoActual->$nombre_columna == 1);
                    @endphp
                    <td>
                      <div class="form-check form-switch d-flex justify-content-center">
                        {{-- Input Checkbox --}}
                        <input class="form-check-input" type="checkbox" role="switch" id="switch-{{ $nombre_columna }}"
                          name="{{ $nombre_columna }}" {{-- El 'name' debe coincidir con la columna DB --}} value="1"
                          {{-- El valor enviado si está marcado --}} {{-- Añadir 'checked' si $isChecked es true --}}
                          @if($isChecked) checked @endif>
                        {{-- Label oculto para accesibilidad --}}
                        <label class="form-check-label visually-hidden" for="switch-{{ $nombre_columna }}">
                          Permiso para {{ $permiso['display'] }}
                        </label>
                      </div>
                    </td>
                    @endforeach
                    @endforeach
                  </tr>
                </tbody>
              </table>
            </div>

            {{-- Botón Guardar Cambios --}}
            <div class="text-center mt-3">
              <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">
                <i class="fas fa-save me-2"></i> Guardar Cambios
              </button>
            </div>

            {{-- Advertencia sobre permisos de administrador --}}
            <div class="alert alert-danger text-center mt-3 p-2 fw-semibold" role="alert"
              style="color: #d9534f; background-color: #f2dede; border-color: #ebccd1;">
              Al asignar permisos de administrador, usted tendrá la responsabilidad de la integridad de la información
              del sistema.
            </div>
          </form>

        </div> {{-- Fin card-body --}}
      </div> {{-- Fin card --}}
    </div> {{-- Fin container-fluid --}}
  </div> {{-- Fin py-4 --}}
</x-app-layout>