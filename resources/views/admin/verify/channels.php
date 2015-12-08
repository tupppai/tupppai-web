<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>内容分类</li>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="uid" class="form-filter form-control" placeholder="账号ID">
    </div>
    <div class="form-group">
        <input name="nickname" class="form-filter form-control" placeholder="昵称">
    </div>

    <div class="form-group">
        <select name="category_ids[]" class="form-filter form-control">
            <option value="">所有帖子</option>
            <?php
                $catId = isset( $_REQUEST['category_ids'] ) ? $_REQUEST['category_ids'] : NULL;
                foreach( $categories as $category ):
            ?>
                <option value="<?php echo $category['id']; if( $catId == $category['id']): echo '" selected="selected'; endif;?>"><?php echo $category['display_name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<!-- <div class="tabbable-line">
    <label for="selectAll">
        <input type="checkbox" name="selectAll" id="selectAll" checked="checked"/>全选
    </label>
</div> -->


<div id="thread-data"></div>

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
</style>
<script>
var table = null;
var category_ids;

jQuery(document).ready(function() {
    category_ids = getQueryVariable('category_ids', '');

    table = new Paginate();
    table.init({
        src: $('#thread-data'),
        url: '/verify/list_threads?category_ids='+category_ids,
        template: _.template($('#thread-item-template').html()),
        success: function() {
        },
        display:15
    });


    $('#search').on('click', function(){
        var form = $('#search_form').find('.form-inline');
        var postData = {
            'uid'         : form.find('[name="uid"]').val()        ,
            'nickname'    : form.find('[name="nickname"]').val()   ,
            'category_ids' : form.find('[name="category_id"]').val(),
        };
        $.post('/verify/list_threads', postData, function( data ){
            table.submitFilter();
        });
    });
});
</script>
