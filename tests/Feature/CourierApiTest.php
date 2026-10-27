<?php

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index returns paginated couriers sorted by name by default', function () {
    Courier::factory()->create(['name' => 'Charlie Pratama', 'level' => 1]);
    Courier::factory()->create(['name' => 'Andi Wijaya', 'level' => 2]);
    Courier::factory()->create(['name' => 'Budi Santoso', 'level' => 3]);

    $response = $this->getJson('/api/couriers');

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'phone_number',
                    'vehicle_type',
                    'license_plate',
                    'level',
                    'is_active',
                    'created_at',
                    'updated_at',
                ],
            ],
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
        ]);

    $data = $response->json('data');
    expect($data)->toHaveCount(3);
    expect($data[0]['name'])->toBe('Andi Wijaya');
    expect($data[1]['name'])->toBe('Budi Santoso');
    expect($data[2]['name'])->toBe('Charlie Pratama');
});

test('index can sort couriers by created_at override', function () {
    $c1 = Courier::factory()->create([
        'name' => 'Zack',
        'created_at' => now()->subDays(2),
    ]);
    $c2 = Courier::factory()->create([
        'name' => 'Alpha',
        'created_at' => now()->subDay(),
    ]);

    $response = $this->getJson('/api/couriers?sort_by=created_at&sort_order=asc');

    $response->assertOk();
    $data = $response->json('data');
    expect($data[0]['id'])->toBe($c1->id);
    expect($data[1]['id'])->toBe($c2->id);

    $responseDesc = $this->getJson('/api/couriers?sort_by=created_at&sort_order=desc');
    $responseDesc->assertOk();
    $dataDesc = $responseDesc->json('data');
    expect($dataDesc[0]['id'])->toBe($c2->id);
    expect($dataDesc[1]['id'])->toBe($c1->id);
});

test('index can search courier by partial multiple terms', function () {
    Courier::factory()->create(['name' => 'Budiono Hadi Agung']);
    Courier::factory()->create(['name' => 'Budi Sudarsono']);
    Courier::factory()->create(['name' => 'Siti Agung']);

    $response = $this->getJson('/api/couriers?search=budi+agung');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['name'])->toBe('Budiono Hadi Agung');
});

test('index can filter couriers by comma separated levels', function () {
    Courier::factory()->create(['name' => 'Kurir L1', 'level' => 1]);
    Courier::factory()->create(['name' => 'Kurir L2', 'level' => 2]);
    Courier::factory()->create(['name' => 'Kurir L3', 'level' => 3]);
    Courier::factory()->create(['name' => 'Kurir L4', 'level' => 4]);

    $response = $this->getJson('/api/couriers?level=2,3');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(2);

    $levels = array_column($data, 'level');
    sort($levels);
    expect($levels)->toBe([2, 3]);
});

test('show returns courier detail', function () {
    $courier = Courier::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone_number' => '08123456789',
        'level' => 4,
    ]);

    $response = $this->getJson("/api/couriers/{$courier->id}");

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $courier->id,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone_number' => '08123456789',
                'level' => 4,
            ],
        ]);
});

test('show returns 404 if courier not found', function () {
    $response = $this->getJson('/api/couriers/99999');

    $response->assertNotFound()
        ->assertJson([
            'status' => 'error',
        ]);
});

test('store creates courier and saves to database', function () {
    $payload = [
        'name' => 'Bambang Pamungkas',
        'email' => 'bambang@courier.com',
        'phone_number' => '081999888777',
        'vehicle_type' => 'Motorcycle',
        'license_plate' => 'B 1234 ABC',
        'level' => 3,
        'is_active' => true,
    ];

    $response = $this->postJson('/api/couriers', $payload);

    $response->assertCreated()
        ->assertJson([
            'status' => 'success',
            'message' => 'Kurir berhasil ditambahkan.',
            'data' => [
                'name' => 'Bambang Pamungkas',
                'email' => 'bambang@courier.com',
                'level' => 3,
            ],
        ]);

    $this->assertDatabaseHas('couriers', [
        'email' => 'bambang@courier.com',
        'phone_number' => '081999888777',
        'level' => 3,
    ]);
});

test('store fails with custom validation error messages on invalid input', function () {
    $response = $this->postJson('/api/couriers', [
        'name' => '',
        'email' => 'not-an-email',
        'phone_number' => '',
        'level' => 10,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'phone_number', 'level']);

    $errors = $response->json('errors');
    expect($errors['name'][0])->toBe('Nama kurir wajib diisi.');
    expect($errors['email'][0])->toBe('Format email kurir tidak valid.');
    expect($errors['phone_number'][0])->toBe('Nomor telepon kurir wajib diisi.');
    expect($errors['level'][0])->toBe('Level kurir harus berada di antara level 1 hingga 5.');
});

test('update modifies courier and persists in database', function () {
    $courier = Courier::factory()->create([
        'name' => 'Old Name',
        'level' => 1,
    ]);

    $updatePayload = [
        'name' => 'New Courier Name',
        'level' => 5,
        'vehicle_type' => 'Car',
    ];

    $response = $this->putJson("/api/couriers/{$courier->id}", $updatePayload);

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $courier->id,
                'name' => 'New Courier Name',
                'level' => 5,
                'vehicle_type' => 'Car',
            ],
        ]);

    $this->assertDatabaseHas('couriers', [
        'id' => $courier->id,
        'name' => 'New Courier Name',
        'level' => 5,
        'vehicle_type' => 'Car',
    ]);
});

test('destroy removes courier from database', function () {
    $courier = Courier::factory()->create([
        'name' => 'Kurir To Delete',
    ]);

    $this->assertDatabaseHas('couriers', ['id' => $courier->id]);

    $response = $this->deleteJson("/api/couriers/{$courier->id}");

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'message' => 'Data kurir berhasil dihapus.',
        ]);

    $this->assertDatabaseMissing('couriers', ['id' => $courier->id]);
});
