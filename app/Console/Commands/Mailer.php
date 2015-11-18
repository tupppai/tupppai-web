<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;

class Mailer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "邮件推送测试包";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->layout = '';

        $email = 'billqiang@qq.com';
        $name  = 'junqiang';

        $data = ['email'=>$email, 'name'=>$name];

        Mail::send('test', $data, function($message) use($data) {
            $message->to($data['email'], $data['name'])->subject('欢迎注册我们的网站，请激活您的账号！');
        });
    }
}
