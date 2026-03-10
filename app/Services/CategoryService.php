<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class CategoryService extends BaseService
{
    public function __construct(public Category $category)
    {
    }

    public function store(array $data){
        
        $this->category->create([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $data['name'],
            'type' => $data['type'],
            'color' => $data['color'],
            'icon' => $data['icon'],
            'is_default' => false,
        ]);

        return $this->category;
    }

    public function update(array $data, $id){

        $this->category = $this->category->find($id);

        $this->category->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'color' => $data['color'],
            'icon' => $data['icon'],
        ]);

        return $this->category;
    }

    public function delete($id){

        $category = $this->category->find($id);
        $hasTransaction = Transaction::where('tenant_id', Auth::user()->tenant_id)
        ->where('category_id', $category->id)
        ->exists();

        if($category->is_default == true){
            abort(403, 'Kategori Default tidak dapat dihapus');
        }elseif($hasTransaction){
            abort(403, 'Kategori ini digunakan di transaksi, tidak dapat dihapus');
        }

        $category->delete();
    }
}