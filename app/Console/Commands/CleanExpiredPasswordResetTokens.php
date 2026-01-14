<?php

namespace App\Console\Commands;

use App\Domain\Repositories\PasswordResetTokenRepositoryInterface;
use Illuminate\Console\Command;

class CleanExpiredPasswordResetTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:clean-expired 
                            {--hours=24 : Delete tokens older than this many hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired password reset tokens from the database';

    public function __construct(
        private PasswordResetTokenRepositoryInterface $tokenRepository
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $expirationTime = now()->subHours($hours);

        $deleted = $this->tokenRepository->deleteExpired($expirationTime);

        $this->info("Deleted {$deleted} expired password reset token(s) older than {$hours} hour(s).");

        return Command::SUCCESS;
    }
}

