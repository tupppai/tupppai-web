<?php namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use App\Facades\Umeng;

class Push extends Job 
{
    public $text   = '';
    public $tokens = array();
    public $custom = array();

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($custom, $tokens=array(), $data=null)
    {
        parent::__construct();
        #todo: i18n
        $this->text     = '';
        #参数
        $this->tokens   = $tokens;
        $custom['data'] = $data;
        $this->custom   = $custom;
    }

    /**
     * set the text for push
     */
    public function text($text='') {
        $this->text = $text;

        return $this;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Umeng::push($this->text, $this->custom, $this->tokens);
    }
}
