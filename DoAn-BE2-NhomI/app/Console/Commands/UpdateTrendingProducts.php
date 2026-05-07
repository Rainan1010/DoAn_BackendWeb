<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateTrendingProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-trending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update trending products based on top 20 view count';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update trending products...');

        try {
            DB::transaction(function () {
                // Lấy 20 sản phẩm có view_count cao nhất
                $top20Ids = DB::table('products')
                    ->where('is_active', 1)
                    ->orderBy('view_count', 'desc')
                    ->limit(20)
                    ->pluck('product_id');

                // Bỏ check trending những sản phẩm ngoài top
                DB::table('products')
                    ->whereNotIn('product_id', $top20Ids)
                    ->update(['is_trending' => 0]);

                // Set trending cho những sản phẩm trong top
                DB::table('products')
                    ->whereIn('product_id', $top20Ids)
                    ->update(['is_trending' => 1]);
            });

            $this->info('Successfully updated trending products!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to update trending products: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
