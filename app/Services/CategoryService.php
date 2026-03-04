<?php

namespace App\Services;

use App\Models\Category;

class CategoryService extends BaseService
{
    public function __construct(public Category $category)
    {
    }

    public function store(array $data){
        $this->category->create([
            'tenant_id' => $data['tenant_id'],
            'name' => $data['name'],
            'type' => $data['type'],
            'color' => $data['color'],
            'icon' => $data['icon'],
        ]);

        return $this->category;
    }

    public function update(array $data){

        $this->category = $this->category->find($data['id']);

        $this->category->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'color' => $data['color'],
            'icon' => $data['icon'],
        ]);

        return $this->category;
    }

    public function delete($id){
        $this->category->find($id)->delete();
    }
}