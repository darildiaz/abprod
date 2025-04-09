<form action="{{ route('welcome') }}" method="GET" class="space-y-6">
    <!-- Categorías -->
    <div>
        <h3 class="text-lg font-semibold mb-2">Categorías</h3>
        <div class="space-y-2">
            <label class="flex items-center">
                <input type="radio" name="category" value="" class="mr-2" {{ !request('category') ? 'checked' : '' }}>
                <span>Todas</span>
            </label>
            @foreach($categories as $category)
                <label class="flex items-center">
                    <input type="radio" name="category" value="{{ $category->id }}" class="mr-2" {{ request('category') == $category->id ? 'checked' : '' }}>
                    <span>{{ $category->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Líneas -->
    <div>
        <h3 class="text-lg font-semibold mb-2">Líneas</h3>
        <div class="space-y-2">
            <label class="flex items-center">
                <input type="radio" name="line" value="" class="mr-2" {{ !request('line') ? 'checked' : '' }}>
                <span>Todas</span>
            </label>
            @foreach($lines as $line)
                <label class="flex items-center">
                    <input type="radio" name="line" value="{{ $line->id }}" class="mr-2" {{ request('line') == $line->id ? 'checked' : '' }}>
                    <span>{{ $line->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Etiquetas -->
    <div>
        <h3 class="text-lg font-semibold mb-2">Etiquetas</h3>
        <div class="space-y-2">
            <label class="flex items-center">
                <input type="radio" name="tag" value="" class="mr-2" {{ !request('tag') ? 'checked' : '' }}>
                <span>Todas</span>
            </label>
            @foreach($tags as $tag)
                <label class="flex items-center">
                    <input type="radio" name="tag" value="{{ $tag }}" class="mr-2" {{ request('tag') == $tag ? 'checked' : '' }}>
                    <span>{{ $tag }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Aplicar Filtros
    </button>
</form> 