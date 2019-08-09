<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class RegistrationActivateMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The User instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailTemplateRepository = resolve('App\Repositories\Interfaces\EmailTemplate\EmailTemplateRepositoryInterface');
        
        $template = $emailTemplateRepository->findByName('registration.activate', [
            'code' => $this->user->activation_code,
            'email' => $this->user->email,
        ]);
        
        return $this->html($template->body)->subject($template->subject);
    }
}
