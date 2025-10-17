<x-app-layout>
  <script src="https://kit.fontawesome.com/9d1bcc908a.js" crossorigin="anonymous"></script>

  <x-slot name="header">
    <h2 class="h4 fw-semibold text-dark">
      {{ __('Inicio') }}
    </h2>
  </x-slot>

  <div class="py-4">
    <div class="container-fluid">
      <div class="row g-4">

        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm border-0 rounded-3 text-center">
            <div class="card-body d-flex flex-column justify-content-center">
              <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
              <h5 class="card-title fw-bold">Registro de Muestras</h5>
              <p class="card-text text-muted">Crea nuevas solicitudes de análisis para diferentes materiales.</p>
              <a href="{{ route('muestras.create') }}" class="btn btn-primary mt-auto stretched-link">Acceder</a>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm border-0 rounded-3 text-center">
            <div class="card-body d-flex flex-column justify-content-center">
              <i class="fas fa-flask fa-3x text-success mb-3"></i>
              <h5 class="card-title fw-bold">Análisis de Muestras</h5>
              <p class="card-text text-muted">Procesa y captura los resultados de las muestras pendientes.</p>
              <a href="#" class="btn btn-success mt-auto stretched-link">Acceder</a>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm border-0 rounded-3 text-center">
            <div class="card-body d-flex flex-column justify-content-center">
              <i class="fas fa-truck-moving fa-3x text-info mb-3"></i>
              <h5 class="card-title fw-bold">Muestras en Patios</h5>
              <p class="card-text text-muted">Registra muestras directamente desde el área de patios.</p>
              <a href="#" class="btn btn-info mt-auto stretched-link text-white">Acceder</a>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm border-0 rounded-3 text-center">
            <div class="card-body d-flex flex-column justify-content-center">
              <i class="fas fa-industry fa-3x text-secondary mb-3"></i>
              <h5 class="card-title fw-bold">Control de Cal/Silos</h5>
              <p class="card-text text-muted">Gestión y control de la calidad en los silos de cal.</p>
              <a href="#" class="btn btn-secondary mt-auto stretched-link">Acceder</a>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm border-0 rounded-3 text-center">
            <div class="card-body d-flex flex-column justify-content-center">
              <i class="fas fa-wind fa-3x text-dark mb-3"></i>
              <h5 class="card-title fw-bold">APC's</h5>
              <p class="card-text text-muted">Control y análisis de los colectores de polvo.</p>
              <a href="#" class="btn btn-dark mt-auto stretched-link">Acceder</a>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm border-0 rounded-3 text-center">
            <div class="card-body d-flex flex-column justify-content-center">
              <i class="fas fa-camera-retro fa-3x text-primary mb-3"></i>
              <h5 class="card-title fw-bold">EOD's</h5>
              <p class="card-text text-muted">Evidencias del Día a Día para los tableros de operación.</p>
              <a href="#" class="btn btn-primary mt-auto stretched-link">Acceder</a>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm border-0 rounded-3 text-center">
            <div class="card-body d-flex flex-column justify-content-center">
              <i class="fas fa-chart-pie fa-3x text-danger mb-3"></i>
              <h5 class="card-title fw-bold">Reportes</h5>
              <p class="card-text text-muted">Visualiza los datos y resultados a través de reportes.</p>
              <a href="#" class="btn btn-danger mt-auto stretched-link">Acceder</a>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm border-0 rounded-3 text-center">
            <div class="card-body d-flex flex-column justify-content-center">
              <i class="fas fa-ruler-combined fa-3x text-warning mb-3"></i>
              <h5 class="card-title fw-bold">Metrología</h5>
              <p class="card-text text-muted">Administra el programa de calibración de equipos.</p>
              <a href="#" class="btn btn-warning mt-auto stretched-link text-white">Acceder</a>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm border-0 rounded-3 text-center">
            <div class="card-body d-flex flex-column justify-content-center">
              <i class="fas fa-camera fa-3x" style="color: #6f42c1;"></i>
              <h5 class="card-title fw-bold mt-3">Evidencias de Embarque</h5>
              <p class="card-text text-muted">Registro y consulta de evidencias fotográficas de embarques.</p>
              <a href="#" class="btn mt-auto stretched-link"
                style="background-color: #6f42c1; color: white;">Acceder</a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</x-app-layout>