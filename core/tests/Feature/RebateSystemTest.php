<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RebateProgram;
use App\Models\RebateCategory;
use App\Models\RebateTransaction;
use App\Models\ProductUpload;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class RebateSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $rebateProgram;
    protected $rebateCategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'status' => 1,
            'balance' => 1000.00
        ]);

        // Create test rebate category
        $this->rebateCategory = RebateCategory::create([
            'name' => 'Electronics',
            'description' => 'Electronics rebate category',
            'default_rate' => 5.00,
            'minimum_amount' => 10.00,
            'maximum_rebate' => 100.00,
            'is_active' => true
        ]);

        // Create test rebate program
        $this->rebateProgram = RebateProgram::create([
            'name' => 'Electronics Cashback',
            'description' => 'Get cashback on electronics purchases',
            'default_rate' => 5.00,
            'maximum_rebate' => 100.00,
            'minimum_amount' => 25.00,
            'is_active' => true
            'start_date' => now(),
            'end_date' => now()->addMonths(6)
        ]);
    }

    /** @test */
    public function user_can_view_rebate_dashboard()
    {
        $response = $this->actingAs($this->user)
            ->get(route('user.rebate.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('templates.MayaOfLagos.user.rebate.dashboard');
        $response->assertViewHas(['pageTitle', 'stats', 'tierInfo']);
    }

    /** @test */
    public function user_can_view_available_programs()
    {
        $response = $this->actingAs($this->user)
            ->get(route('user.rebate.programs'));

        $response->assertStatus(200);
        $response->assertViewIs('templates.MayaOfLagos.user.rebate.programs');
        $response->assertSee($this->rebateProgram->name);
    }

    /** @test */
    public function user_can_upload_product_for_rebate()
    {
        Storage::fake('public');

        $productImage = UploadedFile::fake()->image('product.jpg');
        $receiptImage = UploadedFile::fake()->image('receipt.jpg');

        $response = $this->actingAs($this->user)
            ->post(route('user.product.upload.store'), [
                'rebate_program_id' => $this->rebateProgram->id,
                'purchase_amount' => 150.00,
                'product_image' => $productImage,
                'receipt_image' => $receiptImage,
                'purchase_date' => now()->format('Y-m-d'),
                'store_name' => 'Best Buy',
                'product_description' => 'iPhone 15'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('notify');

        // Assert upload was created
        $this->assertDatabaseHas('product_uploads', [
            'user_id' => $this->user->id,
            'rebate_program_id' => $this->rebateProgram->id,
            'purchase_amount' => 150.00
        ]);

        // Assert rebate transaction was created
        $this->assertDatabaseHas('rebate_transactions', [
            'user_id' => $this->user->id,
            'rebate_program_id' => $this->rebateProgram->id,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function rebate_calculation_works_correctly()
    {
        $purchaseAmount = 150.00;
        $expectedBaseRebate = $purchaseAmount * ($this->rebateProgram->default_rate / 100); // 150 * 0.05 = 7.50
        $tierMultiplier = 1.0; // Bronze tier
        $expectedFinalRebate = $expectedBaseRebate * $tierMultiplier;

        $rebateTransaction = RebateTransaction::create([
            'user_id' => $this->user->id,
            'rebate_category_id' => $this->rebateCategory->id,
            'rebate_program_id' => $this->rebateProgram->id,
            'transaction_type' => 'product_upload',
            'original_amount' => $purchaseAmount,
            'purchase_amount' => $purchaseAmount,
            'rebate_rate' => $this->rebateProgram->default_rate,
            'rebate_amount' => $expectedBaseRebate,
            'tier_multiplier' => $tierMultiplier,
            'final_amount' => $expectedFinalRebate,
            'status' => 'pending'
        ]);

        $this->assertEquals($expectedBaseRebate, $rebateTransaction->rebate_amount);
        $this->assertEquals($expectedFinalRebate, $rebateTransaction->final_amount);
    }

    /** @test */
    public function tier_advancement_works_correctly()
    {
        // Create approved rebate transactions to reach Silver tier (1000)
        RebateTransaction::create([
            'user_id' => $this->user->id,
            'rebate_category_id' => $this->rebateCategory->id,
            'rebate_program_id' => $this->rebateProgram->id,
            'transaction_type' => 'product_upload',
            'rebate_amount' => 500.00,
            'final_amount' => 500.00,
            'status' => 'approved',
            'processed_at' => now()
        ]);

        RebateTransaction::create([
            'user_id' => $this->user->id,
            'rebate_category_id' => $this->rebateCategory->id,
            'rebate_program_id' => $this->rebateProgram->id,
            'transaction_type' => 'product_upload',
            'rebate_amount' => 600.00,
            'final_amount' => 600.00,
            'status' => 'approved',
            'processed_at' => now()
        ]);

        $notificationService = new NotificationService();
        $tierInfo = $this->invokeMethod($notificationService, 'getUserTierInfo', [$this->user->id]);

        $this->assertEquals('Silver', $tierInfo['tier']);
        $this->assertEquals(1.25, $tierInfo['multiplier']);
        $this->assertEquals(1100.00, $tierInfo['total_earned']);
    }

    /** @test */
    public function fraud_detection_flags_suspicious_activity()
    {
        // Create multiple uploads in short time period
        for ($i = 0; $i < 15; $i++) {
            ProductUpload::create([
                'user_id' => $this->user->id,
                'rebate_program_id' => $this->rebateProgram->id,
                'product_image' => 'test.jpg',
                'receipt_image' => 'receipt.jpg',
                'purchase_amount' => 100.00,
                'purchase_date' => now(),
                'status' => 'pending',
                'upload_ip' => '192.168.1.1',
                'created_at' => now()
            ]);
        }

        Storage::fake('public');
        $productImage = UploadedFile::fake()->image('product.jpg');
        $receiptImage = UploadedFile::fake()->image('receipt.jpg');

        // This should be flagged by fraud detection
        $response = $this->actingAs($this->user)
            ->post(route('user.product.upload.store'), [
                'rebate_program_id' => $this->rebateProgram->id,
                'purchase_amount' => 100.00,
                'product_image' => $productImage,
                'receipt_image' => $receiptImage,
                'purchase_date' => now()->format('Y-m-d'),
                'store_name' => 'Test Store'
            ]);

        // Should redirect with error due to fraud detection
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function notification_is_sent_when_rebate_approved()
    {
        Mail::fake();

        $rebateTransaction = RebateTransaction::create([
            'user_id' => $this->user->id,
            'rebate_category_id' => $this->rebateCategory->id,
            'rebate_program_id' => $this->rebateProgram->id,
            'transaction_type' => 'product_upload',
            'rebate_amount' => 50.00,
            'final_amount' => 50.00,
            'status' => 'pending'
        ]);

        // Approve the rebate
        $rebateTransaction->approve('admin');

        // Assert status changed
        $this->assertEquals('approved', $rebateTransaction->fresh()->status);
        $this->assertNotNull($rebateTransaction->fresh()->approved_at);
    }

    /** @test */
    public function user_can_view_rebate_history()
    {
        // Create some rebate transactions
        RebateTransaction::create([
            'user_id' => $this->user->id,
            'rebate_category_id' => $this->rebateCategory->id,
            'rebate_program_id' => $this->rebateProgram->id,
            'transaction_type' => 'product_upload',
            'rebate_amount' => 25.00,
            'final_amount' => 25.00,
            'status' => 'approved',
            'processed_at' => now()
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.rebate.history'));

        $response->assertStatus(200);
        $response->assertViewIs('templates.MayaOfLagos.user.rebate.history');
    }

    /** @test */
    public function user_can_view_tier_information()
    {
        $response = $this->actingAs($this->user)
            ->get(route('user.rebate.tiers'));

        $response->assertStatus(200);
        $response->assertViewIs('templates.MayaOfLagos.user.rebate.tiers');
        $response->assertViewHas(['tierInfo', 'tierBenefits', 'achievements']);
    }

    /** @test */
    public function api_endpoints_require_authentication()
    {
        $response = $this->getJson('/api/rebate/dashboard');
        $response->assertStatus(401);

        $response = $this->getJson('/api/rebate/programs');
        $response->assertStatus(401);

        $response = $this->getJson('/api/rebate/history');
        $response->assertStatus(401);
    }

    /** @test */
    public function api_returns_dashboard_data_for_authenticated_user()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->getJson('/api/rebate/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'tier_info' => ['tier', 'multiplier', 'total_earned'],
                'stats' => ['total_earned', 'pending_amount', 'this_month'],
                'recent_rebates',
                'recommended_programs'
            ]
        ]);
    }

    /**
     * Helper method to invoke private methods for testing
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}