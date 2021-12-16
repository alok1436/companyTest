<?php 
/**
 * Method to generateCode.
 *
 * @param  $codePrefix, $objectId, $provenceId
 * @return generatedCode
 */
function resizeImage($filename, $newwidth, $newheight){
    list($width, $height) = getimagesize($filename);
    if($width > $height && $newheight < $height){
        $newheight = $height / ($width / $newwidth);
    } else if ($width < $height && $newwidth < $width) {
        $newwidth = $width / ($height / $newheight);   
    } else {
        $newwidth = $width;
        $newheight = $height;
    }
    $thumb = imagecreatetruecolor($newwidth, $newheight);
    $source = imagecreatefromjpeg($filename);
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    return imagejpeg($thumb);
}


function createThumbnail($image_name,$new_width,$new_height,$uploadDir,$moveToDir, $newImgName='')
{
    $path = $uploadDir . '/' . $image_name;

    $mime = getimagesize($path);

    if($mime['mime']=='image/png') { 
        $src_img = imagecreatefrompng($path);
    }
    if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
        $src_img = imagecreatefromjpeg($path);
    }   

    $old_x          =   imageSX($src_img);
    $old_y          =   imageSY($src_img);

    if($old_x > $old_y) 
    {
        $thumb_w    =   $new_width;
        $thumb_h    =   $old_y*($new_height/$old_x);
    }

    if($old_x < $old_y) 
    {
        $thumb_w    =   $old_x*($new_width/$old_y);
        $thumb_h    =   $new_height;
    }

    if($old_x == $old_y) 
    {
        $thumb_w    =   $new_width;
        $thumb_h    =   $new_height;
    }

    $dst_img        =   ImageCreateTrueColor($thumb_w,$thumb_h);

    imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 


    // New save location
    $new_thumb_loc = $moveToDir . ($newImgName == '' ? $image_name :  $newImgName);

    if($mime['mime']=='image/png') {
        $result = imagepng($dst_img,$new_thumb_loc,8);
    }
    if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
        $result = imagejpeg($dst_img,$new_thumb_loc,80);
    }

    imagedestroy($dst_img); 
    imagedestroy($src_img);

    return $result;
}

function kl_price($amount){
  return 'Rs '.number_format($amount,2);
}

function slugify($text)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}

function resize_image($file, $w, $h, $crop=false) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    
    //Get file extension
    $exploding = explode(".",$file);
    $ext = end($exploding);
    
    switch($ext){
        case "png":
            $src = imagecreatefrompng($file);
        break;
        case "jpeg":
        case "jpg":
            $src = imagecreatefromjpeg($file);
        break;
        case "gif":
            $src = imagecreatefromgif($file);
        break;
        default:
            $src = imagecreatefromjpeg($file);
        break;
    }
    
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    return $dst;
}

function getChildCategoryByParentId($id){
  $category = \App\Category::find($id);
  if( $category ){
    return  $category->name;
  }
  return 'N/A';
}

function send_message($phone, $message){
  
  $args = http_build_query(array(
        'token' => 'fAs9aTR46FEumroJzMqa',
        'from'  => 'Klothus',
        'to'    => $phone,
        'text'  => $message
      ));
    
    $url = "http://api.sparrowsms.com/v2/sms/";

    # Make the call using API.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$args);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Response
    $response = curl_exec($ch);
    $response = json_decode($response,true);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $response;
}

function getImageSizeForProduct(){
  return [
      'thumbnail' => array(180,180),
      'medium'    => array(300,300),
      'catelog' => array(800,800),
      'full' => array(1200,1200)
  ];
}

function verify_google_recaptcha($recaptcha){
  $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' .urlencode('6Lfm9bwZAAAAAI_ydkinJ1iqdw-LkWjGxRycA_df').'&response='.urlencode($recaptcha);
  $response = file_get_contents($url);
  $responseKeys = json_decode($response,true);
  return $responseKeys['success'];
}

function getChildrenCategoryMenuhtml($parent_id = 0){

      $menus= App\Category::where(['parent_id'=>$parent_id])->orderBy('id', 'asc')->get()->toArray();
      $children = array();
      $i = 0;  
      if( !empty($menus ) ){
          
          foreach ($menus as $key => $menu) {
            $menusChild = App\Category::where(['parent_id'=>$menu['id']])->orderBy('id', 'asc')->get()->toArray(); 
            $li_class = 'gfg';    
            if(empty($menusChild)){ 
              $li_class = '';
            }
            echo '<li><span class="'.$li_class.'">';            
            if(isset($menu['image']) && $menu['image'] != null){
              echo '<a class="example-image-link" href="'.url($menu['image']).'" data-lightbox="example-set"><img width="28" height="28" src="'.url($menu['image']).'" class="rounded-circle" /></a>&nbsp;';
            }
            echo $menu['name'].'</span>';
            echo '&nbsp;<a class="text-primary" href="'.url('admin/category/edit/'.$menu['id']).'"><i class="fa fa-edit fa-sm"></i></a>';
            echo '&nbsp;<a class="text-danger" href="'.url('admin/category/delete/'.$menu['id']).'" onclick="return confirm(\'Are you sure?\')"><i class="fas fa-trash fa-sm"></i></a>';  
             echo '&nbsp;<a class="text-success" href="'.url('admin/category/store/?parent_id='.$menu['id']).'"><i class="fa fa-plus-circle fa-sm"></i></a>';     
            if(!empty($menusChild)){              
              echo '<ul class="cover">';
              getChildrenCategoryMenuhtml($menu['id']);
            }
            echo '</li>';
          }
          echo '</ul>';
      }
            /*echo '<li class="bg_g_father" data-submenu_id="'.$menu['id'].'">';
            echo '<i class="icon-folder-open fa fa-minus"></i>';
            if(isset($menu['image']) && $menu['image'] != null){
              echo '<img width="28" height="28" src="'.url($menu['image']).'" class="rounded-circle" />';
            }
            echo '<span> '.$menu['name'].'&nbsp;<a class="text-primary" href="'.url('admin/category/edit/'.$menu['id']).'"><i class="fas fa-user-edit fa-sm"></i></a>&nbsp;<a class="text-danger" href="'.url('admin/category/delete/'.$menu['id']).'" onclick="return confirm(\'Are you sure?\')"><i class="fas fa-trash fa-sm"></i></a></span>';

            getChildrenCategoryMenuhtml($menu['id']);
            echo '</li>';
            */
           // $i++;
          
     // return $children;
} 

function getGetAdminUserTokens(){
  return \App\User::whereHas('roles', function($q){ 
            $q->where('name','admin');
        })->pluck('device_token')->toArray();
}

function getAdmins(){
  return \App\User::whereHas('roles', function($q){ 
            $q->where('name','admin');
        })->get();
}

function admin_mail(){
  return 'info@klothus.com';
}

function get_setting($key){
    $setting = \App\Setting::firstOrNew(['key' => $key]);
    return $setting ? $setting->value : '';
}
?>