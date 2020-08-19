<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 方法1: global 的使用; 至于这里为什么这样写, 可参考链接: http://blog.csdn.net/whatday/article/details/51364563
// 方法2: 这里同样可以将参数 $data 和 set_value('father_menu') 传入参数到方法 check_have_menu_children() 中
// 功能列表的类型
global $set_func_type_option;
$set_func_type_option = $func_type_option;

// // 检查该菜单或者[按钮/链接/数据模块/数据] 是否存在子模块
function check_have_model_children($data_array, $sub, $parent_class = '', $mud_k, $tm, $group_role)
{
    global $set_func_type_option;

    // [按钮/链接/数据模块/数据]
    if ( isset($data_array['children']))
    {
        foreach ($data_array['children'] as $c_tma)
        {
?>      
            <?php echo $sub; ?>
            <label>
                <input class="all_role module_id_<?php echo $mud_k["module_id"]; ?> menu_id_<?php echo $tm['menu_id']; ?> index_cla_<?php echo $tm['menu_id']; ?> data_cla <?php echo $parent_class; ?>" type="checkbox" name="roles[]" value="<?php echo $c_tma['menu_id']; ?>" <?php echo in_array($c_tma['menu_id'], $group_role)? 'checked' : ''; ?> ><?php echo $c_tma['menu_name'] ?> <span class='label label-label label-warning'>[<?php echo isset($set_func_type_option[$c_tma['is_show']])? $set_func_type_option[$c_tma['is_show']] : '';  ?>]</span>
            </label>&nbsp;&nbsp;&nbsp;<br>

            <?php 
                check_have_model_children($c_tma, $sub . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $parent_class . " btn_url_data_{$c_tma['menu_id']}", $mud_k, $tm, $group_role);
            ?>
<?php 
        }
    }
} 
?>

<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">

        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">修改角色</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            <form role="form" action="<?php echo base_url('user_group/modify') ?>" method='POST'>
                <div class="box-body">
                    <!-- 自定义表单验证失败的错误提示 -->
                    <?php echo $form_error_contents; ?>
                    <div class="form-group">
                        <label for="group_name">角色名称</label>
                        <input type="text" <?php echo ($user_group['group_id'] == 1) ? '' : 'name="group_name"'; ?>  class="form-control" id='group_name' value="<?php echo set_value('group_name') ?: $user_group['group_name']; ?>" required <?php echo ($user_group['group_id'] == 1) ? 'disabled' : ''; ?>>

                        <?php echo ($user_group['group_id'] == 1) ? '<input type="hidden" name="group_name" value="'.$user_group['group_name'].'">' : ''; ?>
                        <input type="hidden" name="group_id" value="<?php echo $user_group['group_id']; ?>">

                    </div>
                    <table class="table table-bordered mb20">
                        <tr>
                            <th style="word-break: keep-all;">默认首页</th>
                            <th>
                                <?php 
                                    foreach ($menu_unlimit_data as $mud_i_k)
                                    {
                                        foreach ($mud_i_k['menus'] as $mud_i_k) 
                                        {
                                            if ($mud_i_k['is_show'] != 1) 
                                            {
                                                continue;
                                            }
                                 ?>
                                            <input type="radio" name="def_index_id" class="index_cla" data-id='<?php echo $mud_i_k['menu_id']; ?>' value='<?php echo $mud_i_k['menu_id']; ?>' <?php echo ($mud_i_k['menu_id'] == $user_group['def_index_id'])?'checked':''; ?> required ><?php echo $mud_i_k['menu_name']; ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                <?php 
                                        }
                                    }
                                ?>
                            </th>
                        </tr>
                    </table>

                    <table class="table table-bordered">
                        <tr>
                            <th colspan=2>
                                选择所有权限
                                <input type="checkbox" name="" id='all_role'>
                            </th>
                        </tr>

                        <?PHP
                        $group_role = explode(',',$user_group['group_role']);
                        
                        // 菜单模块
                        foreach ($menu_unlimit_data as $mud_v => $mud_k)  
                        {
                        ?>
                        
                        <tr>
                            <th style="word-break: keep-all; width:150px;">
                                <?php echo $mud_k['module_name']; ?> <span class='label label-success'>[菜单模块]</span>
                                <br><br>
                                <input type="checkbox" class="all_role mo_cla" id='<?php echo $mud_k['module_id'] ?>'>
                                <span style="font-size: xx-small;font-weight: normal;">全选</span>
                            </th>
                            <td>
                        
                            <?php 
                            // 菜单
                            foreach ($mud_k['menus'] as $tm) 
                            {
                                if ($tm['is_show'] != 1)
                                {
                                    continue;
                                }
                            ?>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>
                                            <label>
                                              <input class="all_role module_id_<?php echo $mud_k["module_id"] ?> men_cla index_cla_<?php echo $tm['menu_id'] ?>" id='<?php echo $tm['menu_id'] ?>' type="checkbox" name="roles[]" value="<?php echo $tm['menu_id'] ?>" <?php echo in_array($tm['menu_id'], $group_role)? 'checked' : ''; ?> > <?php echo $tm['menu_name']; ?> <span class='label label-info'>[菜单]</span>
                                            </label>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php 
                                                check_have_model_children($tm, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '', $mud_k, $tm, $group_role);
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            <?php } ?>
                        
                            </td>
                        </tr>
                        <?php } ?>

                    </table>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary form_button"><i class='fa fa-spinner fa-spin hide'></i>提交</button>
                    <a class="btn btn-default" href="<?php echo base_url("$curr_controller/index") ?>">取消</a>
                </div>

            </form>
        </div>
        <!-- /.box -->

    </div>
    <!-- /.row (main row) -->

</section>
<!-- /.content -->

<script type="text/javascript">
    
    // 全选所有权限
    $('#all_role').click(function(){
        var $all_role_class = '.all_role';
        if ( $(this).is(':checked')){
            $($all_role_class).prop('checked', true);
        }else{
            $($all_role_class).prop('checked', false);
        }
    }); 

    // 菜单模块全选
    $('.mo_cla').click(function(){
        var $module_id = $(this).prop('id');
        var $menu_class = '.module_id_'+$module_id;
        if ( $(this).is(':checked')){
            $($menu_class).prop('checked', true);
        }else{
            $($menu_class).prop('checked', false);
        }
    });
    
    // 菜单包含[按钮/链接/数据模块/数据]全选
    $('.men_cla').click(function(){
        var $menu_id = $(this).prop('id');
        var $func_class = '.menu_id_'+$menu_id;

        if ( $(this).is(':checked')){
            $($func_class).prop('checked', true);
        }else{
            $($func_class).prop('checked', false);
        }
    });
    
    // [按钮/链接/数据模块/数据]包含下级全选
    $('.data_cla').click(function(){
        var $menu_id = $(this).val();
        var $func_class = '.btn_url_data_'+$menu_id;

        if ( $(this).is(':checked')){
            $($func_class).prop('checked', true);
        }else{
            $($func_class).prop('checked', false);
        }
    });

    // 默认首页选择后包含该菜单的权限和该菜单的所有功能
    /*$('.index_cla').click(function(){
        var $menu_id = $(this).data('id');
        var $index_class = '.index_cla_'+$menu_id;

        if ( $(this).is(':checked')){
            $($index_class).prop('checked', true);
        }else{
            $($index_class).prop('checked', false);
        }
    });*/
</script>
