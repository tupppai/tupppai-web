<?php
namespace App\Models\Parttime;

class Designer extends ModelBase
{
    /**
     * 通过id获取设计师
     */
    public function get_designer_by_id($designer_id) {
        $designer = self::find($designer_id);

        return $designer;
    }

    /**
     * 通过id集合获取设计师
     */
    public function get_designer_by_ids($designer_ids) {
        #$designers = self::whereRaw(" FIND_IN_SET (id ,$designer_ids)")
        #->get();
        $designers = self::whereIn('id', $designer_ids)
            ->get();

        return $designers;
    }
}
