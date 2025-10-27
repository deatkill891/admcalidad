<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Etiqueta Muestra {{ $muestra->id }}</title>
  <style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 5mm;
    /* Margen para impresión */
    width: 80mm;
    /* Ancho estimado de etiqueta, ajusta según necesidad */
    height: 50mm;
    /* Alto estimado de etiqueta, ajusta según necesidad */
    border: 1px solid #ccc;
    /* Borde para visualizar el tamaño */
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    /* Distribuye el espacio */
    font-size: 10pt;
    /* Tamaño de fuente base */
  }

  .header,
  .footer {
    text-align: center;
    font-weight: bold;
  }

  .content {
    display: flex;
    justify-content: space-between;
    /* Espacio entre info y QR */
    align-items: center;
    /* Alinea verticalmente */
    flex-grow: 1;
    /* Ocupa el espacio disponible */
    padding: 2mm 0;
  }

  .info p {
    margin: 1mm 0;
    /* Espacio entre líneas de información */
    line-height: 1.2;
  }

  .qr-code {
    text-align: right;
  }

  /* Estilos para impresión - Oculta borde, ajusta márgenes */
  @media print {
    body {
      border: none;
      margin: 0;
      padding: 0;
      width: auto;
      height: auto;
    }

    .print-button {
      display: none;
      /* Oculta el botón al imprimir */
    }
  }

  .print-button {
    margin-top: 5mm;
    text-align: center;
  }
  </style>
</head>

<body>
  <div class="header">
    MUESTRA DE CALIDAD
  </div>

  <div class="content">
    <div class="info">
      <p><strong>ID Muestra:</strong> {{ $muestra->id }}</p>
      {{-- Formatea la fecha como necesites --}}
      <p><strong>Recibido:</strong> {{ $muestra->created_at->format('d/m/Y H:i') }}</p>
      {{-- Asume que tienes relaciones definidas en el modelo Muestra --}}
      {{-- Si no usas relaciones, muestra los IDs directamente: $muestra->material_id --}}
      <p><strong>Material:</strong> {{ $muestra->material->nombre ?? 'N/A' }}</p>
      {{-- Ajusta 'nombre' al campo correcto --}}
      <p><strong>Proveedor:</strong> {{ $muestra->proveedor->nombre ?? 'N/A' }}</p>
      {{-- Ajusta 'nombre' al campo correcto --}}
      <p><strong>Ubicación:</strong> {{ $muestra->ubicacion->nombre ?? 'N/A' }}</p>
      {{-- Ajusta 'nombre' al campo correcto --}}
    </div>
    <div class="qr-code">
      {!! $qrCode !!} {{-- Importante usar {!! !!} para renderizar el SVG/HTML del QR --}}
    </div>
  </div>

  <div class="footer">
    {{ $muestra->id }}
  </div>

  <div class="print-button">
    <button onclick="window.print();">Imprimir Etiqueta</button>
  </div>

</body>

</html>