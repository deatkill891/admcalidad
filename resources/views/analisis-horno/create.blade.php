<x-app-layout>
  <x-slot name="header">
    <h2 class="h4 fw-semibold text-dark">
      {{-- Título dinámico (Ej: "Fusión (HF)") --}}
      {{ $titulo }}
    </h2>
  </x-slot>

  <div class="py-4">
    <div class="container-fluid">
      <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4 p-md-5">

          {{-- Mostrar errores de validación --}}
          @if ($errors->any())
          <div class="alert alert-danger">
            <h5 class="fw-bold">Errores encontrados:</h5>
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif

          {{-- Mostrar mensaje de error general (si existe) --}}
          @if(session('error'))
          <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
          </div>
          @endif

          <form id="FormAnalisisHorno" method="POST" action="{{ route('analisis-horno.store') }}">
            @csrf
            {{-- Campo oculto para saber qué tipo de análisis estamos guardando --}}
            <input type="hidden" id="tipoAnalisis" name="tipo" value="{{ $tipo }}">

            {{-- Fila 1: Datos Generales --}}
            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <label for="Fecha" class="form-label fw-semibold">Fecha y Hora <span
                    class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" id="Fecha" name="Fecha"
                  value="{{ old('Fecha', now()->format('Y-m-d\TH:i')) }}" required>
              </div>

              <div class="col-md-3">
                <label for="Tecnico" class="form-label fw-semibold">Técnico <span class="text-danger">*</span></label>
                <select id="Tecnico" name="Tecnico" class="form-select" required>
                  <option value="" disabled selected>Seleccione un técnico...</option>
                  @foreach($tecnicos as $tecnico)
                  <option value="{{ $tecnico->NomTecnico }}"
                    {{ old('Tecnico') == $tecnico->NomTecnico ? 'selected' : '' }}>
                    {{ $tecnico->NomTecnico }}
                  </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-3">
                <label for="HORNO" class="form-label fw-semibold">Horno</label>
                <input type="text" class="form-control" id="HORNO" name="HORNO" value="{{ old('HORNO') }}">
              </div>

              <div class="col-md-3">
                <label for="Turno" class="form-label fw-semibold">Turno</label>
                <input type="text" class="form-control" id="Turno" name="Turno" value="{{ old('Turno') }}">
              </div>
            </div>

            {{-- Fila 2: Datos de Colada --}}
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label for="COLADA" class="form-label fw-semibold">Colada</label>
                <input type="text" class="form-control" id="COLADA" name="COLADA" value="{{ old('COLADA') }}">
              </div>
              <div class="col-md-6">
                <label for="GRADO" class="form-label fw-semibold">Grado</label>
                <input type="text" class="form-control" id="GRADO" name="GRADO" value="{{ old('GRADO') }}">
              </div>
            </div>

            {{-- Fila 3: Campos de Elementos Químicos --}}
            <h5 class="fw-bold mb-3 text-dark border-top pt-3">
              <i class="fas fa-flask-vial me-2"></i> Composición Química
            </h5>
            <div class="row g-3 mb-3">
              <div class="col-md-2">
                <label for="CaO" class="form-label">CaO</label>
                <input type="number" step="0.01" class="form-control calc-input" id="CaO" name="CaO"
                  value="{{ old('CaO') }}">
              </div>
              <div class="col-md-2">
                <label for="MgO" class="form-label">MgO</label>
                <input type="number" step="0.01" class="form-control calc-input" id="MgO" name="MgO"
                  value="{{ old('MgO') }}">
              </div>
              <div class="col-md-2">
                <label for="SiO2" class="form-label">SiO<sub>2</sub></label>
                <input type="number" step="0.01" class="form-control calc-input" id="SiO2" name="SiO2"
                  value="{{ old('SiO2') }}">
              </div>
              <div class="col-md-2">
                <label for="Al2O3" class="form-label">Al<sub>2</sub>O<sub>3</sub></label>
                <input type="number" step="0.01" class="form-control calc-input" id="Al2O3" name="Al2O3"
                  value="{{ old('Al2O3') }}">
              </div>
              <div class="col-md-2">
                <label for="MnO" class="form-label">MnO</label>
                <input type="number" step="0.01" class="form-control calc-input" id="MnO" name="MnO"
                  value="{{ old('MnO') }}">
              </div>
              <div class="col-md-2">
                <label for="FeO" class="form-label">FeO</label>
                <input type="number" step="0.01" class="form-control calc-input" id="FeO" name="FeO"
                  value="{{ old('FeO') }}">
              </div>
              <div class="col-md-2">
                <label for="S" class="form-label">S</label>
                <input type="number" step="0.01" class="form-control calc-input" id="S" name="S" value="{{ old('S') }}">
              </div>
            </div>

            {{-- Fila 4: Campos Condicionales (Solo para HF) --}}
            @if($tipo == 'hf')
            <h5 class="fw-bold mb-3 text-dark border-top pt-3">
              <i class="fas fa-weight-hanging me-2"></i> Adiciones (Fusión)
            </h5>
            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <label for="KgCalSiderurgica" class="form-label">Kg Cal Siderúrgica</label>
                <input type="number" step="0.01" class="form-control" id="KgCalSiderurgica" name="KgCalSiderurgica"
                  value="{{ old('KgCalSiderurgica') }}">
              </div>
              <div class="col-md-3">
                <label for="KgCalDolomitica" class="form-label">Kg Cal Dolomítica</label>
                <input type="number" step="0.01" class="form-control" id="KgCalDolomitica" name="KgCalDolomitica"
                  value="{{ old('KgCalDolomitica') }}">
              </div>
            </div>
            @endif

            {{-- Fila 5: Campos Calculados --}}
            <h5 class="fw-bold mb-3 text-dark border-top pt-3">
              <i class="fas fa-calculator me-2"></i> Índices Calculados
            </h5>
            <div class="row g-3 mb-4">
              {{-- Condicional para IB2 (Solo en HA y MCC) --}}
              @if($tipo == 'ha' || $tipo == 'mcc')
              <div class="col-md-3" id="campo-ib2">
                <label for="IB2" class="form-label">IB2</label>
                <input type="number" step="0.01" class="form-control" id="IB2" name="IB2" value="{{ old('IB2') }}"
                  readonly>
              </div>
              @endif
              <div class="col-md-3">
                <label for="IB3" class="form-label">IB3</label>
                <input type="number" step="0.01" class="form-control" id="IB3" name="IB3" value="{{ old('IB3') }}"
                  readonly>
              </div>
              <div class="col-md-3">
                <label for="IB4" class="form-label">IB4</label>
                <input type="number" step="0.01" class="form-control" id="IB4" name="IB4" value="{{ old('IB4') }}"
                  readonly>
              </div>
              <div class="col-md-3">
                <label for="TOTAL" class="form-label">TOTAL</label>
                <input type="number" step="0.01" class="form-control" id="TOTAL" name="TOTAL" value="{{ old('TOTAL') }}"
                  readonly>
              </div>
            </div>

            {{-- Botón de Guardar --}}
            <div class="d-flex justify-content-end mt-4">
              <button type="submit" class="btn btn-primary fw-bold px-4 py-2">
                <i class="fas fa-save me-2"></i> Registrar Análisis
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- Script para cálculos dinámicos --}}
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const tipo = document.getElementById('tipoAnalisis').value;

    // Función para calcular
    function calcularIndices() {
      // 1. Obtener todos los valores
      const CaO = parseFloat(document.getElementById("CaO").value) || 0;
      const MgO = parseFloat(document.getElementById("MgO").value) || 0;
      const SiO2 = parseFloat(document.getElementById("SiO2").value) || 0;
      const Al2O3 = parseFloat(document.getElementById("Al2O3").value) || 0;
      const MnO = parseFloat(document.getElementById("MnO").value) || 0;
      const FeO = parseFloat(document.getElementById("FeO").value) || 0;
      const S = parseFloat(document.getElementById("S").value) || 0;

      // 2. Calcular todos los índices
      const denominadorIB3_IB4 = SiO2 + Al2O3;

      const IB2 = SiO2 > 0 ? (CaO / SiO2) : 0;
      const IB3 = denominadorIB3_IB4 > 0 ? (CaO / denominadorIB3_IB4) : 0;
      const IB4 = denominadorIB3_IB4 > 0 ? ((CaO + MgO) / denominadorIB3_IB4) : 0;
      const Total = CaO + MgO + SiO2 + Al2O3 + MnO + FeO + S;

      // 3. Asignar valores a los campos readonly
      document.getElementById("IB3").value = IB3.toFixed(2);
      document.getElementById("IB4").value = IB4.toFixed(2);
      document.getElementById("TOTAL").value = Total.toFixed(2);

      // 4. Asignar IB2 solo si el campo existe (tipos 'ha' y 'mcc')
      const ib2Field = document.getElementById("IB2");
      if (ib2Field) {
        ib2Field.value = IB2.toFixed(2);
      }
    }

    // 5. Añadir el listener a todos los inputs de cálculo
    document.querySelectorAll('.calc-input').forEach(input => {
      input.addEventListener('input', calcularIndices);
    });

    // 6. Ejecutar un cálculo al cargar por si hay 'old' values
    calcularIndices();
  });
  </script>
</x-app-layout>