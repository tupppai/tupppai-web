<?php

namespace App\Models;

class Urelation extends ModelBase
{

    public function getSource()
    {
        return 'follow';
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('fellow', 'App\Models\User', 'uid', array(
            'alias' => 'the_fellow',
        ));
        $this->belongsTo('fans', 'App\Models\User', 'uid', array(
            'alias' => 'the_fans',
        ));
    }
}
