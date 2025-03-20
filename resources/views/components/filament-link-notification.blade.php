@if (session()->has('linkPedido'))
<div x-data="{ copied: false }"
     class="p-4 mb-4 border rounded-lg bg-primary-50 border-primary-200 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-medium text-primary-700">Enlace público generado correctamente</h3>
        <p class="mt-1 text-sm text-primary-600 max-w-2xl truncate">
            {{ session('linkPedido') }}
        </p>
    </div>
    <div class="flex gap-2">
        <button
            x-on:click="
                navigator.clipboard.writeText('{{ session('linkPedido') }}');
                copied = true;
                setTimeout(() => copied = false, 2000);
            " 
            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
            <span x-show="!copied">Copiar enlace</span>
            <span x-show="copied">¡Copiado!</span>
        </button>
        <a 
            href="{{ session('linkPedido') }}" 
            target="_blank" 
            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-primary-700 bg-white border border-primary-300 hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
            Abrir enlace
        </a>
    </div>
</div>
@endif 