<?php

namespace App\Console\Commands;

use App\Models\Obituary;
use Illuminate\Console\Command;

class PublishScheduledObituariesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'obituaries:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled obituaries whose scheduled published_at time has arrived.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $scheduledObituaries = Obituary::where('status', 'scheduled')
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->get();

        $count = 0;
        foreach ($scheduledObituaries as $obituary) {
            $obituary->update([
                'status' => 'published',
                'published_at' => $obituary->published_at ?? now(),
            ]);
            $count++;
        }

        $this->info("Successfully published {$count} scheduled obituary notice(s).");
        return Command::SUCCESS;
    }
}
