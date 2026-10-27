<?php

namespace App\Repositories;

use App\Models\Courier;
use App\Repositories\Contracts\CourierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourierRepository implements CourierRepositoryInterface
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
    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        $query = Courier::query();

        // Search: support matching words, e.g. "budi agung" -> match "Budiono Hadi Agung"
        if (! empty($filters['search'])) {
            $rawSearch = trim((string) $filters['search']);
            $keywords = preg_split('/\s+/', $rawSearch, -1, PREG_SPLIT_NO_EMPTY);

            if (! empty($keywords)) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->where('name', 'like', '%' . $word . '%');
                    }
                });
            }
        }

        // Filter by level: e.g. "2,3" or [2, 3]
        if (! empty($filters['level'])) {
            $levels = $filters['level'];
            if (is_string($levels)) {
                $levels = array_filter(array_map('trim', explode(',', $levels)), fn ($val) => is_numeric($val));
            }

            if (! empty($levels)) {
                $query->whereIn('level', $levels);
            }
        }

        // Sorting: default name ASC, or created_at (didaptarkan) override
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortOrder = strtolower($filters['sort_order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        if (in_array($sortBy, ['created_at', 'name', 'level', 'id'], true)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('name', 'asc');
        }

        $perPage = ! empty($filters['per_page']) && is_numeric($filters['per_page'])
            ? (int) $filters['per_page']
            : 15;

        return $query->paginate($perPage);
    }

    /**
     * Find courier by ID.
     */
    public function findById(int $id): ?Courier
    {
        return Courier::find($id);
    }

    /**
     * Create a new courier record.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Courier
    {
        return Courier::create($data);
    }

    /**
     * Update an existing courier record.
     *
     * @param array<string, mixed> $data
     */
    public function update(Courier $courier, array $data): Courier
    {
        $courier->update($data);

        return $courier->fresh();
    }

    /**
     * Delete a courier record.
     */
    public function delete(Courier $courier): bool
    {
        return (bool) $courier->delete();
    }
}
