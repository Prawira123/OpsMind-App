<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::all();

        return redirect()->route('categories.index', compact('categories'));
    }

    public function create(){
        return view('tenant.categories.create');
    }

    public function store(CategoryStoreRequest $request, CategoryService $service){
        $request->validated($request->all());

        $service->store([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->color,
            'icon' => $request->icon,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit(Category $category){
        return redirect()->route('categories.edit', compact('category'));
    }

    public function update(CategoryUpdateRequest $request, CategoryService $service, $id){
        $request->validated($request->all());

        $service->update([
            'id' => $id,
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->color,
            'icon' => $request->icon,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diubah');
    }

    public function destroy(CategoryService $service, $id){
        $service->delete($id);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus');
    }
}
