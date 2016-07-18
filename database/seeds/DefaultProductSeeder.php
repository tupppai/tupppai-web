<?php

use Illuminate\Database\Seeder;
use App\Models\Product as mProduct;

class DefaultProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product = [
            [
                'name' => '求助',
                'desc' => '求助',
                'remark' => '发布求助扣5毛',
                'price' => 500,
                'status' => mProduct::STATUS_NORMAL
            ]
		];
		mProduct::truncate();
		foreach( $product as $product ){
			$product['create_time'] = time();
			$product['update_time'] = time();
			mProduct::insert( $product );
		}
    }
}
