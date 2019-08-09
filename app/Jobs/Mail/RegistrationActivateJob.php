<?php
namespace App\Jobs\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationActivateMail;
use App\Models\User;

/**
 * Class RegistrationActivateJob
 * @package App\Jobs\Mail
 */
class RegistrationActivateJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The User instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @patam ProductRepositoryInterface $productRepository
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->email)->send(new RegistrationActivateMail($this->user));
    }
}
