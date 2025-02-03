 public static function table(Table $table): Table
    {
        // Obtener todas las categorías dinámicamente
        $categories = Category::all();

        // Definir columnas base
        $columns = [
            Tables\Columns\TextColumn::make('id')
                ->label('Order ID')
                ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                ->label('Reference Name')
                ->sortable()
                ->searchable(),

                Tables\Columns\TextColumn::make('delivery_date')
                ->label('Fecha de Entrega')
                ->date()
                ->sortable(),
        ];

        // Agregar columnas dinámicas de categorías
        foreach ($categories as $category) {
            $columns[] = Tables\Columns\TextColumn::make("category_{$category->id}")
                ->label($category->name)
                ->formatStateUsing(fn ($record) => 
                    $record->product->categories
                        ->firstWhere('id', $category->id)?->pivot->quantity ?? 0
                );
        }

        // Pasar columnas correctamente al método `columns()`
        return $table->columns($columns);
    }