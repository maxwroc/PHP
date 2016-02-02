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