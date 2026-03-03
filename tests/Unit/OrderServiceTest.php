<?php

namespace Tests\Unit;

use App\Models\Buyer;
use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use App\Models\Voucher;
use App\Services\Api\OrderService;
use App\Services\FcmNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $orderService;
    protected $buyerUser;
    protected $seller;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $fcmMock = $this->createMock(FcmNotificationService::class);
        $this->orderService = new OrderService($fcmMock);

        $this->buyerUser = User::factory()->create();
        $buyer = Buyer::create([
            'user_id' => $this->buyerUser->id,
            'name' => 'Buyer',
            'phone' => '123',
            'address' => '',
            'photo_path' => ''
        ]);

        $sellerUser = User::factory()->create();
        $this->seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Seller',
            'address' => '',
            'phone' => '',
            'is_active' => true,
            'photo_path' => '',
            'status' => 'online',
        ]);

        $this->product = Product::create([
            'seller_id' => $this->seller->id,
            'name' => 'Item 1',
            'price' => 50000,
            'stock' => 5,
            'photo_path' => '',
            'is_active' => true,
        ]);
    }

    public function test_create_order_calculates_total_correctly()
    {
        $data = [
            'seller_id' => $this->seller->id,
            'payment_method' => 'COD',
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 2] // 2 * 50000 = 100000
            ]
        ];

        $order = $this->orderService->createOrder($this->buyerUser, $data);

        $this->assertEquals(100000, $order->total_price);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(3, $this->product->fresh()->stock); // 5 - 2
    }

    public function test_create_order_applies_voucher_discount()
    {
        $voucher = Voucher::create([
            'code' => 'DISC10K',
            'type' => 'fixed',
            'value' => 10000,
            'min_purchase' => 50000,
            'max_discount' => 10000,
            'valid_until' => now()->addDays(2),
            'limit' => 10,
            'claimed_count' => 0,
        ]);

        $data = [
            'seller_id' => $this->seller->id,
            'payment_method' => 'TRANSFER',
            'voucher_code' => 'DISC10K',
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 2] // Total 100000, Diskon 10000 = 90000
            ]
        ];

        $order = $this->orderService->createOrder($this->buyerUser, $data);

        $this->assertEquals(100000, $order->total_price); // total price sebelum dikonversi
        $this->assertEquals(10000, $order->discount_amount);
        $this->assertEquals($voucher->id, $order->voucher_id);
    }
}
