<script language='JavaScript' src='<?php echo jQuery; ?>' type='text/javascript'></script>
<script language='JavaScript' src='<?php echo sweetJs; ?>' type='text/javascript'></script>
<script language='JavaScript' src='<?php echo Site; ?>/public/js/gallery.js' type='text/javascript'></script>

<?php
class galleryController
{
    
    public function indexAction($request)
    {
        
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
		
		$Admins = array(1, 5); // for copy banners to admins
        
        $id_input = prepareStr($request[1]);
        if (!$id_input or $id_input == '') {
            echo '#error_id_input';
            exit;
        }
        echo openCss('sweet');
        echo openCss('gallery');
        
        $galleryDir         = mainDir . '/tracker/upload/banners/' . AuthID;
		$galleryDirAdmin    = mainDir . '/tracker/upload/banners/';
        $indexFileName      = $galleryDir . '/index.php';
        $exts               = array(
            'jpg',
            'jpeg',
            'png',
            'gif',
            'swf',
            'JPG',
            'JPEG',
            'PNG',
            'GIF',
            'SMF'
        );
        
        if (!file_exists($indexFileName)) {
            mkdir($galleryDir, 0777, true);
            $indexFile = '<?php 
					$back = "http://".$_SERVER["SERVER_NAME"];
					header("Location: ".$back);
					exit;';
            $fp        = fopen($indexFileName, "w");
            fwrite($fp, $indexFile);
            fclose($fp);
        }
        
        if (isset($_FILES['banner_file']) and $_FILES['banner_file']['name'] != '') {
            
            if ((($_FILES["banner_file"]["type"] != "image/gif") && 
			($_FILES["banner_file"]["type"] != "image/jpeg") &&
			($_FILES["banner_file"]["type"] != "image/png") && 
			($_FILES["banner_file"]["type"] != "application/x-shockwave-flash")) && 
			($_FILES["banner_file"]["size"] > 5242881)) 
			{			
                echo "#error_banner_file_size_or_type";
                exit;
            }
            
            if (is_uploaded_file($_FILES["banner_file"]["tmp_name"])) {
                
                $galleryNumber = count(scandir($galleryDir));
                $galleryNumber++;
                if ($_FILES["banner_file"]["type"] != "application/x-shockwave-flash") {
					require_once(Imager);
					$imager = new SimpleImage();
					$imager->load($_FILES["banner_file"]["tmp_name"]);
					$width      = $imager->getWidth();
					$height     = $imager->getHeight();
				} else {
					$width      = '';
					$height     = '';
				}				
                $banner_tmp = explode('.', $_FILES["banner_file"]["name"]);
                $name       = $banner_tmp[0];
                $ext        = array_pop($banner_tmp);
                
                $banner_file_name = $name . '(' . $width . 'x' . $height . ').' . $ext;
                
                if (!file_exists($galleryDir . '/' . $banner_file_name)) {
                    move_uploaded_file($_FILES["banner_file"]["tmp_name"], $galleryDir . '/' . $banner_file_name);

					if (ADMIN == 'false') {
						foreach ($Admins as $admin) {
							copy($galleryDir . '/' . $banner_file_name, $galleryDirAdmin . $admin . '/(' . AuthID .')_'. $banner_file_name);
						}
					}	
										
                } else {
                    echo '<script>file_exist();</script>';
                }
            } else {
                echo "#error_banner_file_upload";
                exit;
            }
        }
        
        $galleryList   = scandir($galleryDir, 1);
        $galleryImages = array();
        foreach ($galleryList as $file) {
            $file_ext = explode('.', $file);
            if (in_array($file_ext[1], $exts)) {
                $galleryImages[] = Site . '/tracker/upload/banners/' . AuthID . '/' . $file;
            }
        }

?>

<input type = 'hidden' value = '<?php echo $id_input; ?>' id = 'input_id' />

<div class = 'mainConteiner'>
<center><h2>Галерея пользователя</h2></center>

<form id = 'newGalleryElement' enctype='multipart/form-data' action = '' method = 'POST'>

<div class = 'input'>
<label>Выберите фаил банера (Макс. размер 5Мб)</label><br>
<input id = 'banner_file' autocomplete='off' type = 'file' name = 'banner_file' />
</div>

<div class = 'button'>
<input id = 'newGalleryElementGo' type = 'button' value = 'Загрузить' onclick = "formGo();"/>
</div>
</form>

<div class = 'clear'></div>

<?php
        foreach ($galleryImages as $img) {
            $ext = explode('.', $img);
            $ext = array_pop($ext);
?>

<div class = 'imgConteiner'>
<div src = '<?php echo $img; ?>' class = 'imgLayer'></div>
<div src = '<?php echo $img; ?>' class = 'imgLayerAdd'></div>
<?php
            if ($ext != 'swf') {
?>
<img width = '280px;' height = '260px;' src = '<?php echo $img; ?>'> </img><br>
<?php
            } else {
?>

<object style = 'width:280px;height:260px;' type='application/x-shockwave-flash' data='<?php echo $img; ?>'><param name='movie' value='<?php echo $img; ?>' /> </object>

<?php
            }
?>
<div class = 'ImgName'> <?php $img_name = explode('/', $img); echo array_pop($img_name); ?> </div>
<div class = 'ImgDel'><a href = '<?php echo Site; ?>/gallery/del/<?php echo $id_input; ?>/<?php echo AuthID; ?>/<?php $img_name = explode('/', $img); echo array_pop($img_name); ?>/'>Удалить</a></div>
</div>
<?
        }
?>



</div>

<div class = "clear"></div>
<div class = 'loadingLayer' id = 'loading'> <div class = 'loading'><img src = "<?php echo Site; ?>/public/img/loading.gif" /></div> </div>
<?php
        
        
        
        
    }
    
    public function delAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        if ($request[3] != AuthID) {
            header("Location: " . Site);
            exit;
        }
        
        $DB   = new DB();
        $used = $DB->query("SELECT id,name FROM trackers WHERE rule like '%" . $request[3] . "%" . $request[4] . "%';");
        
        if ($used != false) {
            header("Location: " . Site . "/gallery/" . $request[2] . "#(" . $used[0]['id'] . ")" . $used[0]['name']);
            exit;
        } else {
            $file   = mainDir . '/tracker/upload/banners/' . AuthID . '/' . $request[4];
            $result = unlink($file);
            header("Location: " . Site . "/gallery/" . $request[2] . "/");
            exit;
            
        }
        
        
    }
    
    
    
    
}




?>



