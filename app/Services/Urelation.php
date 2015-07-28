<?php

namespace Psgod\Services;

use \Psgod\Models\Urelation as mUrelation,

class Urelation extends ServiceBase
{

    public function setUserRelation($fellow, $fans, $status)
    {
        $rela = mUrelation::findFirst(array(
            "fellow = '$fellow' AND fans = '$fans'"
        ));
        if($rela)
            $rela->status = $status;
        else {
            $rela = new mUrelation();
            $rela->fans = $fans;
            $rela->fellow = $fellow;
        }
        return $rela->save_and_return($rela);
    }

}
