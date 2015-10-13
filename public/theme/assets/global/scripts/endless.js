/***
Wrapper/Helper Class for datagrid based on jQuery Datatable Plugin
***/
var Endless = function() {

    var options = {
        src: null,
        url: '',
        data: {
            page: 1,
            legnth: 15
        },
        template: function() {},
        bottomPixels: 450,
        fireDelay: 10,
        success: function(data) {
            
        },
        callback: function() {
            var self = options;
            var form = self.serialize();
            for(var i in form){
                self.data[i] = form[i];
            }
            console.log(self.data);
            $.get(self.url, self.data, function(data){
                data = JSON.parse(data);
                //todo: error reporting
                data = data.data;
                for(var i in data){
                    self.src.append(self.template(data[i]));
                }
                self.success && self.success(data);
                self.data.page ++;
            });
        },
        serialize: function() {
            var data  = {};
            var forms = $(".form-filter");
            _.each(forms, function(row){
                if(row.name && row.value)
                    data[row.name] = row.value;
            });

            return data;
        }
    }

    return {
        //main function to initiate the module
        init: function(opts) {
            for(var i in options) {
                if(opts[i]) options[i] = opts[i];
            }

            $(document).endlessScroll(options)
            options.callback();
        },
        submitFilter: function(){
            options.data.page = 1;
            options.src.empty();
            options.callback();
        }
    };
};
