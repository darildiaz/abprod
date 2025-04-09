<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abprod</title>
    <meta name="author" content="Adrian y Daril">
    <meta name="description" content="Aplicacion para la gestion de Multigyms">

    <!-- Tailwind -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Karla:400,700&display=swap');

        .font-family-karla {
            font-family: karla;
        }
        
        /* Estilos para el menú móvil */
        .mobile-filters {
            display: none;
        }
        
        @media (max-width: 768px) {
            .mobile-filters {
                display: block;
            }
            .desktop-filters {
                display: none;
            }
            .mobile-menu {
                display: none;
            }
            .mobile-menu.active {
                display: block;
            }
        }
    </style>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
</head>
<body class="bg-white font-family-karla">
    <!-- Menú de navegación -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="text-2xl font-bold text-gray-800">Abprod</a>
                </div>

                <!-- Menú Desktop -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-blue-600">Inicio</a>
                    <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-blue-600">Productos</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600">Información</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600">Noticias</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600">Nosotros</a>
                    <a href="https://wa.me/595123456789" target="_blank" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 flex items-center">
                        <i class="fab fa-whatsapp mr-2"></i> Contacto
                    </a>
                </div>

                <!-- Botón menú móvil -->
                <div class="md:hidden">
                    <button onclick="toggleMenu()" class="text-gray-600 hover:text-blue-600 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>

            <!-- Menú Móvil -->
            <div class="mobile-menu md:hidden py-4">
                <div class="flex flex-col space-y-4">
                    <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-blue-600">Inicio</a>
                    <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-blue-600">Productos</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600">Información</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600">Noticias</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600">Nosotros</a>
                    <a href="https://wa.me/595123456789" target="_blank" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 flex items-center justify-center">
                        <i class="fab fa-whatsapp mr-2"></i> Contacto
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Text Header -->
    <header class="w-full container mx-auto">
        <div class="flex flex-col items-center py-12">
            <a class="font-bold text-gray-800 uppercase hover:text-gray-700 text-5xl" href="#">
                Abprod
            </a>
            <p class="text-lg text-gray-600">
                Encuentra el diseño perfecto
            </p>
        </div>
    </header>

    <div class="container mx-auto flex flex-wrap py-6">
        <!-- Botón de filtros móvil -->
        <div class="mobile-filters w-full px-3 mb-4">
            <button onclick="toggleFilters()" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center">
                <i class="fas fa-filter mr-2"></i> Filtros
            </button>
        </div>

        <!-- Filtros (Desktop) -->
        <aside class="desktop-filters w-full md:w-1/4 flex flex-col items-center px-3">
            <div class="w-full bg-white shadow flex flex-col my-4 p-6">
                <h2 class="text-xl font-semibold pb-5">Filtros</h2>
                @include('partials.filters')
            </div>
        </aside>

        <!-- Filtros (Mobile) -->
        <div id="mobileFilters" class="mobile-filters w-full px-3 mb-4 hidden">
            <div class="w-full bg-white shadow flex flex-col my-4 p-6">
                <h2 class="text-xl font-semibold pb-5">Filtros</h2>
                @include('partials.filters')
            </div>
        </div>

        <!-- Posts Section -->
        <section class="w-full md:w-3/4 flex flex-col items-center px-3">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        @if($product->imagen)
                            <img src="{{ asset('storage/' . $product->imagen) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        <div class="p-4">
                            <a href="{{ route('product.show', $product) }}" class="text-lg font-semibold hover:text-blue-600">{{ $product->name }}</a>
                            <p class="text-gray-600">{{ Str::limit($product->description, 100) }}</p>
                            <div class="mt-2">
                                <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $product->category->name }}</span>
                                <span class="text-sm bg-green-100 text-green-800 px-2 py-1 rounded ml-2">{{ $product->line->name }}</span>
                            </div>
                            @if($product->tags)
                                <div class="mt-2">
                                    @foreach($product->tags as $tag)
                                        <span class="text-sm bg-gray-100 text-gray-800 px-2 py-1 rounded mr-1">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </section>
    </div>

    <footer class="w-full border-t bg-white pb-12">
        <div class="w-full container mx-auto flex flex-col items-center">
            <div class="flex flex-col md:flex-row text-center md:text-left md:justify-between py-6">
                <a href="#" class="uppercase px-3">Nosotros</a>
                <a href="#" class="uppercase px-3">Políticas de Privacidad</a>
                <a href="#" class="uppercase px-3">Contactos</a>
            </div>
            <div class="uppercase pb-6">&copy; abdiez.com</div>
        </div>
    </footer>

    <script>
        function toggleFilters() {
            const filters = document.getElementById('mobileFilters');
            filters.classList.toggle('hidden');
        }

        function toggleMenu() {
            const menu = document.querySelector('.mobile-menu');
            menu.classList.toggle('active');
        }
    </script>
</body>
</html>
