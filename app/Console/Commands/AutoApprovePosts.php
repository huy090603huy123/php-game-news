<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use Carbon\Carbon;

class AutoApprovePosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:approve_posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically approve posts based on created_at';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();

        Post::where('created_at', '<=', $now)
            ->where('approved', 0)
            ->update(['approved' => 1]);

        $this->info('Posts automatically approved!');
    }
}