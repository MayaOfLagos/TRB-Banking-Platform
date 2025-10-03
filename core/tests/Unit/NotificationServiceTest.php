<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\RebateProgram;
use App\Models\RebateCategory;
use App\Models\RebateTransaction;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $notificationService;
    protected $user;
    protected $rebateTransaction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationService = new NotificationService();
        
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'status' => 1
        ]);

        $category = RebateCategory::create([
            'name' => 'Test Category',
            'default_rate' => 5.00,
            'is_active' => true
        ]);

        $program = RebateProgram::create([
            'name' => 'Test Program',
            'rebate_category_id' => $category->id,
            'default_rate' => 5.00,
            'is_active' => true
        ]);

        $this->rebateTransaction = RebateTransaction::create([
            'user_id' => $this->user->id,
            'rebate_category_id' => $category->id,
            'rebate_program_id' => $program->id,
            'transaction_type' => 'product_upload',
            'rebate_amount' => 50.00,
            'final_amount' => 50.00,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function it_calculates_correct_tier_info()
    {
        // Create approved transactions to test tier calculation
        RebateTransaction::create([
            'user_id' => $this->user->id,
            'rebate_category_id' => $this->rebateTransaction->rebate_category_id,
            'rebate_program_id' => $this->rebateTransaction->rebate_program_id,
            'transaction_type' => 'product_upload',
            'rebate_amount' => 800.00,
            'final_amount' => 800.00,
            'status' => 'approved'
        ]);

        $tierInfo = $this->invokeMethod($this->notificationService, 'getUserTierInfo', [$this->user->id]);

        $this->assertEquals('Bronze', $tierInfo['tier']);
        $this->assertEquals(1.0, $tierInfo['multiplier']);
        $this->assertEquals(800.00, $tierInfo['total_earned']);
    }

    /** @test */
    public function it_detects_tier_advancement()
    {
        // Add transactions to reach Silver tier (1000+)
        RebateTransaction::create([
            'user_id' => $this->user->id,
            'rebate_category_id' => $this->rebateTransaction->rebate_category_id,
            'rebate_program_id' => $this->rebateTransaction->rebate_program_id,
            'transaction_type' => 'product_upload',
            'rebate_amount' => 1200.00,
            'final_amount' => 1200.00,
            'status' => 'approved'
        ]);

        $newTier = $this->notificationService->checkTierAdvancement($this->user);

        $this->assertEquals('Silver', $newTier);
    }

    /** @test */
    public function it_calculates_tier_progress_correctly()
    {
        // Add 600 in approved rebates (Bronze tier, 400 away from Silver)
        RebateTransaction::create([
            'user_id' => $this->user->id,
            'rebate_category_id' => $this->rebateTransaction->rebate_category_id,
            'rebate_program_id' => $this->rebateTransaction->rebate_program_id,
            'transaction_type' => 'product_upload',
            'rebate_amount' => 600.00,
            'final_amount' => 600.00,
            'status' => 'approved'
        ]);

        $tierInfo = $this->invokeMethod($this->notificationService, 'getUserTierInfo', [$this->user->id]);
        $tierProgress = $this->invokeMethod($this->notificationService, 'calculateTierProgress', [$this->user, $tierInfo]);

        $this->assertEquals('Bronze', $tierProgress['current_tier']);
        $this->assertEquals('Silver', $tierProgress['next_tier']);
        $this->assertEquals(400.00, $tierProgress['amount_to_next']);
        $this->assertEquals(60.0, $tierProgress['progress_percentage']); // 600/1000 * 100
    }

    /** @test */
    public function it_identifies_next_tier_correctly()
    {
        $nextTier = $this->invokeMethod($this->notificationService, 'getNextTier', ['Bronze']);
        $this->assertEquals('Silver', $nextTier);

        $nextTier = $this->invokeMethod($this->notificationService, 'getNextTier', ['Silver']);
        $this->assertEquals('Gold', $nextTier);

        $nextTier = $this->invokeMethod($this->notificationService, 'getNextTier', ['Gold']);
        $this->assertEquals('Platinum', $nextTier);

        $nextTier = $this->invokeMethod($this->notificationService, 'getNextTier', ['Platinum']);
        $this->assertNull($nextTier);
    }

    /** @test */
    public function it_handles_max_tier_correctly()
    {
        // Add transactions to reach Platinum tier
        RebateTransaction::create([
            'user_id' => $this->user->id,
            'rebate_category_id' => $this->rebateTransaction->rebate_category_id,
            'rebate_program_id' => $this->rebateTransaction->rebate_program_id,
            'transaction_type' => 'product_upload',
            'rebate_amount' => 20000.00,
            'final_amount' => 20000.00,
            'status' => 'approved'
        ]);

        $tierInfo = $this->invokeMethod($this->notificationService, 'getUserTierInfo', [$this->user->id]);
        $tierProgress = $this->invokeMethod($this->notificationService, 'calculateTierProgress', [$this->user, $tierInfo]);

        $this->assertEquals('Platinum', $tierInfo['tier']);
        $this->assertEquals(2.0, $tierInfo['multiplier']);
        $this->assertNull($tierProgress['next_tier']);
        $this->assertEquals(0, $tierProgress['amount_to_next']);
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