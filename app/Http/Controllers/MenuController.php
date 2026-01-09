<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\MenuItem;


class MenuController extends Controller
{
    public function index(Request $request)
    {
        $selectedCategory = $request->query('category');

        // Get all active categories with their active menu items
        $categories = Category::query()
            ->where('is_active', true)
            ->with([
                'menuItems' => function ($query) {
                    $query->where('is_available', true)
                        ->orderBy('sort_order');
                }
            ])
            ->orderBy('sort_order')
            ->get();

        // Get menu items based on selected category
        $menuItems = MenuItem::query()
            ->where('is_available', true)
            ->when($selectedCategory, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->with(['category', 'optionGroups'])
            ->orderBy('sort_order')
            ->get();

        return view('menu.index', [
            'categories' => $categories,
            'menuItems' => $menuItems,
            'selectedCategory' => $selectedCategory,
        ]);
    }
}
