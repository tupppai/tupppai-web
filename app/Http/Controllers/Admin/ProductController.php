<?php
namespace App\Http\Controllers\Admin;

use App\Services\Product as sProduct;
use App\Models\Product as mProduct;

use Validator;

class ProductController extends ControllerBase{
	public function list_productsAction(){
		$product = new mProduct;
        // 检索条件
        $cond = array();
        $cond['id']             = $this->post("id", "int");
        $cond['status']	        = mProduct::STATUS_NORMAL;
        $cond['name']           = array(
            $this->post("name", "string"),
            'LIKE'
        );
        $cond['desc']   = array(
            $this->post("desc", "string"),
            'LIKE'
        );
        $cond['remark']   = array(
            $this->post("remark", "string"),
            'LIKE'
        );

        // 用于遍历修改数据
        $data  = $this->page($product, $cond);

        foreach($data['data'] as $row){
            $product_id = $row->id;
            $row->create_time = date('Y-m-d H:i:s', $row->create_time);
            $row->update_time = date('Y-m-d H:i:s', $row->update_time);
            $row->oper = "<a href='#add_product' data-toggle='modal' data-id='$product_id' class='edit'>编辑商品</a> <a href='#delete_product' data-id='$product_id' class='delete'>删除</a>";
        }
        // 输出json
        return $this->output_table($data);
	}

	//RESTful
	public function indexAction(){
		return $this->output();
	}

	public function showAction(){

	}

	public function createAction( ){
		$name = $this->post('name', 'string');
		$desc = $this->post('desc', 'string');
		$remark = $this->post('remark', 'string');
		$price = $this->post('price', 'float');

		$validation = Validator::make( $_REQUEST, [
			'name' => 'required|string',
			'desc' => 'sometimes|string',
			'remark' => 'sometimes|string',
			'price' => 'required|numeric'
		],[
			'name.required' => '商品名称为必填项',
			'name.string' => '商品名称需要为字符串',
			'desc.string' => '商品描述需要为字符串',
			'remark.string' => '商品备注需要为字符串',
			'price.required' => '价格为必填项',
			'price.numeric' => '价格需为金额'
		]);

		if( $validation->fails() ){
			return error('WRONG_ARGUMENTS', $validation->errors()->all());
		}

		$product = sProduct::addNewProduct( $name, $price , $desc, $remark );

		return $this->output_json(['result' => 'ok']);
	}

	public function updateAction(){
		$id = $this->post('id', 'string');
		$name = $this->post('name', 'string');
		$desc = $this->post('desc', 'string');
		$remark = $this->post('remark', 'string');
		$price = $this->post('price', 'float');

		$validation = Validator::make( $_REQUEST, [
			'id' => 'required|exists:products,id',
			'name' => 'required|string',
			'desc' => 'sometimes|string',
			'remark' => 'sometimes|string',
			'price' => 'required|numeric'
		],[
			'id.required' => '请选择要编辑的商品',
			'id.exists' => '您要编辑的商品不存在',
			'name.required' => '商品名称为必填项',
			'name.string' => '商品名称需要为字符串',
			'desc.string' => '商品描述需要为字符串',
			'remark.string' => '商品备注需要为字符串',
			'price.required' => '价格为必填项',
			'price.numeric' => '价格需为金额'
		]);

		if( $validation->fails() ){
			return error('WRONG_ARGUMENTS', $validation->errors()->all());
		}

		$product = sProduct::updateProduct( $id, $name, $price , $desc, $remark );

		return $this->output_json(['result' => 'ok']);
	}

	public function deleteAction(){
		$id = $this->post('id', 'integer' );
		$product = sProduct::deleteProduct( $id );
		return $this->output_json( ['result' => 'ok'] );
	}
}
