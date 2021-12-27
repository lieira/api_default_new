<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Process\Process;

class BackupCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        if (!is_dir(storage_path('backups'))) {
            mkdir(storage_path('backups'));
        }

        $backup = 'backups/' . date('d_m_Y') . '.sql';
        $data = new Process(sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            storage_path($backup)
        ));

        $data->mustRun();

        try {

            $message = view('mail.backup')->render();

            Mail::to(env('BACKUP_EMAIL','teste@gmail.com'))->send(new SendBackup('Backup - '.env('APP_NAME','A CONFIGURAR'), $message, $backup));
            return response()->json([
                'success' => true,
                'message' => 'Backup enviado com sucesso',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Falha ao enviar o backup',
            ]);
        }
    }
}
