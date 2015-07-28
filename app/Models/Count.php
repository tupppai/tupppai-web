<?php

namespace Psgod\Models;

class Count extends ModelBase
{
	const TYPE_ASK = 1;
	const TYPE_REPLY = 2;
    const TYPE_COMMENT = 3;

    public function getSource()
    {
        return 'counts';
    }	

    /**
     * 更新时间
     */
    public function beforeSave() {
        $this->update_time  = time();

        return $this;
    }

    /**
     * 设置默认值
     */
    public function beforeCreate () {
        $this->create_time  = time();
        //$this->status       = self::STATUS_NORMAL;

        return $this;
    }
}
