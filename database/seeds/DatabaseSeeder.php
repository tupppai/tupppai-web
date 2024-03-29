<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('default_roles');
		$this->call(DefaultCategoriesSeeder::class);
		$this->call(DefaultAccountConfigSeeder::class);
		$this->call(DefaultProductSeeder::class);
	}

}
