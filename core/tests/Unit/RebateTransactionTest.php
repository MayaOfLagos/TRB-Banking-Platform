<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\RebateTransaction;

class RebateTransactionTest extends TestCase
{
    /** @test */
    public function it_calculates_status_badge_color_correctly()
    {
        $rebate = new RebateTransaction();
        
        $rebate->status = 'pending';
        $this->assertEquals('yellow', $rebate->getStatusBadgeColor());
        
        $rebate->status = 'approved';
        $this->assertEquals('green', $rebate->getStatusBadgeColor());
        
        $rebate->status = 'rejected';
        $this->assertEquals('red', $rebate->getStatusBadgeColor());
        
        $rebate->status = 'failed';
        $this->assertEquals('red', $rebate->getStatusBadgeColor());
        
        $rebate->status = 'unknown';
        $this->assertEquals('gray', $rebate->getStatusBadgeColor());
    }

    /** @test */
    public function it_updates_status_correctly_when_approved()
    {
        $rebate = new RebateTransaction();
        $rebate->status = 'pending';
        
        $rebate->approve('admin', 'Approved after review');
        
        $this->assertEquals('approved', $rebate->status);
        $this->assertEquals('Approved after review', $rebate->review_notes);
        $this->assertNotNull($rebate->approved_at);
    }

    /** @test */
    public function it_updates_status_correctly_when_rejected()
    {
        $rebate = new RebateTransaction();
        $rebate->status = 'pending';
        
        $rebate->reject('admin', 'Invalid receipt');
        
        $this->assertEquals('rejected', $rebate->status);
        $this->assertEquals('admin', $rebate->rejected_by);
        $this->assertEquals('Invalid receipt', $rebate->review_notes);
        $this->assertNotNull($rebate->rejected_at);
    }
}