<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Models\Courier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    // Menampilkan daftar kurir
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:150'],
            'level' => ['sometimes', 'string', 'regex:/^[1-5](,[1-5])*$/'],
            'sort' => ['sometimes', 'in:name,created_at'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $query = Courier::query();

        // Pencarian nama berdasarkan setiap kata
        if (!empty($validated['search'])) {
            $keywords = preg_split(
                '/\s+/',
                trim($validated['search'])
            );

            foreach ($keywords as $keyword) {
                $query->where(
                    'name',
                    'like',
                    '%' . $keyword . '%'
                );
            }
        }

        // Filter level kurir
        if (!empty($validated['level'])) {
            $levels = explode(',', $validated['level']);

            $query->whereIn('level', $levels);
        }

        // Urutkan berdasarkan nama atau tanggal pendaftaran
        $sort = $validated['sort'] ?? 'name';

        if ($sort === 'created_at') {
            $query->orderByDesc('created_at')
                  ->orderBy('id');
        } else {
            $query->orderBy('name')
                  ->orderBy('id');
        }

        $perPage = $validated['per_page'] ?? 10;

        return response()->json(
            $query->paginate($perPage)
        );
    }

    // Menampilkan detail kurir
    public function show(Courier $courier): JsonResponse
    {
        return response()->json([
            'message' => 'Detail kurir berhasil diambil',
            'data' => $courier,
        ]);
    }

    // Menambahkan kurir
    public function store(StoreCourierRequest $request): JsonResponse
    {
        $courier = Courier::create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Kurir berhasil ditambahkan',
            'data' => $courier,
        ], 201);
    }

    // Memperbarui data kurir
    public function update(
        UpdateCourierRequest $request,
        Courier $courier
    ): JsonResponse {
        $courier->update(
            $request->validated()
        );

        return response()->json([
            'message' => 'Kurir berhasil diperbarui',
            'data' => $courier->fresh(),
        ]);
    }

    // Menghapus kurir
    public function destroy(Courier $courier): JsonResponse
    {
        $courier->delete();

        return response()->json([
            'message' => 'Kurir berhasil dihapus',
        ]);
    }
}
