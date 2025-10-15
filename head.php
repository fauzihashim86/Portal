<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title><?php echo $sitename;?></title>
	<link rel="icon" href="src/images/mtd.png" type="image/x-icon">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
	<link rel="stylesheet" type="text/css" href="src/plugins/datatables/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/datatables/css/responsive.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/switchery/switchery.min.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.css">
	<link rel="stylesheet" type="text/css" href="src/styles/core.css">
	<link rel="stylesheet" type="text/css" href="src/styles/icon-font.min.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/jvectormap/jquery-jvectormap-2.0.3.css">
	<link rel="stylesheet" type="text/css" href="src/styles/style.css">
	<link rel="stylesheet" type="text/css" href="src/styles/extra.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/sweetalert2/sweetalert2.css">
	<style>
		.anyClass {
			height:600px;
			overflow-y: scroll;
		}
		.tree, .tree ul {
    	margin:0;
    	padding:0;
    	list-style:none
		}
		.tree ul {
	    margin-left:1em;
	    position:relative
		}
		.tree ul ul {
	    margin-left:.5em
		}
		.tree ul:before {
	    content:"";
	    display:block;
	    width:0;
	    position:absolute;
	    top:0;
	    bottom:0;
	    left:0;
	    border-left:1px solid;
		}
		.tree li {
	    margin:0;
	    padding:0 1em;
	    line-height:2em;
	    color:#369;
	    font-weight:700;
	    position:relative;
			font-size: 10px
		}
		.tree ul li:before {
	    content:"";
	    display:block;
	    width:10px;
	    height:0;
	    border-top:1px solid;
	    margin-top:-1px;
	    position:absolute;
	    top:1em;
	    left:0;
			font-size: 10px;
		}
		.tree ul li:last-child:before {
	    background:#fff;
	    height:auto;
	    top:1em;
	    bottom:0
			font-size: 10px;
		}
		.indicator {
	    margin-right:5px;
		}
		.tree li a {
	    text-decoration: none;
	    color:#369;
			font-size: 10px;
		}
		.tree li button, .tree li button:active, .tree li button:focus {
	    text-decoration: none;
	    color:#369;
	    border:none;
	    background:transparent;
	    margin:0px 0px 0px 0px;
	    padding:0px 0px 0px 0px;
	    outline: 0;
		}
	</style>
