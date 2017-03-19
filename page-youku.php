<html>
<head>
<title>Youku</title>
<script type="text/javascript" src="http://player.youku.com/jsapi"></script>
<style>
html,body {
   margin:0;
   padding:0;
}
</style>
</head>
<body>
<?php
if(!isset($_GET['id'])){
  exit;
}
$id = $_GET['id'];
$width = isset($_GET['width'])?$_GET['width']: '100%';
$height = isset($_GET['height'])?$_GET['height']: '100%';


?>
<div id="youkuplayer" style="width:<?php echo $width;?>;height:<?php echo $height;?>"></div>
<script type="text/javascript">
new YKU.Player('youkuplayer',{
styleid: '0',
client_id: 'f0dc41c050298748',
vid: '<?php echo $id;?>',
newPlayer: true
});
</script>

</body>
</html>
