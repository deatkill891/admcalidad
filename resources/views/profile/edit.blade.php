<x-app-layout>
  <x-slot name="header">
    <h2 class="h4 fw-bold text-light text-shadow">
      Perfil
    </h2>
  </x-slot>

  <div class="row justify-content-center">

    <div class="col-lg-10 col-xl-12">

      <div class="bg-white rounded-4 shadow-sm p-4">
        <div class="d-grid gap-5">

          <div class="p-3 border rounded-3">
            @include('profile.partials.update-profile-information-form')
          </div>

          <div class="p-3 border rounded-3">
            @include('profile.partials.update-password-form')
          </div>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>