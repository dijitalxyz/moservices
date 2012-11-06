<?php

/*
	iptvlist created by Roman Lut aka hax.
*/

function endsWith($haystack,$needle,$case=true)
{
  $expectedPosition = strlen($haystack) - strlen($needle);

  if($case)
      return strrpos($haystack, $needle, 0) === $expectedPosition;

  return strripos($haystack, $needle, 0) === $expectedPosition;
}

function apply_filters($tag, $value) 
{
        global $wp_filter, $merged_filters, $wp_current_filter;

        $args = array();

        // Do 'all' actions first
        if ( isset($wp_filter['all']) ) {
                $wp_current_filter[] = $tag;
                $args = func_get_args();
                _wp_call_all_hook($args);
        }

        if ( !isset($wp_filter[$tag]) ) {
                if ( isset($wp_filter['all']) )
                        array_pop($wp_current_filter);
                return $value;
        }

        if ( !isset($wp_filter['all']) )
                $wp_current_filter[] = $tag;

        // Sort
        if ( !isset( $merged_filters[ $tag ] ) ) {
                ksort($wp_filter[$tag]);
                $merged_filters[ $tag ] = true;
        }

        reset( $wp_filter[ $tag ] );

        if ( empty($args) )
                $args = func_get_args();

        do {
                foreach( (array) current($wp_filter[$tag]) as $the_ )
                        if ( !is_null($the_['function']) ){
                                $args[1] = $value;
                                $value = call_user_func_array($the_['function'], array_slice($args, 1, (int) $the_['accepted_args']));
                        }

        } while ( next($wp_filter[$tag]) !== false );

        array_pop( $wp_current_filter );

        return $value;
}


function sanitize_file_name( $filename ) {
    $filename_raw = $filename;
    $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
    $special_chars = apply_filters('sanitize_file_name_chars', $special_chars, $filename_raw);
    $filename = str_replace($special_chars, '', $filename);
    $filename = preg_replace('/[\s-]+/', '-', $filename);
    $filename = trim($filename, '.-_');
    return apply_filters('sanitize_file_name', $filename, $filename_raw);
}                               


if( isset( $_GET["name"] ) ) 
{
	if( isset( $_GET["newname"] ) ) 
	{
		$newName = $_GET["newname"];

		if ( endsWith( $newName, ".mpg", false ) == false )
		{
			$newName .= ".mpg";
		}

		$newName = sanitize_file_name( $newName );

		$name = $_GET["name"];

		$slpos = strrpos( $name, "/" );
		if ( $slpos === false )
		{
			$slpos = strrpos( $name, '\\' );
			if ( $slpos === false )
			{
				$slpos = strlen( $name ) - 1;
			}
		}

		$newName = substr( $name, 0, $slpos + 1 ) . $newName;

		rename( $name, $newName );
		echo $slpos;
	}
}

//echo "done";
?>
