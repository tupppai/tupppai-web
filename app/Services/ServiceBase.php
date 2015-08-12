<?php
namespace App\Services;

class ServiceBase {
    //预留0x100个系统用
    const ERROR_URL_FORMAT  = 0x103;

    //EMPTY LOGIC FROM 0x100
    const EMPTY_APP_NAME    = 0x100;
    const EMPTY_LOGO        = 0x101;
    const EMPTY_JUMP_URL    = 0x102;
    const EMPTY_UID         = 0x106;
    const EMPTY_ROLE_ID     = 0x107;
    const EMPTY_MESSAGE_ID  = 0x108;
    const EMPTY_TITLE       = 0x109;
    const EMPTY_ID          = 0x110;
    const EMPTY_POST_TIME   = 0x111;
    const EMPTY_CONTENT     = 0x116;

    const EMPTY_DEVICE_NAME = 0x114;
    const EMPTY_DEVICE_MAC  = 0x114;
    const EMPTY_DEVICE_OS   = 0x114;
    const EMPTY_DEVICE_TOKEN= 0x114;

    const EMPTY_PERMISSION_ID = 0x114;

    //NOT EXIST FROM 0x400
    const KEY_NOT_EXIST     = 0x112;
    const REPLY_NOT_EXIST   = 0x112;
    const USER_NOT_EXIST    = 0x113;
    const INFORM_CONTENT_ERR= 0x113;
    const ASK_NOT_EXIST     = 0x104;
    const UPLOAD_NOT_EXIST  = 0x104;
    const DOWNLOAD_NOT_EXIST= 0x105;
    const DEVICE_TOKEN_NOT_EXIST = 0x106;
    const COMMENT_ERR       = 0x115;
    const COMMENT_NOT_EXIST = 0x115;
    const FEEDBACK_NOT_EXIST = 0x117;
    const ACTION_NOT_EXIST  = 0x118;
    const BIND_NOT_EXIST    = 0x118;
    const USER_DEVICE_NOT_EXIST = 0x120;
    const DEVICE_NOT_EXIST  = 0x121;
    //ERR FROM 0x800
    const FILE_NOT_VALID    = 0x117;
    const PASSWORD_NOT_MATCH= 0x118;
    const WRONG_ARGUMENTS   = 0x118;
    const PERMISSION_DENY   = 0x119;
    const INVALID_PHONE_NUMBER = 0x120;
    const PHONE_ALREADY_EXIST = 0x121;
    const SYSTEM_ERROR      = 0x000;
    const USER_EXISTS = 0x001;

    public static function getErrCode($name) {
        return constant('self::'.$name);
    }

    public static function getErrInfo($name) {
        return str_replace("_", " ", strtolower($name));
    }
}
