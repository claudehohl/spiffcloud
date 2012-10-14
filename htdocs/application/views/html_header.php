<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo $title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="generator" content="Codeigniter" />
	<meta name="keywords" content="Brainstorm, Mindmap, Tagcloud, Connected, Mind" />
	<meta name="author" content="Claude Hohl, Fabian Schär" />
	<link rel="shortcut icon" href="<?php echo base_url(); ?>img/favicon.ico" />
<?php
//Carabiner
$this->carabiner->config(array(
    'script_dir' => 'static/js/', 
    'style_dir'  => 'static/styles/',
    'cache_dir'  => 'static/asset/',
    'base_uri'	 => base_url(),
    'combine'	 => true,
    'dev'		 => false,
));

// CSS
$this->carabiner->css('jquery.autocomplete.css');
$this->carabiner->css('main.css');

$this->carabiner->display('css'); 
?>
<script type="text/javascript">
//<![CDATA[
var base_url = '<?php echo base_url(); ?>';
//]]>
</script>
</head>
<body>

