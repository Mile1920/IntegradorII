<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CredencialesTrabajadorMail;
use App\Models\Trabajador;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email? : Email address to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Enter email address to test');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address');
            return 1;
        }

        $this->info("Testing email configuration...");
        $this->info("Sending test email to: {$email}");
        
        try {
            // Crear un trabajador temporal para el test
            $trabajadorTest = new Trabajador([
                'nombre' => 'Test',
                'ap_paterno' => 'Usuario',
                'ap_materno' => null,
                'email' => $email,
            ]);
            
            $password = 'TestPassword123!';
            
            Mail::to($email)->send(new CredencialesTrabajadorMail($trabajadorTest, $password));
            
            $this->info("✓ Email sent successfully!");
            $this->info("Check your inbox at: {$email}");
            
            Log::info("Test email sent successfully to: {$email}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("✗ Failed to send email: " . $e->getMessage());
            $this->error("Check your mail configuration in .env file");
            $this->error("MAIL_MAILER=" . config('mail.default'));
            $this->error("MAIL_HOST=" . config('mail.mailers.smtp.host'));
            $this->error("MAIL_PORT=" . config('mail.mailers.smtp.port'));
            $this->error("MAIL_USERNAME=" . config('mail.mailers.smtp.username'));
            
            Log::error("Test email failed: " . $e->getMessage());
            
            return 1;
        }
    }
}
