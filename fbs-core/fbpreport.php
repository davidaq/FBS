<?php
function fbpreport()
{
	global $configData;
	global $playerData;
	$html_header = <<<END
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf8"/>
		<style type="text/css">
		body {
			font-family: arial;
			font-size: 1.1em;
			margin: 0 auto;
			width: 1000px;
		}
		table td {
			border: 1px solid #000;
			border-top: 0;
			border-right: 0;
			text-align: right;
			vertical-align: top;
			padding: 5px;
		}
		table {
			border: 1px solid #000;
			border-bottom: 0;
			border-left: 0;
			width: 100%;
			margin: 10px auto;
		}
		.pad {
			border: 0;
		}
		.head td {
			background: #E9DECF;
			font-weight: bold;
			text-align: center;
		}
		.hl {
			background: #E9DECF;
		}
		h2 {
			margin-top: 40px;
		}
		</style>
		<script type="text/javascript">
		function main() {
			var items = document.getElementsByTagName("td");
			for(k in items) {
				if(items[k].hasAttribute('filter') && items[k].innerHTML == items[k].getAttribute('filter')) {
					items[k].parentElement.style.display = "none";
				}
				if(items[k].hasAttribute('percent')) {
					items[k].innerHTML = (Math.ceil(items[k].innerHTML * 10000) / 100) + '%';
				}
			}
		}
		</script>
	</head>
	<body onload="main()">
END;

	$html_footer = <<<END
	</body>
</html>
END;


}
?>
