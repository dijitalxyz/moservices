<?php
#############################################################################
# Author: stalker 2011                                                      #
# Author: consros 2011                                                      #
#############################################################################

class ParserTools {
    function compactHtml($html) {
        # compact all to a single line
        $html = preg_replace('|\n|', '', $html);

        # no spaces between tags
        $html = preg_replace('|>\s+|', '>', $html);
        $html = preg_replace('|\s+<|', '<', $html);

        return $html;
    }

    function parseOutCookies($html) {
        preg_match_all("/Set-Cookie: .*?;/", $html, $matches);
        $cookies = "";
        foreach ($matches[0] as $cookie) {
            $cookies .= substr($cookie, 4, strlen($cookie) - 5) . "\r\n";
        }
        return $cookies;
    }

    static function parseTokens($scope, $prefix1, $suffix1, $prefix2 = null, $suffix2 = null) {
      	$tokens = array();
        while (true) {
            $start = strpos($scope, $prefix1);
        	if (false === $start) {
                break;
            }
            $start += strlen($prefix1);
            $stop  = strpos($scope, $suffix1, $start);
            if (false === $stop) {
                break;
            }

            $token1 = substr($scope, $start, $stop - $start );
            $scope = substr($scope, $stop + strlen($suffix1));
            if (null == $prefix2 || null == $suffix2) {
            	$tokens[] = $token1;
            	continue;
            }

            $start = strpos($scope, $prefix2);
            if (false === $start) {
                break;
            }
            $start += strlen($prefix2);
            $stop  = strpos($scope, $suffix2, $start);
            if (false === $stop) {
                break;
            }
            $token2 = substr($scope, $start, $stop - $start );
            $scope = substr($scope, $stop + strlen($suffix2));

            $tokens[$token1] = $token2;
        }

        return $tokens;
    }

    static function parseParam($scope, $prefix, $suffix, $default = null, $occurence = 1) {
        for ($start = 0; $occurence > 0; $occurence--) {
        	$start = null == $prefix ? 0 : 
        	   strpos($scope, $prefix, $start);
            if (false === $start) {
                return $default;
            }
            $start += strlen($prefix);
        }
        $stop =  null == $suffix ? strlen($scope) : 
            strpos($scope, $suffix, $start);
        if (false === $stop) {
            return $default;
        }
        return substr($scope, $start, $stop - $start);
    }


    /*
     * Remove All HTML tags and replace some common HTML Entities with characters
     * */
    static function removeHtmlTags($str) {

		$search = array ('@<script[^>]*?>.*?</script>@si', // Strip out javascript
		                 '@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
		                 '@([\r\n])[\s]+@',                // Strip out white space
		                 '@&(quot|#34);@i',                // Replace HTML entities
		                 '@&(amp|#38);@i',
		                 '@&(lt|#60);@i',
		                 '@&(gt|#62);@i',
						 '@&(hellip|#133);@i',
		                 '@&(nbsp|#160);@i',
		                 '@&(iexcl|#161);@i',
		                 '@&(cent|#162);@i',
		                 '@&(pound|#163);@i',
		                 '@&(copy|#169);@i',
						 '@&(laquo|#171);@i',
						 '@&(raquo|#187);@i',
						 '@&(ndash|#8211);@i',
						 '@&(mdash|#8212);@i',
						 '@&(lsquo|#8216);@i',
						 '@&(rsquo|#8217);@i',
						 '@&(ldquo|#8220);@i',
						 '@&(rdquo|#8221);@i',
						 '@&(lsaquo|#8249);@i',
						 '@&(rsaquo|#8250);@i',
		                 '@&#(\d+);@e');                    // evaluate as php

		$replace = array ('',
		                 '',
		                 '\1',
		                 '"',
		                 '&',
		                 '<',
		                 '>',
						 '...',
		                 ' ',
		                 chr(161),
		                 chr(162),
		                 chr(163),
		                 chr(169),
		                 chr(171),
		                 chr(187),
		                 chr(8211),
		                 chr(8212),
		                 chr(8216),
		                 chr(8217),
		                 chr(8220),
		                 chr(8221),
		                 chr(8249),
		                 chr(8250),
		                 'chr(\1)');

		return preg_replace($search, $replace, $str);			
			
    }
}

?>
