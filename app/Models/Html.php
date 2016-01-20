<?php namespace App\Models;

class Html extends ModelBase {

    protected $table = 'htmls';

    public function get_html_by_id($id) {
        return self::find($id);
    }
}
