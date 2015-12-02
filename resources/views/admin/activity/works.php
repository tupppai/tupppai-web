<ul class="breadcrumb">
  <li>
    <a href="#">内容管理</a>
  </li>
  <li>活动管理</li>
</ul>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li>
        <a href="/activity/index">
        活动列表</a>
      </li>
      <li class="active">
        <a href="#">
        参与作品列表</a>
      </li>
    </ul>
</div>

<div id="work-data"></div>

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
var activity_id;

jQuery(document).ready(function() {
    activity_id = getQueryVariable('category_id', 4);

    table = new Paginate();
    table.init({
        src: $('#work-data'),
        url: '/activity/list_works?activity_id='+activity_id,
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
            'activity_id' : form.find('[name="activity_id"]').val(),
        };
        $.post('/verify/list_threads', postData, function( data ){
            table.submitFilter();
        });
    });
});
</script>
