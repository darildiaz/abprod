<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden #{{ $order->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; font-size: 20px; font-weight: bold; margin-bottom: 20px; }
        .info, .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .info td, .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f4f4f4; }
    </style>
    <style>
        .text-rotate {
            transform: rotate(90deg);
            transform-origin: left bottom;
            white-space: nowrap;
            display: inline-block;
            
        }
        .bg-lightpink {background-color: #ffccd5 !important;}
        .bg-lightblue {background-color: #b4d1eb !important;}
        .bg-lightgreen {background-color: #b4ebcb !important;}
    </style>
</head>
<body>

    <table class="info">
        <tr>
            <td colspan="3"><center><h1> {{ $order->reference_name }}</h1></center></td>
        </tr>
        <tr>
            <td><h1><strong>O.T.:</strong> {{ $order->id }}</h1></td>
            <td colspan="2"><strong>Fecha de Entrega:</strong> {{ $order->delivery_date }}</td>
        </tr>
        <tr>
            <td><strong>Fecha Emisión:</strong> {{ $order->issue_date }}</td>
            <td><strong>Vendedor:</strong> {{ $order->seller->name ?? 'N/A' }}</td>
            <td><strong>Gestor:</strong> {{ $order->manager->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <h4>Detalles de la Orden</h4>
    <table class="table">
        <thead>
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

    <h4>Resumen de Camisetas</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Categoria</th>
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
    <h4>Informacion </h4>
    <table class="table">
        <thead>
            <tr>
                <th>Pregunta</th>
                <th>Respuesta</th>
                <th>Pregunta</th>
                <th>Respuesta</th>
            </tr>
        </thead>
        <tbody>
                @foreach($order->questionAnswers as $index => $answer)
                    @if($index % 2 == 0)
                    <tr>
                    <td>{{ $answer->question->text }}</td>
                            <td>{{ $answer->answer }}</td>

                    @endif
                    @if($index % 2 == 1)
                    
                            <td>{{ $answer->question->text }}</td>
                            <td>{{ $answer->answer }}</td>
                 </tr>      

                    @endif
            @endforeach
        </tbody>
    </table>
     <h4>Modelos con Imágenes</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Modelo</th>
                    <th>Imagen</th>
                    <th>Qr</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderMolds as $item)
                    <tr>
                        <td><center><h2 class="">{{ $item->title }}</h2></center></td>
                        <td>
                        <center>
                                <img src="{{ public_path('storage/' . $item->imagen ?? 'N/A' ) }}" alt="Imagen del modelo" style="max-width: 300px; max-height: 300px;">
                                
                            </center>
                        </td>
                        <td>
                            <center>
                            <img src="{{ $item->qr}}" alt="QR Code">
                        </center>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
</body>
</html>
