<ul class="breadcrumb">
  <li>
    <a href="#">数据统计</a>
  </li>
  <li>统计</li>
</ul>
<script type="text/javascript" src="/theme/vendors/HighCharts/js/highcharts.js"></script>
<!-- <script type="text/javascript" src="/theme/assets/global/scripts/stat.js"></script> -->
<link rel="stylesheet" type="text/css" href="/theme/assets/global/css/stat.css">

<div class="container">

	<div class="hcharts">

	</div>

</div>

<script type="text/javascript">
	var today = new Date();
	var chnDate = ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'];
	var chart;


Date.prototype.toYmd = function( ){
    var year = this.getFullYear();

    var month = this.getMonth()+1;
    if(month<10){
        month = '0'+month;
    }

    var date = this.getDate();
    if(date<10){
        date = '0'+date;
    }

    var date = [year,month,date]

    return date.join('-');
}

function getCategories( startFrom, durationDays ){
    var today = new Date();
    today.setDate(today.getDate()+1+startFrom);
    today.setHours(0);
    today.setMinutes(0);
    var startDateSec = today.setSeconds(0);
    var stopDateSec = startDateSec + durationDays*(1000*60*60*24);

    var dates = [];
    for( var i = startDateSec; i < stopDateSec; i+=(1000*60*60*24) ){
        var crntDay = new Date( i );
        //console.log(crntDay);
        var crntDayStr = crntDay.toYmd() + '<br />' + chnDate[crntDay.getDay()];
        dates.push( crntDayStr );
    }
    return dates;
}

var HCOpts = {
    'areaspline':{
        chart: {
            type: 'areaspline'
        },
        legend: {  //图列
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 150, //图例距离位置
            y: 100,
            floating: true,  //图例放图内？
            borderWidth: 1, //图例边框
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        xAxis: {
            categories: getCategories( -7, 7 ),
        },
        plotOptions: {
            areaspline: {
                fillOpacity: 0.5
            }
        },
        tooltip: {
            shared: true,
            valueSuffix: ' units',
            crosshairs: true
        },
        credits: {
            enabled: false
        }
    },
    'pie':{
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        }
    }
};


var statOpts = {
	'user' :{
		'gender':{
			'title': {
				text: '注册用户性别比例'
			},
	        'tooltip': {
	            pointFormat: '<b>共 {point.y} 人</b>'
	        }
		}
	},
	'ask':{
		'post':{
			'title':{
				'text':'求助'
			},
			'tooltip':{
				pointFormat: '<b>共 {point.y} 帖</b>'
			}
		}
	}
    // 'os':{
    //     title: {
    //         text: '操作系统比例'
    //     },
    //     tooltip: {
    //         pointFormat: '<b>共 {point.y} 台</b>'
    //     },
    // },
    // 'users':{
    //     title:{
    //         text: '注册用户男女比例'
    //     },
    //     tooltip: {
    //         pointFormat: '<b>共 {point.y} 人</b>'
    //     }
    // },
    // 'threads':{
    //     title:{
    //         text: '帖子和求助比例'
    //     },
    //     tooltip:{
    //         pointFormat: '<b>共 {point.y} 条</b>'
    //     },
    // },
    // 'asks':{
    //     title: {
    //         text: '求助趋势分析图'
    //     },
    //     yAxis: {
    //         title: {
    //             text: '数量'
    //         },
    //         labels: {
    //             align: 'left',
    //             x: 10,
    //             y: 20
    //         }
    //     },
    //     // series: [{
    //     //     name: '求助数',
    //     //     data: point.data.asks
    //     // }, {
    //     //     name: '作品数',
    //     //     data: point.data.replies
    //     // }]
    // }
}

var table = null;
var chart = null;
$(function () {

    var type = getQueryVariable('type');
    var category = getQueryVariable('category');
    var defOpts = {};
    var request_url = '';

    switch( type ){
        case 'user':
            request_url = '/stat/stat';
            defOpts = HCOpts.pie;
            break;
        case 'os':
        case 'thread':
            request_url = '/stat/sum_stats';
            defOpts = HCOpts.pie;
            break;
        case 'replie':
        case 'ask':
            request_url = '/stat/sum_analyze';
        default:
            defOpts = HCOpts.areaspline;
            break;
    }

    Highcharts.setOptions( defOpts );
    var getData = {
    	'target': type,
    	'category': category,
    	'startFrom': 0
   	};
    $.get( request_url, getData, function(result) {
    	var data = result.data;
        var opt = statOpts[type][category];
        console.log( data.points );
        opt['series'] = [{
            type: 'pie',
            name: '----',
            data: data.points,
        }];

        opt['title']['text'] += '(' + data.startFrom + ' ~ ' + data.endAt + ')';
        chart = $('.hcharts').highcharts( opt );
    });

});
</script>
