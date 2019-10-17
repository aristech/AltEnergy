<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\CustomClasses\v1\SendMail;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email with task that should be executed within half hour';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mail = new SendMail();
        //return $mail->checktime('2019-10-17T18:39:00.000Z');
        $mail->getDamages();
        $mail->getEvents();
        $mail->createMessage();
        $mail->sendMail();

        $this->info('Το μήνυμα εστάλη επιτυχώς!');
    }
}
