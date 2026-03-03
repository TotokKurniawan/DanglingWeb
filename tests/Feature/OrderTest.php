<?php

namespace Tests\Feature;

use App\Models\Buyer;
use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $buyerUser;
    protected $sellerUser;
    protected $buyer;
    protected $seller;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('passport:install');

        // Setup Buyer
        $this->buyerUser = User::factory()->create();
        $this->buyer = Buyer::create([
            'user_id' => $this->buyerUser->id,
            'name' => 'Buyer Test',
            'phone' => '08111',
            'address' => 'Jakarta',
            'photo_path' => ''
        ]);
        $this->buyerUser->assignRole('buyer');

        // Setup Seller
        $this->sellerUser = User::factory()->create();
        $this->seller = Seller::create([
            'user_id' => $this->sellerUser->id,
            'store_name' => 'Toko Test',
            'store_description' => 'Desc',
            'address' => 'Bandung',
            'phone' => '08222',
            'latitude' => 0.0,
            'longitude' => 0.0,
            'is_active' => true,
            'photo_path' => '',
            'status' => 'online',
        ]);
        $this->sellerUser->assignRole('seller');

        // Setup Product
        $category = Category::create(['name' => 'Makanan']);
        $this->product = Product::create([
            'seller_id' => $this->seller->id,
            'category_id' => $category->id,
            'name' => 'Nasi Goreng Test',
            'description' => 'Enak',
            'price' => 20000,
            'stock' => 10,
            'photo_path' => 'test.jpg',
            'is_active' => true,
        ]);
    }

    public function test_buyer_can_create_order()
    {
        $response = $this->actingAs($this->buyerUser, 'api')
                         ->postJson('/api/orders', [
                             'seller_id' => $this->seller->id,
                             'payment_method' => 'COD',
                             'notes' => 'Pedes dikit',
                             'items' => [
                                 [
                                     'product_id' => $this->product->id,
                                     'quantity' => 2
                                 ]
                             ]
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', [
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'total_price' => 40000,
            'payment_method' => 'COD',
            'notes' => 'Pedes dikit',
        ]);
        
        // Cek stok berkurang
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'stock' => 8
        ]);
    }

    public function test_seller_can_accept_order()
    {
        $order = Order::create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'total_price' => 20000,
            'status' => 'pending',
            'payment_method' => 'COD',
        ]);

        $response = $this->actingAs($this->sellerUser, 'api')
                         ->patchJson("/api/orders/{$order->id}/accept");

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'accepted'
        ]);
    }
}
