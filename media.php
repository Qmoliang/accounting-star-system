<?php
  $page_title = 'All Image';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
?>

<?php
  if(isset($_POST['submit'])) {
  $photo = new Media();
  $photo->upload($_FILES['file_upload']);
    if($photo->process_media()){
        $session->msg('s','photo has been uploaded.');
        redirect('media.php');
    } else{
      $session->msg('d',join($photo->errors));
      redirect('media.php');
    }

  }
?>
<?php  
  
// 总记录数  
$sql = "SELECT id FROM media m";
$totalnums = totalnums($sql);  
  
// 每页显示条数  
$fnum = 20;  
  
// 翻页数  
$pagenum = ceil($totalnums / $fnum);  
  
// 页数常量  
$page = $_GET['page'];  
if ( $page == '' ) {
$page = 1;
}  
//防止恶意翻页  
if ($page > $pagenum)  
    echo "<script>window.location.href='media.php'</script>";  
  
//计算分页起始值  
if ($page == "") {  
    $num = 0;  
} else {  
    $num = ($page-1) * $fnum;  
}   
$media_files = find_part('media',$num,$fnum);
?> 

<?php include_once('layouts/header.php'); ?>
     <div class="row">
        <div class="col-md-6">
          <?php echo display_msg($msg); ?>
        </div>

      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <span class="glyphicon glyphicon-camera"></span>
            <span>All Photos</span>
            <div class="pull-right">
              <form class="form-inline" action="media.php" method="POST" enctype="multipart/form-data">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-btn">
                    <input type="file" name="file_upload" multiple="multiple" class="btn btn-primary btn-file"/>
                 </span>

                 <button type="submit" name="submit" class="btn btn-default">Upload</button>
               </div>
              </div>
             </form>
            </div>
          </div>
          <div class="panel-body">
            <table class="table">
              <thead>
                <tr>
                  <th class="text-center" style="width: 50px;">#</th>
                  <th class="text-center">Photo</th>
                  <th class="text-center">Photo Name</th>
                  <th class="text-center" style="width: 20%;">Photo Type</th>
                  <th class="text-center" style="width: 50px;">Actions</th>
                </tr>
              </thead>
                <tbody>
                <?php foreach ($media_files as $media_file): ?>
                <tr class="list-inline">
                 <td class="text-center"><?php echo count_id();?></td>
                  <td class="text-center">
                      <img src="uploads/products/<?php echo $media_file['file_name'];?>" class="img-thumbnail" />
                  </td>
                <td class="text-center">
                  <?php echo $media_file['file_name'];?>
                </td>
                <td class="text-center">
                  <?php echo $media_file['file_type'];?>
                </td>
                <td class="text-center">
                  <a href="delete_media.php?id=<?php echo (int) $media_file['id'];?>" class="btn btn-danger btn-xs"  title="Edit">
                    <span class="glyphicon glyphicon-trash"></span>
                  </a>
                </td>
               </tr>
              <?php endforeach;?>
            </tbody>
          </div>
        </div>
      </div>
</div>
<?php
  //上一页
   $pagestr="";
   $bothNum=4;
   if($page==1){
       $pagestr.='<span>首页  </span>';
   }else{
       $lastPage=$page-1;
       $pagestr.="<a href='media.php?page=1'>首页</a>"."  ";
   }
 
   if($page-$bothNum>1){
       $pagestr.="<a href='media.php?page=$lastPage'>上一页</a>";
       $pagestr.="<span>...</span>";
   }
   //当前页的左边
   for($i=$bothNum;$i>=1;$i--){
       if(($page - $i) < 1 ) { // 当前页左边花最多 bothnum 个数字
            continue;
        }
       $lastPage=$page-$i;
       $pagestr.="<a href='media.php?page=$lastPage'>$lastPage</a>"."  ";
   }
   //当前页
   $pagestr.="<span>$page</span>"."  ";
   //当前页右边
   for($i=1;$i<=$bothNum;$i++){
       if(($page + $i) > $pagenum) { // 当前页右边最多 bothnum 个数字
           break;
       }
       $lastPage=$page+$i;
       $pagestr.="<a href='media.php?page=$lastPage'>$lastPage</a>"."  ";
   
   }
   if(($page+$bothNum)<$pagenum){
       $pagestr.="<span>...</span>"."  ";
     }
  //下一页
    if($page == $pagenum) {
         $pagestr .= '<span>  下一页  </span>';
      } else {
             $nextPage=$page+1;
          $pagestr .= "<a href='media.php?page={$nextPage}'>  下一页  </a>";
      }
   //尾页
   if(($page+$bothNum)<$pagenum){
       $pagestr .= '<a href="media.php?page='.$pagenum.'">尾页</a>'."  ";
   }
   
   echo $pagestr;
   echo "<hr/>";
   echo '当前页数为：'.$page.'，总页数为：'.$pagenum;   
   ?>

<?php include_once('layouts/footer.php'); ?>
