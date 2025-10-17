<section>
  <header>
    <h2 class="h5 fw-bold text-dark">
      {{ __('Eliminar cuenta') }}
    </h2>

    <p class="mt-1 text-muted">
      {{ __('Una vez que tu cuenta sea eliminada, todos sus recursos y datos se borrarán de forma permanente. Antes de eliminar tu cuenta, descarga cualquier información o dato que desees conservar.') }}
    </p>
  </header>

  <x-danger-button class="btn btn-danger mt-3" x-data=""
    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
    {{ __('Eliminar cuenta') }}
  </x-danger-button>

  <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <form method="post" action="{{ route('profile.destroy') }}" class="p-4 p-md-5 bg-light rounded-3">
      @csrf
      @method('delete')

      <h2 class="h5 fw-bold text-dark">
        {{ __('¿Estás seguro de que deseas eliminar tu cuenta?') }}
      </h2>

      <p class="mt-1 text-muted">
        {{ __('Una vez que tu cuenta sea eliminada, todos sus recursos y datos se borrarán de forma permanente. Ingresa tu contraseña para confirmar que deseas eliminar tu cuenta permanentemente.') }}
      </p>

      <div class="mt-3">
        <label for="password" class="form-label visually-hidden">{{ __('Contraseña') }}</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Contraseña') }}"
          style="max-width: 75%;" />
        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
      </div>

      <div class="mt-4 d-flex justify-content-end">
        <x-secondary-button class="btn btn-secondary me-2" x-on:click="$dispatch('close')">
          {{ __('Cancelar') }}
        </x-secondary-button>

        <x-danger-button class="btn btn-danger">
          {{ __('Eliminar cuenta') }}
        </x-danger-button>
      </div>
    </form>
  </x-modal>
</section>