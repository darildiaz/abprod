// Sección 1: Datos generales del pedido
                Forms\Components\Select::make('customer_id')->label('Customer')->relationship('customer', 'name')->required(),
                Forms\Components\Select::make('seller_id')
                ->label('Seller')
                ->relationship('seller', 'name')
                ->default(auth()->id()) // Predetermina el usuario logueado
                ->required(),
                Forms\Components\TextInput::make('reference_name')->label('Reference Name')->required(),
                Forms\Components\DatePicker::make('issue_date')->label('Issue Date')->required(),
                Forms\Components\DatePicker::make('delivery_date')->label('Delivery Date')->required(),

                // Sección 2: Modelos (título, imagen)
                Forms\Components\Repeater::make('models')->label('Models')->relationship('orderMolds')->schema([
                        Forms\Components\TextInput::make('title')->label('Model Title')->required(),
                        Forms\Components\FileUpload::make('imagen')->label('Image')->required(),
                ])->columns(2)->required(),

                // Sección 3: Referencias desde Excel
                Forms\Components\Textarea::make('references_input')
                    ->label('Paste Product References')
                    ->helperText("Paste data separated by tab and new lines (Product Name, Product Code, Price)")
                    ->rows(5)
                    ->reactive()
                    ->placeholder("Example:\ncamiseta\tcam-s01\t100000\nshort\tsh-s01\t50000")
                    ->afterStateUpdated(function ($state, callable $set) {
                        $references = collect(explode("\n", $state))
                            ->map(fn ($line) => explode("\t", $line))
                            ->filter(fn ($cols) => count($cols) === 3)
                            ->map(fn ($cols) => [
                                'name' => $cols[0],
                                'code' => $cols[1],
                                'price' => (int) $cols[2],
                            ]);
                        $set('references', $references->toArray());
                    }),
                    Forms\Components\TextInput::make('references')->label('Processed References')->hidden(),

                // Sección 4: Ítems desde Excel
                Forms\Components\Textarea::make('order_items_input')
                    ->label('Paste Order Items')
                    ->helperText("Paste data separated by tab and new lines (Item, Model, Name, Number, Others, Size, Quantity, Products, Subtotal)")
                    ->rows(5)
                    ->reactive()
                    ->placeholder("Example:\n1\toficial\tjugador 1\t10\t0rh+\tm-cab\t1\tcamiseta,short\t150000")
                    ->afterStateUpdated(function ($state, callable $set) {
                        $orderItems = collect(explode("\n", $state))
                            ->map(fn ($line) => explode("\t", $line))
                            ->filter(fn ($cols) => count($cols) === 9)
                            ->map(fn ($cols) => [
                                'item' => (int) $cols[0],
                                'model' => $cols[1],
                                'name' => $cols[2],
                                'number' => $cols[3],
                                'others' => $cols[4],
                                'size' => $cols[5],
                                'quantity' => (int) $cols[6],
                                'products' => explode(',', $cols[7]),
                                'subtotal' => (int) $cols[8],
                            ]);
                        $set('order_items', $orderItems->toArray());
                    }),
                Forms\Components\TextInput::make('total')->label('Total')->numeric()->required(),
