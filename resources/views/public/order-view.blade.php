<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #{{ $order->id }} - {{ $order->reference_name }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        .bg-lightpink {background-color: #ffccd5 !important;}
        .bg-lightblue {background-color: #b4d1eb !important;}
        .bg-lightgreen {background-color: #b4ebcb !important;}
        
        .order-header {
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 2rem;
            padding: 1.5rem;
        }
        
        .order-content {
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 2rem;
            padding: 1.5rem;
        }
        
        .order-id {
            font-size: 1.75rem;
            font-weight: bold;
            color: #0d6efd;
        }
        
        .section-title {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            color: #495057;
        }
        
        .table-responsive {
            margin-bottom: 2rem;
        }
        
        .order-image {
            max-width: 100%;
            height: auto;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        
        .qr-image {
            max-width: 150px;
            height: auto;
        }
        
        .footer {
            margin-top: 3rem;
            text-align: center;
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        @media print {
            body {
                padding: 0 !important;
                background-color: #fff !important;
            }
            .order-header, .order-content {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
                break-inside: avoid;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Botones de acción -->
        <div class="mb-4 no-print">
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary me-2" onclick="window.print()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                        <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                        <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                    </svg>
                    Imprimir
                </button>
            </div>
        </div>
        
        <!-- Encabezado del pedido -->
        <div class="order-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="text-center mb-3">{{ $order->reference_name }}</h1>
                    <div class="order-id">Pedido: #{{ $order->id }}</div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-2"><strong>Fecha de Emisión:</strong> {{ $order->issue_date }}</p>
                            <p class="mb-2"><strong>Fecha de Entrega:</strong> {{ $order->delivery_date }}</p>
                            <p class="mb-2"><strong>Vendedor:</strong> {{ $order->seller->name ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Gestor:</strong> {{ $order->manager->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sección de detalles del pedido -->
        <div class="order-content">
            <h3 class="section-title">Detalles de la Orden</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>ORD</th>
                            <th>TIPO</th>
                            <th>NOMBRE</th>
                            <th>NRO</th>
                            <th>OTROS</th>
                            <th>TAM - MOLDE</th>
                            <th>TIPO DE INDUMENTARIA</th>   
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr class="{{ $item->size->color ?? '' }}">
                            <td>{{ $item->item }}</td>
                            <td>{{ $item->model }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->other }}</td>
                            <td>{{ $item->size->name ?? 'N/A' }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->quantity }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <h3 class="section-title">Resumen de Camisetas</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>Categoría</th>
                            <th>Tipo</th>
                            <th>Producto</th>
                            <th>Tamaño</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderReferenceSummaries->sortBy('code_new') as $tipo)
                            <tr class="{{ $tipo->size->color ?? '' }}">
                                <td>{{ $tipo->product->Category->name ?? 'N/A' }}</td>
                                <td>{{ $tipo->product->code ?? 'N/A' }}</td>
                                <td>{{ $tipo->product->name ?? 'N/A' }}</td>
                                <td>{{ $tipo->size->name ?? 'N/A' }}</td>
                                <td>{{ $tipo->total_quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <h3 class="section-title">Información Adicional</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>Pregunta</th>
                            <th>Respuesta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->questionAnswers as $answer)
                        <tr>
                            <td>{{ $answer->question->text }}</td>
                            <td>{{ $answer->answer }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Sección de modelos con imágenes -->
        <div class="order-content">
            <h3 class="section-title">Modelos con Imágenes</h3>
            <div class="row">
                @foreach($order->orderMolds as $item)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="text-center mb-0">{{ $item->title }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="{{ asset('storage/' . ($item->imagen ?? 'N/A')) }}" 
                                     alt="Imagen del modelo" 
                                     class="order-image">
                            </div>
                            <div class="text-center">
                                <img src="{{ $item->qr }}" alt="QR Code" class="qr-image">
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <div class="footer">
            <p>Este documento es de carácter informativo. Enlace válido por tiempo limitado.</p>
            <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 