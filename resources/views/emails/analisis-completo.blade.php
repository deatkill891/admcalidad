<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Resultados de Analisis de Muestra</title>
  <style>
  body {
    font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f5f5f5;
    margin: 0;
    padding: 0;
  }

  /* .container reemplaza a .email-container */
  .container {
    max-width: 600px;
    margin: 0 auto;
    background: #ffffff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  /* .header reemplaza a .email-header */
  .header {
    background: #2c3e50;
    color: white;
    padding: 20px;
    text-align: center;
  }

  /* Estilos para h2 y h3 dentro de .header */
  .header h2 {
    margin: 0;
    font-size: 24px;
    color: white;
  }

  .header h3 {
    margin: 5px 0 0 0;
    font-size: 18px;
    font-weight: normal;
    color: #bdc3c7;
    /* Un gris claro para el subtítulo */
  }

  /* Estilos adaptados para .info-table (en lugar de .info-grid) */
  .info-table {
    width: 100%;
    margin: 20px 0;
    border-collapse: collapse;
    padding: 0 20px;
    /* Padding para el cuerpo */
  }

  .info-table td {
    padding: 8px 20px;
    /* Padding para el cuerpo */
    border-bottom: 1px solid #eaeaea;
  }

  .info-table strong {
    font-weight: bold;
    color: #555;
  }

  /* Título de sección del script original */
  .results-header {
    color: #2c3e50;
    border-bottom: 2px solid #eaeaea;
    padding: 0 20px 8px 20px;
    /* Padding para el cuerpo */
    margin: 20px 0 15px 0;
    font-size: 18px;
  }

  /* .table-results reemplaza a .results-table */
  .table-results {
    width: calc(100% - 40px);
    /* Ajuste por padding */
    margin: 0 20px 20px 20px;
    /* Padding para el cuerpo */
    border-collapse: collapse;
  }

  /* .header-table th reemplaza a .results-table th */
  .table-results .header-table th {
    background-color: #2c3e50;
    color: white;
    padding: 10px;
    text-align: left;
  }

  .table-results td {
    padding: 10px;
    border-bottom: 1px solid #eaeaea;
  }

  .table-results tbody tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  /* Estilo para la columna Valor */
  .table-results td:nth-child(2) {
    font-weight: bold;
  }

  /* Estilos para Mínimo y Máximo (centrados) */
  .table-results .header-table th:nth-child(3),
  .table-results .header-table th:nth-child(4),
  .table-results td:nth-child(3),
  .table-results td:nth-child(4) {
    text-align: center;
  }

  /* .footer reemplaza a .email-footer */
  .footer {
    background: #f5f5f5;
    padding: 15px;
    text-align: center;
    font-size: 12px;
    color: #777;
  }

  /* Insignia de estado (del script original) */
  .status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 14px;
  }

  .status-pass {
    background-color: #d4edda;
    color: #155724;
  }

  .status-fail {
    background-color: #f8d7da;
    color: #721c24;
  }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h2>Resultados de Analisis de Muestra</h2>
      <h3>Folio: {{ $muestra->IdMuestra }}</h3>
    </div>

    <table class="info-table">
      <tr>
        <td style="width: 25%;"><strong>Material:</strong></td>
        <td>{{ $muestra->material->Material ?? 'N/A' }}</td>
      </tr>
      <tr>
        <td><strong>Fecha de Analisis:</strong></td>
        <td>
          {{ $muestra->FechaAnalisis ? \Carbon\Carbon::parse($muestra->FechaAnalisis)->format('d/m/Y h:i A') : 'N/A' }}
        </td>
      </tr>
      <tr>
        <td><strong>Proveedor:</strong></td>
        <td>{{ $muestra->Proveedor ?? 'N/A' }}</td>
      </tr>
      <tr>
        <td><strong>Registró:</strong></td>
        <td>{{ $muestra->usuarioOper->username ?? 'N/A' }}</td>
      </tr>
      <tr>
        <td><strong>Condiciones Climáticas:</strong></td>
        <td>{{ $muestra->Clima ?? 'N/A' }}</td>
      </tr>
      <tr>
        <td><strong>Humedad:</strong></td>
        <td>{{ $muestra->Humedad ?? 'N/A' }}</td>
      </tr>
    </table>

    <h3 class="results-header">Resultados del Analisis</h3>
    <table class="table-results">
      <thead>
        <tr class="header-table">
          <th>Elemento</th>
          <th>Valor</th>
          <th>Minimo</th>
          <th>Maximo</th>
        </tr>
      </thead>
      <tbody>
        @forelse($muestra->resultados as $resultado)
        @php
        // Obtenemos los límites del array que pasamos al Mailable
        $min = $limites[$resultado->IdElemento]['min'] ?? 'N/A';
        $max = $limites[$resultado->IdElemento]['max'] ?? 'N/A';
        @endphp
        <tr>
          <td>{{ $resultado->elemento->Nombre ?? 'N/A' }}</td>
          <td><strong>{{ $resultado->Valor }}</strong></td>
          <td>{{ $min }}</td>
          <td>{{ $max }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="4" style="text-align: center;">No se registraron resultados.</td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <div class="footer">
      <p>Este es un correo automático. Por favor, no responda a este mensaje.<br>
        &copy; {{ date('Y') }} ADM Calidad Central | Designed by TI Operaciones Acería Celaya.</p>
    </div>
  </div>
</body>

</html>