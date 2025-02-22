<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use SimplePie\SimplePie;

class ParseRssCommand extends Command
{
    protected $signature = 'rss:parse';
    protected $description = 'Parse RSS feed and save new posts';

    public function handle()
    {
        $feedUrl = 'https://lifehacker.com/rss';
        $feed = new SimplePie();
        $feed->set_cache_location(storage_path('rss'));
        $feed->set_cache_duration(3600);
        $feed->set_feed_url($feedUrl);
        $feed->init();
        $feed->handle_content_type();

        foreach ($feed->get_items() as $item) {
            $link = $item->get_link();
            $existingPost = Post::where('link', $link)->exists();

            if (!$existingPost) {
                Post::create([
                    'title'       => $item->get_title(),
                    'link'        => $link,
                    'description' => $item->get_description(),
                    'pub_date'    => $item->get_date('Y-m-d H:i:s'),
                ]);
            }
        }

        $this->info('RSS feed parsed successfully.');
    }
}
