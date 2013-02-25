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
		.expand,
		.collapse {
			width: 16px;
			height: 16px;
			display: block;
			float: left;
			cursor: pointer;
			background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAr0lEQVR4Xu2SMQ7CIBSGH8LUeByGnkG9ghMkDKzqETRGPQAD3dyM13CpgzpIeo6mG8E8FkyNYdCxJC/58z348g+QEAL8ckafaBBorS9KqSClDEIInJiR4a4vYH3gvS8XyxUUxRhzZJRS6LoWDvtdmW1gjOHbzRqaxsHtcceJGRnucg1wrraqOBBST6azCI7nEyDDXU6QJNZyxliNwDk3T4+zgiR5q/z8dmn4yn8QvAAQGkk+aUWHyQAAAABJRU5ErkJggg==');
		}
		.expand {
			background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAtElEQVR4Xt2TvwqCYBTFjx/NXnsEoUfogYyWaq5AcRFEwcEeoHoUCaSGit5FN4f0VEJj0WdTneVOv8O5/wyS+EYKwI8b9F4BQRAUJOVejSdA8vMETdOI44zgeR47tUAS+f6AyXQG13XZaQYiguPpjPliCd/3qZ3AFBOmJajra7ct9C0LA9vGKk0RhuFQy0AphbIssN2sEcfxA75oGVRVle+yDFEUtbD2HSRJMgYgLfxGf/BMN4MbQuD5WgrGAAAAAElFTkSuQmCC');
		}
		</style>
		<script type="text/javascript">
		<!--
		    function exceptionist_toggle(c,id) {
		    	var e = document.getElementById(id);
		    	if (c.className == 'expand') {
		    		e.style.display = 'block';
		    		c.className = 'collapse';
		    	} else {
		    		c.className = 'expand';
		    		e.style.display = 'none';
		    	}
		    }
		//-->
		</script>
	</head>
	<body>
		<div id="exception-header">
			<h1><?php echo $exception_class .' '. $exception_hashcode; ?></h1>
			<h3><?php echo strip_tags($exception_message); ?></h3>
		</div>
		<?php $exception_count = count($exception_trace); ?>
		<div class="code-block primary-code-block">
			<h2>Source</h2>
			<p><tt><span class="collapse" onClick="exceptionist_toggle(this,'pcb-1');"></span><?php echo ($exception_count+1) .'. '. $exception_file . ':'. $exception_fileline  ?></tt></p>
			<pre id="pcb-1"><code class="php"><ul><?php foreach ($exception_codeblock as $line_no => $line_code): 
					$highlight_code = ($exception_fileline == $line_no) ? ' class="highlight"' : null; 
					echo sprintf('<li%s><span class="no">%s:</span>%s</li>', $highlight_code, $line_no, $line_code);
				endforeach; ?></ul></code></pre>
		</div>

		<div class="code-block stack-trace">
			<h2>Stack trace</h2>
			<?php foreach ($exception_trace as $i => $trace_entry): ?>
			<div class="entry">
			<p><tt><span class="collapse" onClick="exceptionist_toggle(this,'st-<?php echo $i?>');"></span><?php echo ($exception_count-$i) .'. '. $trace_entry['exception_file'] . ':'. $trace_entry['exception_fileline'] ?></tt></p>
			<pre id="st-<?php echo $i?>"><code class="php"><ul><?php foreach ($trace_entry['exception_codeblock'] as $line_no => $line_code): 
					$highlight_code = ($trace_entry['exception_fileline'] == $line_no) ? ' class="highlight"' : null; 
					echo sprintf('<li%s><span class="no">%s:</span>%s</li>', $highlight_code, $line_no, $line_code);
				endforeach; ?></ul></code></pre>
			</div>
			<?php endforeach; ?>
		</div>
	</body>
</html>