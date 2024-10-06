<?php

namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\View\View;

use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Illuminate\support\Facades\Storage;

class ProductController extends Controller
{
    public function index() : View
    {
        //get all products
        $products = Product::latest()->paginate(10);

        //render view with products
        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return View('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        //validate form
        $request->validate([
            'image'         =>  'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'         =>  'required|min:5',
            'description'   =>  'required|min:10',
            'price'         =>  'required|numeric',
            'stock'         =>  'required|numeric'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        //create product
        Product::create([
            'image'         =>  $image->hashName(),
            'title'         =>  $request->title,
            'description'   =>  $request->description,
            'price'         =>  $request->price,
            'stock'         =>  $request->stock
        ]);

        //redirect to index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function show(string $id): View

    {
        $product = Product::findOrFail($id);
        
        return view('products.show', compact('product'));
    }
    
    public function edit(string $id): View
    
    {
        $product = Product::findOrFail($id);
        
        return view('products.edit', compact('product'));
    }
    
    public function update(Request $request, $id):RedirectResponse
    {
        $request->validate([
            'image'         =>  'image|mimes:jpeg,jpg,png|max:2048',
            'title'         =>  'required|min:5',
            'description'   =>  'required|min:10',
            'price'         =>  'required|numeric',
            'stock'         =>  'required|numeric'
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('image')){

            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            Storage::delete('public/products/'.$product->image);

            $product->update([
                'image'         =>  $image->hashName(),
                'title'         =>  $request->title,
                'description'   =>  $request->description,
                'price'         =>  $request->price,
                'stock'         =>  $request->stock
            ]);
        } else {
            $product->update([
                'title'         =>  $request->title,
                'description'   =>  $request->description,
                'price'         =>  $request->price,
                'stock'         =>  $request->stock
            ]);
        }
        return redirect()->route('products.index')->with(['success' => 'data Berhasil Diubah']);
    }

    public function destroy($id):RedirectResponse
    {
        $product = Product::findOrFail($id);

        Storage::delete('public/products/'.$product->image);

        $product->delete();

        return redirect()->route('products.index')->with(['success' => 'Data berhasil Dihapus']);
    }
}