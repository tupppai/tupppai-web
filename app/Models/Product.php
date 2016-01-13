<?php namespace App\Models;

class Product extends ModelBase{
	public $table = 'products';

	public function get_product_by_id( $id ){
		return $this->find( $id );
	}

	public function update_status( $id, $status ){

	}
	/*********** getter & setter *************/
	//getter
	public function get_id(){
		return $this->id;
	}
	public function get_name(){
		return $this->name;
	}
	public function get_desc(){
		return $this->desc;
	}
	public function get_remark(){
		return $this->remark;
	}
	public function get_price(){
		return $this->price;
	}
	public function get_status(){
		return $this->status;
	}
	public function get_create_by(){
		return $this->create_by;
	}
	public function get_update_by(){
		return $this->update_by;
	}

	//setter
	public function set_id( $id ){
		$this->id = $id;
		return $this;
	}
	public function set_name( $name ){
		$this->name = $name;
		return $this;
	}
	public function set_desc( $desc ){
		$this->desc = $desc;
		return $this;
	}
	public function set_remark( $remark ){
		$this->remark = $remark;
		return $this;
	}
	public function set_price( $price ){
		$this->price = $price;
		return $this;
	}
	public function set_status( $status ){
		$this->status = $status;
		return $this;
	}
	public function set_create_by( $create_by ){
		$this->create_by = $create_by;
		return $this;
	}
	public function set_update_by( $update_by ){
		$this->update_by = $update_by;
		return $this;
	}
}
