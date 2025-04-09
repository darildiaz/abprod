<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Abprod</title>
    <meta name="author" content="Adrian y Daril">
    <meta name="description" content="Detalles del producto {{ $product->name }}">

    <!-- Tailwind -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Karla:400,700&display=swap');

        .font-family-karla {
            font-family: karla;
        }
    </style>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
</head>
<body class="bg-white font-family-karla">
    <div class="container mx-auto px-4 py-8">
        <a href="{{ route('welcome') }}" class="text-blue-500 hover:text-blue-700 mb-4 inline-block">
            <i class="fas fa-arrow-left"></i> Volver a productos
        </a>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="md:flex">
                <div class="md:w-1/2">
                    @if($product->imagen)
                        <img src="{{ asset('storage/' . $product->imagen) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                </div>
                
                <div class="md:w-1/2 p-8">
                    <div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold">{{ $product->category->name }}</div>
                    <h1 class="mt-2 text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <div class="mt-2">
                        <span class="text-sm bg-green-100 text-green-800 px-2 py-1 rounded">{{ $product->line->name }}</span>
                    </div>
                    
                    <div class="mt-4">
                        <h2 class="text-lg font-semibold text-gray-700">Descripción</h2>
                        <p class="mt-2 text-gray-600">{{ $product->description }}</p>
                    </div>

                    <!-- Precios -->
                    <div class="mt-4">
                        <h2 class="text-lg font-semibold text-gray-700">Precios</h2>
                        <div class="mt-2">
                            @if($product->price->count() > 0)
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach($product->price as $price)
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <div class="font-semibold">{{ $price->size->name }}</div>
                                            <div class="text-lg font-bold text-blue-600">
                                                {{ number_format($price->price, 0, ',', '.') }} Gs.
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No hay precios disponibles</p>
                            @endif
                        </div>
                    </div>

                    @if($product->tags)
                        <div class="mt-4">
                            <h2 class="text-lg font-semibold text-gray-700">Etiquetas</h2>
                            <div class="mt-2">
                                @foreach($product->tags as $tag)
                                    <span class="text-sm bg-gray-100 text-gray-800 px-2 py-1 rounded mr-1">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($product->imagmolde)
                        <div class="mt-4">
                            <h2 class="text-lg font-semibold text-gray-700">Imagen del molde</h2>
                            <img src="{{ asset('storage/' . $product->imagmolde) }}" alt="Molde de {{ $product->name }}" class="mt-2 w-full h-48 object-contain">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html> 