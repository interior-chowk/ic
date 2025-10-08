<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class StorageLinkRelative extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Using storage:link:relative to avoid colliding with the default.
     *
     * @var string
     */
    protected $signature = 'storage:link:relative';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a relative storage symlink (public/storage -> ../storage/app/public)';

    public function handle()
    {
        $link = public_path('storage');                 // public/storage
        $relativeTarget = '../storage/app/public';     // relative path from public/
        $absoluteTarget = base_path('storage/app/public'); // absolute path used for sanity checks

        // If the target doesn't exist, warn but still create the symlink (user might create storage later)
        if (!is_dir($absoluteTarget)) {
            $this->warn("Warning: target directory does not exist: {$absoluteTarget}");
            $this->warn("The symlink will still be created but make sure the storage path exists for uploads to work.");
        }

        // If a file/symlink/directory already exists at public/storage, remove it safely
        if (is_link($link) || file_exists($link)) {
            // If it's a symlink, unlink it. If it's a directory, attempt to remove only if empty
            if (is_link($link)) {
                @unlink($link);
                $this->info("Removed existing symlink: {$link}");
            } elseif (is_dir($link)) {
                // If it's an actual directory, don't remove it blindly — warn the user
                $this->error("A directory already exists at {$link}. Please remove or rename it before creating the symlink.");
                return 1;
            } else {
                // regular file
                @unlink($link);
                $this->info("Removed existing file: {$link}");
            }
        }

        // Create the relative symlink
        try {
            // use the built-in symlink function; it creates the link with the provided target string as-is
            symlink($relativeTarget, $link);

            // verify
            if (is_link($link)) {
                $this->info("Relative storage link created: public/storage -> {$relativeTarget}");
                return 0;
            } else {
                $this->error("Failed to create symlink. You may need proper permissions.");
                return 1;
            }
        } catch (\Throwable $e) {
            $this->error("Exception while creating symlink: " . $e->getMessage());
            return 1;
        }
    }
}

