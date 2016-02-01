var tutorial_id = null;
function getQueryVariable(variable, def) {
   	var query = window.location.search.substring(1);
   	var vars = query.split("&");
   	for (var i=0;i<vars.length;i++) {
           var pair = vars[i].split("=");
           if(pair[0] == variable){return pair[1];}
   	}

   	return (def==undefined)?def:(false);
};
$(".course-p").click(function() {
	window.location.href = 'sharecourse.html' + "?tutorial_id=" + tutorial_id;
});	
$(".task-p").click(function() {
	window.location.href = 'task.html' + "?tutorial_id=" + tutorial_id;
});	