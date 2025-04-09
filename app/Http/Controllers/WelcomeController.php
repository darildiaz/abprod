<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Line;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['category', 'line']);

        // Filtros
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('line')) {
            $query->where('line_id', $request->line);
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        $products = $query->paginate(12);
        $categories = Category::all();
        $lines = Line::all();
        $tags = Product::pluck('tags')->flatten()->unique()->filter()->values();

        return view('welcome', compact('products', 'categories', 'lines', 'tags'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'line', 'price.size']);
        return view('product.show', compact('product'));
    }
} 