/***
Wrapper/Helper Class for datagrid based on jQuery Datatable Plugin
***/
var Paginate = function() {
    var options = {
        src: null,
        url: null,
        template: null,

        count       : 50,
        start       : 1,
        display     : 10,
        border                  : false,
        text_color              : '#79B5E3',
        background_color        : 'none',
        text_hover_color        : '#2573AF',
        background_hover_color  : 'none',
        images      : false,
        mouse       : 'press'
    };

    return {
        init: function(opts) {
            for(var i in options) {
                if(opts[i]) options[i] = opts[i];
            }
            options.onChange = this.submitFilter;
            options.success  = opts.success;

            options.src.append('<div id="paginate-content"></div>');
            options.src.append('<div id="paginate-pagebar"></div>');

            this.submitFilter(1);
        },
        submitFilter: function(index){
            if(!index) index = options.start;
            var params= {};
            var forms = $(".form-filter");
            _.each(forms, function(row){
                if(row.name && row.value)
                    params[row.name] = row.value;
            });
            params['page'] = index;

            $.get(options.url, params, function(data){
                $("#paginate-content").empty();
                data = JSON.parse(data);

                options.count = data.recordsTotal/options.display;
                if(options.count > parseInt(options.count)) {
                    options.count = parseInt(options.count) + 1;
                }
                //todo: error reporting
                results = data.data;
                for(var i in results){
                    $("#paginate-content").append(options.template(results[i]));
                }
                if(results.length == 0){
                    $("#paginate-content").append('<div style="margin-top: 20px; text-align:center">空记录</div>');
                }
                options.success && options.success(results);

                options.start = index;
                if(options.count > 1) {
                    $("#paginate-pagebar").paginate(options);
                }
            });
        }
    };
};
