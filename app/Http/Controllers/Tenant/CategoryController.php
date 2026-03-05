<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::all();

        return Inertia::render('Category/index', ['categories' => $categories]);
    }

    public function create(){
        return Inertia::render('Category/create');
    }

    public function store(CategoryStoreRequest $request, CategoryService $service){
        $request->validated();

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
        return Inertia::render('Category/edit', compact('category'));
    }

    public function update(CategoryUpdateRequest $request, CategoryService $service, $id){
        $request->validated();

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

    public function bulkDestroy(Request $request, categoryService $service) 
    {   
        $ids = $request::input('ids', []);
            
        foreach ($ids as $id) {
            $service->delete($id);
        }

        return redirect()->route('categories.index')
                        ->with('success', count($ids) . ' kategori berhasil dihapus');
    }
}
