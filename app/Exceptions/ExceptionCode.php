<?php namespace App\Exceptions;

class ExceptionCode {
    //预留0x100个系统用
    const LOGIN_EXPIRED                 = 0x001;
    const ERROR_URL_FORMAT              = 0x002;

    //EXISTS(DUPLICATE)
    const NICKNAME_EXISTS               = 0x003;
    const PHONE_ALREADY_EXIST           = 0x004;
    const USER_EXISTS                   = 0x005;
    const PERMISSION_EXIST              = 0x006;
    const ALREADY_SEND_SMS              = 0x007;
    const ALREADY_BOUND_PLATFORM        = 0x008;

    //EMPTY LOGIC FROM 0x100
    const EMPTY_APP_NAME                = 0x100;
    const EMPTY_LOGO                    = 0x101;
    const EMPTY_JUMP_URL                = 0x102;
    const EMPTY_UID                     = 0x103;
    const EMPTY_ROLE_ID                 = 0x104;
    const EMPTY_MESSAGE_ID              = 0x105;
    const EMPTY_TITLE                   = 0x106;
    const EMPTY_ID                      = 0x107;
    const EMPTY_POST_TIME               = 0x108;
    const EMPTY_CONTENT                 = 0x109;
    const EMPTY_DEVICE_NAME             = 0x110;
    const EMPTY_DEVICE_MAC              = 0x111;
    const EMPTY_DEVICE_OS               = 0x112;
    const EMPTY_DEVICE_TOKEN            = 0x113;
    const EMPTY_PERMISSION_ID           = 0x114;
    const EMPTY_UPLOAD_ID               = 0x115;
    const EMPTY_USERNAME                = 0x116;
    const EMPTY_PASSWORD                = 0x117;
    const EMPTY_NICKNAME                = 0x118;
    const EMPTY_MOBILE                  = 0x119;
    const EMPTY_AVATAR                  = 0x120;
    const EMPTY_OPENID                  = 0x121;
    const EMPTY_VERIFICATION_CODE       = 0x122;
    const EMPTY_TYPE                    = 0x123;
    const EMPTY_TARGET                  = 0x124;
    const EMPTY_CATEGORY_NAME           = 0x125;
    const EMPTY_CATEGORY_ID             = 0x126;
    const EMPTY_STATUS                  = 0x127;
    const EMPTY_SEX                     = 0x128;
    const EMPTY_COMMENT                 = 0x129;
    const OLD_PASSWORD_EMPTY            = 0x130;
    const NEW_PASSWORD_EMPTY            = 0x131;
    const EMPTY_DISPLAY_NAME            = 0x132;
    const EMPTY_CONTROLLER_NAME         = 0x133;
    const EMPTY_ACTION_NAME             = 0x134;
    const EMPTY_REASON                  = 0x135;
    const EMPTY_SCHEDULE_ID             = 0x136;
    const EMPTY_INFORM_ID               = 0x137;
    const EMPTY_FEEDBACK_ID             = 0x138;
    const EMPTY_OPINION                 = 0x139;
    const EMPTY_QUERY_STRING            = 0x140;
    const EMPTY_MSG_TYPE                = 0x141;
    const EMPTY_TARGET_ID               = 0x142;
    const EMPTY_SYSMSG_ID               = 0x143;
    const EMPTY_TAG_ID                  = 0x144;
    const EMPTY_TAG_NAME                = 0x145;
    const EMPTY_ACTIVITY_NAME           = 0x146;
    const EMPTY_JUMP_STRING_OR_URL      = 0x147;
    const EMPTY_ARGUMENTS               = 0x148;

    //INVALID
    const INVALID_PHONE_NUMBER          = 0x201;
    const INVALID_VERIFICATION_CODE     = 0x202;
    const INVALID_TOKEN                 = 0x203;
    const PASSWORD_NOT_MATCH            = 0x204;
    const FILE_NOT_VALID                = 0x205;
    const INVALID_START_TIME            = 0x206;
    const INVALID_END_TIME              = 0x207;
    const INVALID_SEND_TIME             = 0x208;

    //NOT EXIST FROM
    const KEY_NOT_EXIST                 = 0x301;
    const REPLY_NOT_EXIST               = 0x302;
    const USER_NOT_EXIST                = 0x303;
    const ASK_NOT_EXIST                 = 0x305;
    const APP_NOT_EXIST                 = 0x306;
    const BANNER_NOT_EXIST              = 0x307;
    const UPLOAD_NOT_EXIST              = 0x308;
    const DOWNLOAD_NOT_EXIST            = 0x309;
    const DEVICE_TOKEN_NOT_EXIST        = 0x310;
    const COMMENT_ERR                   = 0x311;
    const COMMENT_NOT_EXIST             = 0x312;
    const FEEDBACK_NOT_EXIST            = 0x313;
    const ACTION_NOT_EXIST              = 0x314;
    const BIND_NOT_EXIST                = 0x315;
    const USER_DEVICE_NOT_EXIST         = 0x316;
    const DEVICE_NOT_EXIST              = 0x317;
    const ROLE_NOT_EXIST                = 0x318;
    const FILE_NOT_EXIST                = 0x319;
    const COUNT_NOT_EXIST               = 0x320;
    const DOWNLOAD_FILE_DOESNT_EXISTS   = 0x320;
    const DOWNLOAD_RECORD_DOESNT_EXIST  = 0x321;
    const CATEGORY_NOT_EXIST            = 0x322;
    const PERMISSION_DOESNT_EXIST       = 0x323;
    const SCHEDULE_DOESNT_EXIST         = 0x324;
    const INFORM_NOT_EXIST              = 0x325;
    const TYPE_NOT_EXIST                = 0x326;
    const TAG_NOT_EXIST                 = 0x327;
    const FOCUS_NOT_EXIST               = 0x328;
    const WRONG_AUTHORIZATION_EXIST     = 0x329;
    const OPEN_ID_NOT_EXIST             = 0x330;
    const AMOUNT_NOT_EXIST              = 0x330;
    const TRADE_NOT_EXIST               = 0x330;
    //ERR FROM
    const AMOUNT_ERROR                  = 0x401;
    const INFORM_CONTENT_ERR            = 0x402;
    const WORKTIME_ERROR                = 0x403;
    const WRONG_ARGUMENTS               = 0x404;
    const PERMISSION_DENY               = 0x405;
    const SYSTEM_ERROR                  = 0x406;
    const UPDATE_DEVICE                 = 0x407;
    const DOWOLOAD_FILE                 = 0x408;
    const WRONG_MESSAGE_TYPE            = 0x409;
    const RECEIVER_SAME_AS_SENDER       = 0x410;
    const WRONG_OWNER                   = 0x411;
    const ERROR_SIGN_FAIL               = 0x412;
    const API_NOT_AVAIABLE_NOW          = 0x413;

    const NOT_YOUR_RECORD               = 0x501;
    const NOTHING_TO_BE_PAID            = 0x501;
    const ADD_USER_FAILD                = 0x501;
    const RELEASING_BEFORE_ASK          = 0x501;
    const SCHEDULE_PENDING              = 0x501;
    const SCHEDULE_PASSED               = 0x501;
    const SCHEDULE_DELETED              = 0x501;
    const REWARD_EXIST                  = 0x500;
    const TRADE_USER_BALANCE_ERROR      = 0x501;
    const TRADE_PAY_ERROR               = 0x502;
    const TRADE_CALLBACK_FAILED         = 0x503;
    public static function getErrCode($name) {
        return constant('self::'.$name);
    }

    public static function getErrInfo($name) {
        return str_replace("_", " ", strtolower($name));
    }
}
