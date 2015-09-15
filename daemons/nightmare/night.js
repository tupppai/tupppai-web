var Nightmare = require('nightmare');
var c = require('./tucia/tasks');

new Nightmare()
//.use(c.task_commentPlayers())
.use(c.task_likePlayers())
.run();
