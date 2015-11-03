# 消息类型 #

## 评论别人的评论 / 跳转到作品或者求助或者评论详情页面(这里可能提供一个接口只需要传3和comment_id)就可以获取详情

    type=1 & target_type = 3 & comment_id

    id: "12",
    sender: "246",
    content: "谢谢老板",
    target_type: "3",
    target_id: "25",
    pic_url: "http://7u2spr.com1.z0.glb.clouddn.com/20150704-2151095597e4cda4597mark.png",
    nickname: "",
    avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20150704-15450755978f035e65c.jpg",
    sex: 1,
    comment_id: "25",
    type: "1"

## 得到作品回复,跳转到作品页面
    
    type=2 & target_type = 2 & ask_id & reply_id

    id: "30",
    sender: "250",
    target_type: "2",
    target_id: "682",
    pic_url: "http://7u2spr.com1.z0.glb.clouddn.com/20150414-113155552c8a2b1677e.jpg",
    nickname: "心静",
    avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20150706-131311559a0e672c48d.png",
    sex: 1,
    reply_id: "682",
    ask_id: "287",
    type: "2"

## 关注消息,个人页面？

    type = 3 & target_type = 4 & target_id(uid)

    id: "134",
    sender: "577",
    target_type: "4",
    target_id: "577",
    pic_url: "",
    nickname: "peiwei",
    avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20151022-11220956285661825b9.jpg",
    sex: 1,
    type: "3"

## 点赞消息,个人页面or点赞页面？

    type = 5 & target_type = 1(求助)/2(作品) & target_id

    id: "346",
    sender: "577",
    target_type: "2",
    target_id: "7889",
    pic_url: "",
    nickname: "peiwei",
    avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20151022-11220956285661825b9.jpg",
    sex: 1,
    type: "5"
