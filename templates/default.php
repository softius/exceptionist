<html>
	<head>
		<title></title>
		<style type="text/css">
		body {
			font-family: sans-serif;
			margin: 0;
			padding: 0;
			background-color: #fff;
		}
		#exception-header {
			background: #333;
			color: #fff;
			padding: 10px 20px;
		}
		h1, h2, h3 {
			font-weight: normal;
		}
		h2 {
			margin-top: 40px;
			margin-bottom: 5px;
		}
		h3 {
			margin-top: -10px;
			color: #ccc;
		}
		p {
			margin: 1em 0 5px;
		}
		.code-block {
			margin: 10px 20px;
		}
		.code-block pre {
			border: 1px solid #ddd;
			padding: 3px;
			margin: 0px;
		}
		.code-block ul {
			margin: 0;
			padding: 0;
			background-color: #ffd;
		}
		.code-block li {
			list-style-type: none;
			font-size: 12px;
			line-height: 12px;
			padding-top: 3px;
			padding-bottom: 3px;
			color: #333;
			font-family: monospace;
		}
		.code-block li span.no {
			color: #888;
			display: block; 
			float: left;
			width: 50px;
			text-align: right;
			margin-right: 10px;
			padding-right: 3px;
		}
		.code-block li.highlight {
			background-color: #FFCCCC;
			font-weight: bold;
		}
		</style>
	</head>
	<body>
		<div id="exception-header">
			<h1><?php echo $exception_class .' '. $exception_hashcode; ?></h1>
			<h3><?php echo strip_tags($exception_message); ?></h3>
		</div>
		<?php $exception_count = count($exception_trace); ?>
		<div class="code-block primary-code-block">
			<h2>Source</h2>
			<p><tt><?php echo ($exception_count+1) .'. '. $exception_file . ':'. $exception_fileline  ?></tt></p>
			<pre><code class="php"><ul><?php foreach ($exception_codeblock as $line_no => $line_code): 
					$highlight_code = ($exception_fileline == $line_no) ? ' class="highlight"' : null; 
					echo sprintf('<li%s><span class="no">%s:</span>%s</li>', $highlight_code, $line_no, $line_code);
				endforeach; ?></ul></code></pre>
		</div>

		<div class="code-block stack-trace">
			<h2>Stack trace</h2>
			<?php foreach ($exception_trace as $i => $trace_entry): ?>
			<div class="entry">
			<p><tt><?php echo ($exception_count-$i) .'. '. $trace_entry['exception_file'] . ':'. $trace_entry['exception_fileline'] ?></tt></p>
			<pre><code class="php"><ul><?php foreach ($trace_entry['exception_codeblock'] as $line_no => $line_code): 
					$highlight_code = ($trace_entry['exception_fileline'] == $line_no) ? ' class="highlight"' : null; 
					echo sprintf('<li%s><span class="no">%s:</span>%s</li>', $highlight_code, $line_no, $line_code);
				endforeach; ?></ul></code></pre>
			</div>
			<?php endforeach; ?>
		</div>
	</body>
</html>