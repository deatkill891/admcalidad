<x-app-layout>
  <script src="https://kit.fontawesome.com/9d1bcc908a.js" crossorigin="anonymous"></script>

  <x-slot name="header">
    <h2 class="h4 fw-semibold text-dark">
      {{ __('Crear Nuevo Usuario') }}
    </h2>
  </x-slot>

  <div class="py-4">
    <div class="container-fluid">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4 p-md-5">

          <div class="mb-4">
            <button type="button" class="btn btn-outline-secondary fw-bold" onclick="window.history.back()">
              <i class="fas fa-arrow-left me-2"></i> Volver a Usuarios
            </button>
          </div>
          <h5 class="fw-bold mb-3 text-dark border-top pt-3">
            <i class="fas fa-info-circle me-2"></i> Crear Nuevo Usuario
          </h5>

          <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf

            <div class="mb-3">
              <label for="username" class="form-label fw-semibold">Nombre de Usuario</label>
              <input id="username" class="form-control" type="text" name="username" value="{{ old('username') }}"
                required autofocus />
              <x-input-error :messages="$errors->get('username')" class="mt-2 text-danger" />
            </div>

            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email</label>
              <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required />
              <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="IdUbicacion" class="form-label fw-semibold">Ubicación</label>
                <select id="IdUbicacion" name="IdUbicacion" class="form-select" required>
                  <option value="" disabled selected>Seleccione una ubicación...</option>
                  @foreach($ubicaciones as $ubicacion)
                  <option value="{{ $ubicacion->IdUbicacion }}"
                    {{ old('IdUbicacion') == $ubicacion->IdUbicacion ? 'selected' : '' }}>
                    {{ $ubicacion->Ubicacion }}
                  </option>
                  @endforeach
                </select>
                <x-input-error :messages="$errors->get('IdUbicacion')" class="mt-2 text-danger" />
              </div>

              <div class="col-md-6 mb-3">
                <label for="IdTipoUsuario" class="form-label fw-semibold">Tipo de Usuario</label>
                <select id="IdTipoUsuario" name="IdTipoUsuario" class="form-select" required>
                  <option value="" disabled selected>Seleccione un tipo...</option>
                  @foreach($tiposUsuario as $tipo)
                  <option value="{{ $tipo->IdTipoUsuario }}"
                    {{ old('IdTipoUsuario') == $tipo->IdTipoUsuario ? 'selected' : '' }}>
                    {{ $tipo->TipoUsuario }}
                  </option>
                  @endforeach
                </select>
                <x-input-error :messages="$errors->get('IdTipoUsuario')" class="mt-2 text-danger" />
              </div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label fw-semibold">Contraseña</label>
              <input id="password" class="form-control" type="password" name="password" required
                autocomplete="new-password" />
              <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
            </div>

            <div class="mb-4">
              <label for="password_confirmation" class="form-label fw-semibold">Confirmar Contraseña</label>
              <input id="password_confirmation" class="form-control" type="password" name="password_confirmation"
                required />
              <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger" />
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary fw-bold btn-lg">
                <i class="fas fa-user-plus me-2"></i> {{ __('Crear Usuario') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>