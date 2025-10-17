<section class="">
  <header>
    <h2 class="h5 fw-bold text-dark">
      Información del Perfil
    </h2>

    <p class="mt-1 text-muted">
      Actualiza la información de tu cuenta y tu dirección de correo electrónico.
    </p>
  </header>

  <form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
  </form>

  <form method="post" action="{{ route('profile.update') }}" class="mt-4 row g-3">
    @csrf
    @method('patch')

    <div class="col-12">
      <x-input-label for="name" :value="'Nombre'" class="form-label" />
      <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required
        autofocus autocomplete="name" />
      <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div class="col-12">
      <x-input-label for="email" :value="'Correo Electrónico'" class="form-label" />
      <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required
        autocomplete="username" />
      <x-input-error class="mt-2" :messages="$errors->get('email')" />

      @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
      <div>
        <p class="text-sm mt-2 text-dark">
          Tu correo electrónico no ha sido verificado.

          <button form="send-verification" class="btn btn-sm btn-outline-secondary ms-1">
            Haz clic aquí para reenviar el correo de verificación.
          </button>
        </p>

        @if (session('status') === 'verification-link-sent')
        <p class="mt-2 fw-medium text-success">
          Se ha enviado un nuevo enlace de verificación a tu correo.
        </p>
        @endif
      </div>
      @endif
    </div>

    <div class="d-flex align-items-center gap-3 mt-4">
      <button type="submit" class="btn btn-danger">
        Guardar
      </button>

      @if (session('status') === 'profile-updated')
      <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
        class="text-sm text-muted mb-0">Guardado.</p>
      @endif
    </div>
  </form>
</section>