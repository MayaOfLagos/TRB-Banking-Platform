<?php

namespace App\Jobs;

use App\Models\ProductUpload;
use App\Services\RebateProcessingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessRebateJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $productUpload;
    protected $rebateType;

    /**
     * Create a new job instance.
     */
    public function __construct(ProductUpload $productUpload, string $rebateType = 'product')
    {
        $this->productUpload = $productUpload;
        $this->rebateType = $rebateType;
    }

    /**
     * Execute the job.
     */
    public function handle(RebateProcessingService $processingService): void
    {
        try {
            Log::info('Processing rebate job started', [
                'product_upload_id' => $this->productUpload->id,
                'user_id' => $this->productUpload->user_id,
                'type' => $this->rebateType
            ]);

            $result = $processingService->processProductRebateSync($this->productUpload);

            Log::info('Processing rebate job completed', [
                'product_upload_id' => $this->productUpload->id,
                'success' => $result['success'],
                'message' => $result['message']
            ]);

        } catch (\Exception $e) {
            Log::error('Processing rebate job failed', [
                'product_upload_id' => $this->productUpload->id,
                'error' => $e->getMessage()
            ]);

            // Optionally fail the job to retry later
            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception): void
    {
        Log::error('ProcessRebateJob failed permanently', [
            'product_upload_id' => $this->productUpload->id,
            'error' => $exception->getMessage()
        ]);
    }
}
