<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Main content -->
<section class="content">
 <!-- /.row -->
   <div class="row">
     <div class="col-xs-12">
       <div class="box">
         <!-- /.box-header -->
         <div class="box-body table-responsive no-padding">
           <table class="table table-hover">
             <tr>
               <th>服务器时间</th>
               <th><?php echo $get_sys_info['gmt_time'] ?></th>
             </tr>
             <tr>
               <th>服务器 ip 地址</th>
               <th><?php echo $get_sys_info['server_ip'] ?></th>
             </tr>
             <tr>
               <th>服务器解译引擎</th>
               <th><?php echo $get_sys_info['software'] ?></th>
             </tr>
             <tr>
               <th>web服务端口</th>
               <th><?php echo $get_sys_info['port'] ?></th>
             </tr>
             <tr>
               <th>Mysql 版本</th>
               <th><?php echo $get_sys_info['mysql_version'] ?></th>
             </tr>
             <tr>
               <th>服务器管理员</th>
               <th><?php echo $get_sys_info['admin'] ?></th>
             </tr>
             <tr>
               <th>服务端剩余空间</th>
               <th><?php echo $get_sys_info['diskfree'] ?></th>
             </tr>
             <tr>
               <th>系统当前用户名</th>
               <th><?php echo $get_sys_info['current_user'] ?></th>
             </tr>
             <tr>
               <th>系统时区</th>
               <th><?php echo $get_sys_info['timezone'] ?></th>
             </tr>
           </table>
         </div>
         <!-- /.box-body -->
       </div>
       <!-- /.box -->
     </div>
   </div>
</section>
<!-- /.content -->

