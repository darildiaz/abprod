
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
    </style>


    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
</head>
<body class="bg-white font-family-karla">

    <!-- Top Bar Nav -->

    <!-- Text Header -->
    <header class="w-full container mx-auto">
        <div class="flex flex-col items-center py-12">
            <a class="font-bold text-gray-800 uppercase hover:text-gray-700 text-5xl" href="#">
                Abprod
            </a>
            <p class="text-lg text-gray-600">
                Escuentra en diseño perfecto
            </p>
        </div>
    </header>

    <Topic Nav >
    <nav class="w-full py-4 border-t border-b bg-gray-100" ">

        <div class="w-full flex-grow sm:flex sm:items-center sm:w-auto">
            <div class="w-full container mx-auto flex flex-col sm:flex-row items-center justify-center text-sm font-bold uppercase mt-0 px-6 py-2">
                <a href="#" class="hover:bg-gray-400 rounded py-2 px-4 mx-2">Fútbol</a>
                <a href="#" class="hover:bg-gray-400 rounded py-2 px-4 mx-2">Basket</a>
                <a href="#" class="hover:bg-gray-400 rounded py-2 px-4 mx-2">Voley</a>
                <a href="#" class="hover:bg-gray-400 rounded py-2 px-4 mx-2">Futboley</a>
                <a href="#" class="hover:bg-gray-400 rounded py-2 px-4 mx-2">Pesca</a>
                <a href="#" class="hover:bg-gray-400 rounded py-2 px-4 mx-2">Natación</a>
            </div>
        </div>
    </nav>


    <div class="container mx-auto flex flex-wrap py-6">

        <!-- Posts Section -->
        <section class="w-full md:w-2/3 flex flex-col items-center px-3">
           <table>
            <tr>
                <td>
                    producto
                </td>
            </tr>
           </table>
        </section>

        <!-- Sidebar Section -->
        <aside class="w-full md:w-1/3 flex flex-col items-center px-3">

            <div class="w-full bg-white shadow flex flex-col my-4 p-6">
                <p class="text-xl font-semibold pb-5">Nosotros</p>
                <p class="pb-2">es un proyecto para la gestion de produccion de abdiez</p>
                <a href="admin/login" class="w-full bg-blue-800 text-white font-bold text-sm uppercase rounded hover:bg-blue-700 flex items-center justify-center px-2 py-3 mt-4">
                    Login
                </a>
            </div>


        </aside>

    </div>

    <footer class="w-full border-t bg-white pb-12">
        <div class="w-full container mx-auto flex flex-col items-center">
            <div class="flex flex-col md:flex-row text-center md:text-left md:justify-between py-6">
                <a href="#" class="uppercase px-3">Nosotros</a>
                <a href="#" class="uppercase px-3">Politicas de Privacidad</a>
                <a href="#" class="uppercase px-3">Contactos</a>
            </div>
            <div class="uppercase pb-6">&copy; abdiez.com</div>
        </div>
    </footer>

    <script>
        
        </script>

</body>
</html>
