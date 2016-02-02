<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $title; ?></title>
<style type="text/css">
<!--
	body {
		font-family: Verdana;
		font-size: 10pt;
	}
	
	h1 {
		font-size: 14pt;
		text-weight: bold;
	}
	
	h3 {
		font-size: 12pt;
	}
-->
</style>
</head>
<body>
<h1>Lithium 404 error:</h1>
<h3><?php echo $message; ?></h3>
<?php if ( isset( $description ) ) { ?>
<p><?php echo $description; ?></p>
<?php } ?>
<?php if ( isset( $file ) AND isset( $line ) ) { ?>
	<p>File: <?php echo $file; ?> (<?php echo $line; ?>)</p>
<?php } ?>

<?php 
if ( ! empty( $aTrace ) ) {
	echo '<hr />', '<ul class="backtrace">';
	$iCounter = 0;
	foreach ( $aTrace as $aItem ) {
		
		$iCounter++;
		
		$aArgDump = array();
		
		echo '<li>';
		
		if ( isset( $aItem['file'] ) ) {
			printf( '<tt>%s <strong>%d:</strong></tt>', $aItem['file'], $aItem['line'] );
		}
		
		echo '<pre>';
		
		if ( isset( $aItem['class'] ) ) {
			// Add class and call type
			printf( '<span class="code_class">%s</span><span class="code_operator">%s</span>',
				$aItem['class'],
				$aItem['type']
			);
		}
		
		printf( '<span class="code_function">%s</span><span class="code_operator">(</span>%s', 
			$aItem['function'],
			empty( $aItem['args'] ) ? '' : ' '
		);
		
		$sSep = '';
		foreach ( $aItem['args'] as $aArg ) {
			
			echo $sSep;
			
			if ( empty( $aArg['short'] ) ) {
				printf( '<span class="code_arg">%s</span>', $aArg['content'] );
			} else {
				printf( '<a href="javascript:void(0)" onclick="showArgDetails( \'step_%d_arg_detail_%d\' )" class="code_arg">%s</a>', 
					$iCounter,
					count( $aArgDump ),
					$aArg['short']
				);
				$aArgDump[] = $aArg['content'];
			}
			
			$sSep = '<span class="code_operator">,</span> ';
			
		} // foreach
		
		printf( '%s<span class="code_operator">)</span></pre>',
			empty( $aItem['args'] ) ? '' : ' '
		);
		
		// display detailed args dump
		if ( ! empty( $aArgDump ) ) {
			for ( $j = 0, $max_count = sizeof( $aArgDump ); $j < $max_count; $j++ ) {
				printf( '<div style="display: none;" id="step_%d_arg_detail_%d" class="code_block"><pre>%s</pre></div>', 
					$iCounter,
					$j,
					$aArgDump[ $j ]
				);
			}
		} // if
		
		echo '</li>';
		
	} // foreach
	
	echo '</ul>';
} 
?>
<hr />
<p class="small">Lithium <?php echo $version; ?></p>
</body>
</html>