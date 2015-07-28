<?php

namespace Psgod\Models;

class Urelation extends ModelBase
{

    public function getSource()
    {
        return 'follow';
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('fellow', 'Psgod\Models\User', 'uid', array(
            'alias' => 'the_fellow',
        ));
        $this->belongsTo('fans', 'Psgod\Models\User', 'uid', array(
            'alias' => 'the_fans',
        ));
    }
}
