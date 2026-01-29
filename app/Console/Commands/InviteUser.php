<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Auth\RegisterController;

class InviteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invite:user {mail} secret={secret}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invite user to use app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = RegisterController::invite($this->argument('mail'), $this->argument('secret'));
        $this->comment($response);
    }
}
