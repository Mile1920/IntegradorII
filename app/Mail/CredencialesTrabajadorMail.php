<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CredencialesTrabajadorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trabajador;
    public $password;

    public function __construct($trabajador, $password)
    {
        $this->trabajador = $trabajador;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Tus credenciales de acceso - Mina Porco')
                    ->markdown('emails.credenciales');
    }
}