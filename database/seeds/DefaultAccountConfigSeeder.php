<?php

use Illuminate\Database\Seeder;
use App\Models\Config as mConfig;

class DefaultAccountConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $configs = [
			[
				'name' => 'user.staff_time_price_rate',
				'value' => 8,
				'remark' => '兼职帐号时薪'
			],[
				'name' => 'account.withdraw_min_amount',
				'value' => 2,
				'remark' => '提现最低金额'
			],[
				'name' => 'account.withdraw_max_amount',
				'value' => 200,
				'remark' => '提现最高金额'
			]
		];
		mConfig::truncate();
		foreach( $configs as $config ){
			$config['create_time'] = time();
			$config['update_time'] = time();
			mConfig::insert( $config );
		}
    }
}
