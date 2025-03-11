<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemCategory;

class ItemCategoryController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::all();
        return view('purchasing_officer.item_category', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:item_categories,name',
            'description' => 'nullable|string',
        ]);

        ItemCategory::create($request->all());

        return redirect()->back()->with('success', 'Category added successfully');
    }
}
