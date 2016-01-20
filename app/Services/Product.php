<?php
	namespace App\Services;
	use App\Services\ActionLog as sActionLog;
	use App\Models\Product as mProduct;

	class Product extends ServiceBase{
		public static function addNewProduct($name, $price, $desc, $remark ){
			$product = new mProduct();
			$product = $product->set_name( $name )
					->set_price( $price )
					->set_desc( $desc )
					->set_remark( $remark )
					->set_status( mProduct::STATUS_NORMAL )
					->set_create_by( _uid() )
					->set_update_by( _uid() );

			sActionLog::init('ADD_PRODUCT');
			$p = $product->save();
			sActionLog::save( $p );

			return $p;
		}

		public static function updateProduct($id, $name, $price, $desc, $remark ){
			$product = (new mProduct())->get_product_by_id( $id );
			$product->set_name( $name )
					->set_price( $price )
					->set_desc( $desc )
					->set_remark( $remark )
					->set_update_by( _uid() );

			sActionLog::init('EDIT_PRODUCT');
			$p = $product->save();
			sActionLog::save( $p );

			return $p;
		}

		public static function deleteProduct( $id ){
			return self::updateStatus( $id, mProduct::STATUS_DELETED );
		}

		public static function updateStatus( $id, $status ){
			$product = (new mProduct())->get_product_by_id( $id );
			sActionLog::init( 'EDIT_PRODUCT', $product );
			$product = $product->set_status( $status )
								->set_update_by( _uid() );
			$product->save();
			sActionLog::save( $product );
			return $product;
		}

		public static function getProductById( $id ){
			$product = (new mProduct())->get_product_by_id( $id );
			return self::detail( $product );
		}

        public static function detail( $product ){
            if($product) 
                return $product->toArray();
            return array();
		}
	}
