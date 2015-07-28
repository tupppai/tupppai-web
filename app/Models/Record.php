<?php

namespace Psgod\Models;

class Record extends ModelBase
{

	const TYPE_ASK = 1;
	const TYPE_REPLY = 2;
	const TYPE_COMMENT = 3;

	const ACTION_UP             = 1;
	const ACTION_LIKE           = 2;
	const ACTION_COLLECT        = 3;
	const ACTION_DOWN           = 4;
	const ACTION_SHARE          = 5;
    const ACTION_WEIXIN_SHARE   = 6;
    const ACTION_INFORM         = 7;
	const ACTION_COMMENT        = 8;


    public function getSource()
    {
        return 'records';
    }
}
