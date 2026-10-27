<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dapat_menambahkan_kurir(): void
    {
        $response = $this->postJson('/api/couriers', [
            'name' => 'Budiono Hadi Agung',
            'phone' => '081234567890',
            'email' => 'budiono@example.com',
            'level' => 2,
            'is_active' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Budiono Hadi Agung');

        $this->assertDatabaseHas('couriers', [
            'phone' => '081234567890',
            'level' => 2,
        ]);
    }

    public function test_validasi_level_tidak_boleh_di_luar_1_sampai_5(): void
    {
        $response = $this->postJson('/api/couriers', [
            'name' => 'Kurir Testing',
            'phone' => '081234567891',
            'level' => 6,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['level']);

        $this->assertDatabaseCount('couriers', 0);
    }

    public function test_nomor_telepon_tidak_boleh_duplikat(): void
    {
        Courier::factory()->create([
            'phone' => '081234567890',
        ]);

        $response = $this->postJson('/api/couriers', [
            'name' => 'Kurir Baru',
            'phone' => '081234567890',
            'level' => 3,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);

        $this->assertDatabaseCount('couriers', 1);
    }

    public function test_dapat_melihat_detail_kurir(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->getJson(
            "/api/couriers/{$courier->id}"
        );

        $response->assertOk()
            ->assertJsonPath('data.id', $courier->id);
    }

    public function test_dapat_memperbarui_kurir(): void
    {
        $courier = Courier::factory()->create([
            'level' => 2,
        ]);

        $response = $this->putJson(
            "/api/couriers/{$courier->id}",
            ['level' => 4]
        );

        $response->assertOk()
            ->assertJsonPath('data.level', 4);

        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'level' => 4,
        ]);
    }

    public function test_update_level_tidak_valid_ditolak(): void
    {
        $courier = Courier::factory()->create([
            'level' => 2,
        ]);

        $response = $this->putJson(
            "/api/couriers/{$courier->id}",
            ['level' => 8]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['level']);

        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'level' => 2,
        ]);
    }

    public function test_dapat_menghapus_kurir(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->deleteJson(
            "/api/couriers/{$courier->id}"
        );

        $response->assertOk();

        $this->assertDatabaseMissing('couriers', [
            'id' => $courier->id,
        ]);
    }

    public function test_pencarian_nama_dengan_beberapa_kata(): void
    {
        Courier::factory()->create([
            'name' => 'Budiono Hadi Agung',
        ]);

        Courier::factory()->create([
            'name' => 'Andi Saputra',
        ]);

        $response = $this->getJson(
            '/api/couriers?search=budi+agung'
        );

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath(
                'data.0.name',
                'Budiono Hadi Agung'
            );
    }

    public function test_filter_berdasarkan_beberapa_level(): void
    {
        Courier::factory()->create(['level' => 2]);
        Courier::factory()->create(['level' => 3]);
        Courier::factory()->create(['level' => 5]);

        $response = $this->getJson(
            '/api/couriers?level=2,3'
        );

        $response->assertOk()
            ->assertJsonPath('total', 2);
    }

    public function test_pagination_berjalan(): void
    {
        Courier::factory()->count(15)->create();

        $response = $this->getJson(
            '/api/couriers?per_page=5&page=2'
        );

        $response->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonPath('per_page', 5)
            ->assertJsonPath('total', 15)
            ->assertJsonCount(5, 'data');
    }

    public function test_default_sort_berdasarkan_nama(): void
    {
        Courier::factory()->create([
            'name' => 'Zaki Pratama',
        ]);

        Courier::factory()->create([
            'name' => 'Andi Saputra',
        ]);

        $response = $this->getJson('/api/couriers');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Andi Saputra')
            ->assertJsonPath('data.1.name', 'Zaki Pratama');
    }

    public function test_sort_berdasarkan_tanggal_terbaru(): void
    {
        Courier::factory()->create([
            'name' => 'Kurir Lama',
            'created_at' => '2026-01-01 10:00:00',
        ]);

        Courier::factory()->create([
            'name' => 'Kurir Baru',
            'created_at' => '2026-02-01 10:00:00',
        ]);

        $response = $this->getJson(
            '/api/couriers?sort=created_at'
        );

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Kurir Baru')
            ->assertJsonPath('data.1.name', 'Kurir Lama');
    }
}
