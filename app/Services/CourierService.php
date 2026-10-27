<?php

namespace App\Services;

use App\Models\Courier;
use App\Repositories\Contracts\CourierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CourierService
{
    public function __construct(
        protected CourierRepositoryInterface $courierRepository
    ) {
    }

    /**
     * List couriers with filters.
     *
     * @param array<string, mixed> $filters
     */
    public function getCouriers(array $filters): LengthAwarePaginator
    {
        return $this->courierRepository->getPaginated($filters);
    }

    /**
     * Get single courier by ID.
     */
    public function getCourierById(int $id): Courier
    {
        $courier = $this->courierRepository->findById($id);

        if (! $courier) {
            abort(404, 'Data kurir tidak ditemukan.');
        }

        return $courier;
    }

    /**
     * Create courier. Wrapped in DB transaction per SOP.
     *
     * @param array<string, mixed> $data
     */
    public function createCourier(array $data): Courier
    {
        return DB::transaction(function () use ($data) {
            return $this->courierRepository->create($data);
        });
    }

    /**
     * Update courier. Wrapped in DB transaction per SOP.
     *
     * @param array<string, mixed> $data
     */
    public function updateCourier(Courier $courier, array $data): Courier
    {
        return DB::transaction(function () use ($courier, $data) {
            return $this->courierRepository->update($courier, $data);
        });
    }

    /**
     * Delete courier. Wrapped in DB transaction per SOP.
     */
    public function deleteCourier(Courier $courier): bool
    {
        return DB::transaction(function () use ($courier) {
            return $this->courierRepository->delete($courier);
        });
    }
}
