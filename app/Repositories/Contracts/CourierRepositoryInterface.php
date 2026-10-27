<?php

namespace App\Repositories\Contracts;

use App\Models\Courier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CourierRepositoryInterface
{
    /**
     * Get paginated couriers with filtering, search, and sorting.
     *
     * @param array{
     *     search?: string|null,
     *     level?: string|array<int, int|string>|null,
     *     sort_by?: string|null,
     *     sort_order?: string|null,
     *     per_page?: int|null
     * } $filters
     */
    public function getPaginated(array $filters = []): LengthAwarePaginator;

    /**
     * Find courier by ID.
     */
    public function findById(int $id): ?Courier;

    /**
     * Create a new courier record.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Courier;

    /**
     * Update an existing courier record.
     *
     * @param array<string, mixed> $data
     */
    public function update(Courier $courier, array $data): Courier;

    /**
     * Delete a courier record.
     */
    public function delete(Courier $courier): bool;
}
