<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Skill::orderBy('name')->select(['id', 'name']);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $perPage = min((int) $request->input('per_page', 15), 50);

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:100']);

        $existing = Skill::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->first();
        if ($existing) {
            return response()->json(['skill' => $existing, 'existed' => true]);
        }

        $skill = Skill::create(['name' => $request->name]);
        return response()->json(['skill' => $skill, 'existed' => false], 201);
    }
}
