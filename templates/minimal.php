<html>
	<head>
		<title></title>
		<style type="text/css">
		body {
			font-family: sans-serif;
			margin: 0;
			padding: 0;
		}
		#exception-header {
			padding: 10px;
		}
		#exception-header h1 {
			color: red;
		}
		#exception-header h2 {
			color: #999;
		}
		h1, h2, h3 {
			font-weight: normal;
			font-family: monospace;
		}
		h1 {
			margin-top: 0;
		}
		h2 {
			margin-top: -10px;
			margin-bottom: 5px;
		}
		</style>
	</head>
	<body>
		<div id="exception-header">
			<h1><?php echo $exception_class .' '. $exception_hashcode; ?></h1>
			<h2><?php echo $exception_message ?></h2>
		</div>
	</body>
</html>