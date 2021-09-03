<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar usuário principal';

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
     * @return int
     */
    public function handle()
    {
        $email = $this->ask('Digite o email do administrador');
        $password = $this->ask('Digite a senha do administrador');
        $user = $this->argument('user');
        if(User::where('email',$email)->first() || User::where('type', '=', 1)->first() ){
            return $this->error('O sistema ja possuí um administrador');
        }
        DB::table('users')->insert([
            ['name' => $user ,'email' => $email, 'password' => Hash::make($password),'type'=>1]
        ]);
        return $this->info('Usuário cadastrado, email => ' . $email . ', senha => ' . $password);
    }
}
