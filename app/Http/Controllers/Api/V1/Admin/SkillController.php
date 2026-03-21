<?php

namespace App\Http\Controllers\Api\V1\Admin;

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
        $request->validate(['name' => 'required|string|max:100|unique:skills,name']);
        $skill = Skill::create(['name' => $request->name]);
        return response()->json(['skill' => $skill], 201);
    }

    public function update(Request $request, Skill $skill): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:100|unique:skills,name,' . $skill->id]);
        $skill->update(['name' => $request->name]);
        return response()->json(['skill' => $skill]);
    }

    public function destroy(Skill $skill): JsonResponse
    {
        $skill->delete();
        return response()->json(['message' => 'Skill deleted.']);
    }
}
