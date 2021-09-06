<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class newMailAlc extends Mailable
{
    use Queueable, SerializesModels;

    private $name;
    private $email;
    private $assunto;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $email, $assunto)
    {
        $this->name = $name;
        $this->email = $email;
        $this->assunto = $assunto;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject($this->assunto);
        $this->to($this->email,$this->name);
        return $this->view('mail.newMailAlc',[
            'name'=>$this->name,
            'email'=>$this->email,
            'assunto'=>$this->assunto
        ]);
    }
}
