<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Etiqueta Muestra {{ $muestra->IdMuestra }}</title>
  <style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 4mm;
    /* Reducir padding para más espacio */
    width: 70mm;
    /* Ancho ajustado */
    height: 45mm;
    /* Alto ajustado */
    border: 1px dashed #ccc;
    /* Borde punteado para visualización */
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    font-size: 10pt;
    line-height: 1.3;
  }

  .header {
    text-align: center;
    font-weight: bold;
    font-size: 11pt;
  }

  .content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-grow: 1;
    padding: 2mm 0;
  }

  .info p {
    margin: 1.5mm 0;
    /* Espacio entre líneas */
  }

  .qr-code {
    text-align: right;
    padding-left: 3mm;
  }

  .print-button {
    display: none;
    /* Oculto por defecto */
  }

  /* Estilos para impresión */
  @media print {
    body {
      border: none;
      /* Sin borde al imprimir */
      margin: 0;
      padding: 0;

      /* Ocultar el pie de página del navegador si es posible */
      @page {
        margin: 0;
      }
    }

    .print-button {
      display: none;
      /* Oculta el botón al imprimir */
    }
  }
  </style>
</head>

{{-- Añadimos el script para auto-imprimir al cargar --}}

<body onload="window.print();">

  <div class="header">
    ADM Calidad Central
  </div>

  <div class="content">
    <div class="info">
      <p><strong>ID Muestra:</strong> {{ $muestra->IdMuestra }}</p>
      {{-- Usar FechaRegistro para la fecha y hora --}}
      <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($muestra->FechaRegistro)->format('d/m/Y H:i') }}</p>
      {{-- Usar la relación 'material' y el campo 'Material' --}}
      <p><strong>Material:</strong> {{ $muestra->material->Material ?? 'N/A' }}</p>
      {{-- Mostrar Proveedor o Solicitante --}}
      <p><strong>Origen:</strong> {{ $muestra->Proveedor ?: ($muestra->Solicitante ?: 'N/A') }}</p>
    </div>
    <div class="qr-code">
      {{-- Renderizar el SVG del QR que viene del controlador --}}
      {!! $qrCode !!}
    </div>
  </div>

  <div class="footer" style="text-align: center; font-size: 8pt; font-weight: bold;">
    {{ $muestra->IdMuestra }}
  </div>

  {{-- Botón manual por si falla la auto-impresión --}}
  <div class="print-button" style="display: block; text-align: center; margin-top: 5mm;">
    <button onclick="window.print();">Imprimir Etiqueta</button>
  </div>

</body>

</html>