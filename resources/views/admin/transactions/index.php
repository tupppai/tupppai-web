<ul class="breadcrumb">
  <li>
    <a href="#">交易模块</a>
  </li>
  <li>批量充值</li>
</ul>


<div class="form-inline">
    <div class="form-group">
        <input name="uid" class="form-filter form-control" placeholder="账号ID">
    </div>
    <div class="form-group">
        <input name="phone" class="form-filter form-control" placeholder="手机号">
    </div>
    <div class="form-group">
        <input name="nickname" class="form-filter form-control" placeholder="展示名称">
    </div>
    <div class="form-group">
        <input name="start_time" class="form-filter form-control" placeholder="时间开始区间">
    </div>
    <div class="form-group">
        <input name="end_time" class="form-filter form-control" placeholder="时间结束区间">
    </div>
    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>
<table class="table table-bordered table-hover" id="list_users_ajax"></table>

<?php modal('/role/assign_role'); ?>
