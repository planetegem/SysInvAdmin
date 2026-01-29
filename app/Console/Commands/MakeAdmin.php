<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Auth\RegisterController;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {mail} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add admin user to DB.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = RegisterController::makeAdmin($this->argument('mail'), $this->argument('password'));
        $this->comment($response);
    }
}
