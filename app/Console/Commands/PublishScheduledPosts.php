<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use Carbon\Carbon;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled posts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();

        // Lấy danh sách các bài viết đã lên lịch và chưa được đăng
        $posts = Post::where('scheduled_at', '<=', $now)
                    ->where('approved', 0)
                    ->get();

        foreach ($posts as $post) {
            // Đăng bài viết
            $post->approved = 1;
            $post->save();

            $this->info("Published post: {$post->title}");
        }
    }
}
