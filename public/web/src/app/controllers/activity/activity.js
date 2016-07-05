define([
        'app/views/activity/header/header',
		'app/views/activity/activityList',
		],
	function (header,activityList) {
    "use strict";
    return function(id) {
        var sections = ['_header','_activityList'];
        var layoutView = window.app.render(sections);

        var activitesApi = new window.app.model();
        activitesApi.url = "/activities/5"
        var headerView = new header({
            model: activitesApi
        });
        window.app.show(layoutView._header, headerView);

        //评论列表
        var activity = new window.app.collection();
        activity.url = "/activities?activity_id="+ id;
        activity.url = "/activities?activity_id=1017";
        var activityListView = new activityList({
            collection: activity
        });
        window.app.show(layoutView._activityList, activityListView);

    };
});

