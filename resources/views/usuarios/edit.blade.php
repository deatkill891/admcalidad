<x-app-layout>
  <script src="https://kit.fontawesome.com/9d1bcc908a.js" crossorigin="anónimo"></script>

  <x-slot name="header">
    <h2 class="h4 fw-semibold text-dark">
      {{ __('Editar Usuario: ') . $usuario->username }}
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
            <i class="fas fa-info-circle me-2"></i> Editar Información del Usuario
          </h5>

          <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <x-input-label for="username" :value="__('Nombre de Usuario')" class="form-label" />
              <x-text-input id="username" class="form-control" type="text" name="username"
                :value="old('username', $usuario->username)" required autofocus />
              <x-input-error :messages="$errors->get('username')" class="mt-2 text-danger" />
            </div>

            <div class="mb-3">
              <x-input-label for="email" :value="__('Email')" class="form-label" />
              <x-text-input id="email" class="form-control" type="email" name="email"
                :value="old('email', $usuario->email)" required />
              <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
            </div>

            <div class="mb-3">
              <x-input-label for="password" :value="__('Nueva Contraseña (dejar en blanco para no cambiar)')"
                class="form-label" />
              <x-text-input id="password" class="form-control" type="password" name="password"
                autocomplete="new-password" />
              <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
            </div>

            <div class="mb-4">
              <x-input-label for="password_confirmation" :value="__('Confirmar Nueva Contraseña')" class="form-label" />
              <x-text-input id="password_confirmation" class="form-control" type="password"
                name="password_confirmation" />
              <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger" />
            </div>

            <div class="d-flex justify-content-end">
              <x-primary-button class="btn btn-primary fw-bold">
                <i class="fas fa-save me-2"></i> {{ __('Actualizar Usuario') }}
              </x-primary-button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>