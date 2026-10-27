<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterCourierRequest;
use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Http\Resources\CourierResource;
use App\Models\Courier;
use App\Services\CourierService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CourierController extends Controller
{
    public function __construct(
        protected CourierService $courierService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FilterCourierRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated();
            $couriers = $this->courierService->getCouriers($filters);

            return response()->json([
                'status' => 'success',
                'message' => 'Daftar kurir berhasil diambil.',
                'data' => CourierResource::collection($couriers),
                'meta' => [
                    'current_page' => $couriers->currentPage(),
                    'last_page' => $couriers->lastPage(),
                    'per_page' => $couriers->perPage(),
                    'total' => $couriers->total(),
                ],
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil daftar kurir: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourierRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $courier = $this->courierService->createCourier($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Kurir berhasil ditambahkan.',
                'data' => new CourierResource($courier),
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan kurir: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $courier = $this->courierService->getCourierById($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Detail kurir berhasil diambil.',
                'data' => new CourierResource($courier),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() <= 500 ? $e->getCode() : Response::HTTP_NOT_FOUND;

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'Data kurir tidak ditemukan.',
            ], $statusCode);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourierRequest $request, Courier $courier): JsonResponse
    {
        try {
            $data = $request->validated();
            $updatedCourier = $this->courierService->updateCourier($courier, $data);

            return response()->json([
                'status' => 'success',
                'message' => 'Data kurir berhasil diperbarui.',
                'data' => new CourierResource($updatedCourier),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data kurir: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courier $courier): JsonResponse
    {
        try {
            $this->courierService->deleteCourier($courier);

            return response()->json([
                'status' => 'success',
                'message' => 'Data kurir berhasil dihapus.',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data kurir: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
