<ul class="breadcrumb">
  <li>
    <a href="#">内容审核</a>
  </li>
  <li>列入频道</li>
</ul>

<div class="tabbable-line">
    <label for="selectAll">
        <input type="checkbox" name="selectAll" id="selectAll" />全选
    </label>
</div>

<select name="category_id" style="width: 20%">
    <option value="">所有活动</option>
    <?php foreach( $categories as $category ): ?>
        <option value="<?php echo $category['id']; ?>"><?php echo $category['display_name']; ?></option>
    <?php endforeach; ?>
</select>
<button class="btn btn-info online" style="width: 20%">加入</button>


<div class="pagination" id="thread-data"></div>

<?php modal('/verify/category_item'); ?>

<style>
    .photo-container-admin{
        margin-top: 10px;
        margin-bottom: 10px;
        border: 1px solid transparent;
    }
    .photo-container-admin:hover{
        border: 1px solid steelblue;
    }
    .set-value label{
        padding: 0;
    }
    .photo-main{
        border-top:1px solid lightgray;
        border-bottom:1px solid lightgray;
    }

    .thread_category.normal{ color: dodgerblue; }
    .thread_category.verifing{ color: darkkhaki; }
    .thread_category.verified{ color: lightgreen; }
    .thread_category.deleted{ color: magenta;  text-decoration: line-through; }
</style>
<script>
var table = null;
var category_id;
var status;
var pc_host = '<?php echo $pc_host; ?>';

jQuery(document).ready(function() {
    status = getQueryVariable('status', 'valid');

    table = new Paginate();
    table.init({
        src: $('#thread-data'),
        url: '/verify/list_temp_threads',
        template: _.template($('#thread-item-template').html()),
        success: function() {
        },
        display:15
    });

    $('#selectAll').on('click', function(){
        var all = $(this).prop('checked');
        var checkboxes = $('.photo-container-admin input[name="confirm_online"]');
        if( all ){
            checkboxes.prop('checked', 'checked');
        }
        else{
            checkboxes.removeProp('checked');
        }
    });

    $('.online').on('click', function(){
        var target_types = [];
        var target_ids = [];
        var statuses = [];
        var thread_status;
        $('input[name="confirm_online"]:checked').each(function( i, n ){
            var p = $(this).parents('.photo-container-admin');
            target_types.push( p.attr('data-target-type') );
            target_ids.push( p.attr('data-target-id') );
        });


        var postData = {
            'target_id[]': target_ids,
            'target_type[]': target_types,
            'category_id': $('select[name="category_id"]').val(),
            'status': 'checked'
        };

        $.post('/verify/set_category', postData, function( data ){
            if( data.data.result == 'ok' ){
              toastr['success']('设置分类成功');
              //移除所有data-category-id
            }
        });
    });
});
</script>
