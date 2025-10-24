<x-app-layout>
  <script src="https://kit.fontawesome.com/9d1bcc908a.js" crossorigin="anonymous"></script>

  <x-slot name="header">
    <h2 class="h4 fw-semibold text-dark">
      Análisis de Muestra: Folio #{{ $muestra->IdMuestra }}
    </h2>
  </x-slot>

  <div class="py-4">
    <div class="container-fluid">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4 p-md-5">

          <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary fw-bold">
              <i class="fas fa-home me-2"></i> Regresar al Dashboard
            </a>
            <div id="weather-info" class="text-muted text-end">
              <i class="fas fa-spinner fa-spin me-2"></i> Obteniendo clima...
            </div>
          </div>

          <h5 class="fw-bold mb-3 text-dark border-bottom pb-3">
            <i class="fas fa-info-circle me-2"></i> Datos de la Muestra
          </h5>
          <div class="row mb-4">
            <div class="col-md-4"><strong>Folio:</strong> {{ $muestra->IdMuestra }}</div>
            <div class="col-md-4"><strong>Material:</strong> {{ $muestra->material->Material ?? 'N/A' }}</div>
            <div class="col-md-4"><strong>Fecha de Recibo:</strong>
              {{ \Carbon\Carbon::parse($muestra->FechaRecibo)->format('d/m/Y H:i') }}</div>
            <div class="col-md-4"><strong>Proveedor:</strong> {{ $muestra->Proveedor ?? 'N/A' }}</div>
            <div class="col-md-4"><strong>Remisión:</strong> {{ $muestra->Remision ?? 'N/A' }}</div>
          </div>

          <h5 class="fw-bold mb-3 text-dark border-top pt-3">
            <i class="fas fa-vials me-2"></i> Captura de Resultados
          </h5>

          @if ($errors->any())
          <div class="alert alert-danger">
            <h6 class="fw-bold">Por favor, corrige los siguientes errores:</h6>
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif

          <form method="POST" action="{{ route('muestras.analizar.store', $muestra) }}">
            @csrf

            <input type="hidden" id="clima" name="clima">
            <input type="hidden" id="humedad" name="humedad">

            <div class="row g-3">
              @forelse($elementos_a_analizar as $elemento)
              <div class="col-md-4">
                <label for="elemento-{{ $elemento['IdElemento'] }}" class="form-label fw-semibold">
                  {{ $elemento['Nombre'] }}
                </label>
                <input type="number" step="any" class="form-control analysis-input"
                  id="elemento-{{ $elemento['IdElemento'] }}" name="resultados[{{ $elemento['IdElemento'] }}][valor]"
                  placeholder="Límites: {{ $elemento['ValMin'] }} - {{ $elemento['ValMax'] }}"
                  data-min="{{ $elemento['ValMin'] }}" data-max="{{ $elemento['ValMax'] }}"
                  min="{{ $elemento['ValMin'] }}" max="{{ $elemento['ValMax'] }}"
                  value="{{ old('resultados.' . $elemento['IdElemento'] . '.valor') }}" required>
                <div class="invalid-feedback">
                  Valor fuera de rango (Debe estar entre {{ $elemento['ValMin'] }} y {{ $elemento['ValMax'] }}).
                </div>
              </div>
              @empty
              <div class="col-12">
                <div class="alert alert-warning">
                  No hay elementos de análisis configurados para este material (según la lógica del controlador).
                </div>
              </div>
              @endforelse
            </div>
            @if(!empty($elementos_a_analizar))
            <div class="d-flex justify-content-end mt-4">
              <button type="submit" class="btn btn-success fw-bold px-4 py-2">
                <i class="fas fa-save me-2"></i> Guardar Análisis
              </button>
            </div>
            @endif
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // --- Validacion de límites (Sin cambios) ---
    document.querySelectorAll('.analysis-input').forEach(input => {
      input.addEventListener('input', function(e) {
        const value = parseFloat(e.target.value);
        const min = parseFloat(e.target.dataset.min);
        const max = parseFloat(e.target.dataset.max);
        if (e.target.value.length > 0) {
          if (value < min || value > max) {
            e.target.classList.add('is-invalid');
          } else {
            e.target.classList.remove('is-invalid');
          }
        } else {
          e.target.classList.remove('is-invalid');
        }
      });
    });

    // --- SCRIPT PARA OBTENER EL CLIMA (Sin cambios) ---
    const apiKey = '{{ $openWeatherApiKey }}'; // Obtenemos la key del controlador
    const weatherInfo = document.getElementById('weather-info');
    const climaInput = document.getElementById('clima');
    const humedadInput = document.getElementById('humedad');

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(async (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        const lang = 'es';
        const units = 'metric'; // Para grados Centígrados
        const url =
          `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=${units}&lang=${lang}`;

        try {
          const response = await fetch(url);
          const data = await response.json();

          const climaDesc = data.weather[0].description;
          const temp = data.main.temp;
          const humedad = data.main.humidity;

          // Formateamos el texto
          const climaTexto = `${climaDesc.charAt(0).toUpperCase() + climaDesc.slice(1)}, ${temp}°C`;
          const humedadTexto = `${humedad}%`;

          // Llenamos los campos ocultos
          climaInput.value = climaTexto;
          humedadInput.value = humedadTexto;

          // Mostramos al usuario
          weatherInfo.innerHTML =
            `<i class="fas fa-sun me-1" style="color: #ffc107;"></i> ${climaTexto} | <i class="fas fa-tint me-1" style="color: #0dcaf0;"></i> ${humedadTexto} Humedad`;

        } catch (error) {
          console.error("Error al obtener el clima:", error);
          weatherInfo.innerHTML =
            `<i class="fas fa-exclamation-triangle text-danger me-2"></i> No se pudo obtener el clima.`;
          climaInput.value = 'Error';
          humedadInput.value = 'Error';
        }
      }, () => {
        // El usuario denegó el permiso
        weatherInfo.innerHTML =
          `<i class="fas fa-map-marker-slash text-danger me-2"></i> Permiso de ubicación denegado.`;
        climaInput.value = 'Permiso denegado';
        humedadInput.value = 'Permiso denegado';
      });
    } else {
      // El navegador no soporta geolocalización
      weatherInfo.innerHTML =
        `<i class="fas fa-exclamation-triangle text-warning me-2"></i> Geolocalización no soportada.`;
      climaInput.value = 'Navegador no compatible';
      humedadInput.value = 'Navegador no compatible';
    }
  });
  </script>
</x-app-layout>