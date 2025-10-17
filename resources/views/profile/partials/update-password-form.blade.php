<section class="">
  <header>
    <h2 class="h5 fw-bold text-dark">
      Actualizar Contraseña
    </h2>

    <p class="mt-1 text-muted">
      Asegúrate de que tu cuenta utilice una contraseña larga y aleatoria para mantener la seguridad.
    </p>
  </header>

  <form method="post" action="{{ route('password.update') }}" class="mt-4 row g-3">
    @csrf
    @method('put')

    <div class="col-12">
      <x-input-label for="update_password_current_password" :value="'Contraseña Actual'" class="form-label" />
      <input id="update_password_current_password" name="current_password" type="password" class="form-control"
        autocomplete="current-password" />
      <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
    </div>

    <div class="col-12">
      <x-input-label for="update_password_password" :value="'Nueva Contraseña'" class="form-label" />
      <input id="update_password_password" name="password" type="password" class="form-control"
        autocomplete="new-password" />
      <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
    </div>

    <div class="col-12">
      <x-input-label for="update_password_password_confirmation" :value="'Confirmar Contraseña'" class="form-label" />
      <input id="update_password_password_confirmation" name="password_confirmation" type="password"
        class="form-control" autocomplete="new-password" />
      <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
    </div>

    <div class="d-flex align-items-center gap-3 mt-4">
      <button type="submit" class="btn btn-danger">
        Guardar
      </button>

      @if (session('status') === 'password-updated')
      <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
        class="text-sm text-muted mb-0">Guardado.</p>
      @endif
    </div>
  </form>
</section>