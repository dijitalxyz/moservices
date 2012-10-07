<?php
#############################################################################
# JSON extremely light decoder without RegEx usage.                         #
# Was introduced to speed up JSON handling on xTreamer with IP-TV Rodnoe.TV #
#                                                                           #
# Author: consros 2010                                                      #
#############################################################################

# activate if no internal json_decode defined
if (! function_exists('json_decode')) {
    function json_decode($jsonString, $asHash = true) {
        $decoder = new LightJsonDecoder();
        return $decoder->decode($jsonString);
    }
}


class LightJsonDecoder {
    var $removeBoundingQuotes = true;
    var $removeEscapingSlashes = true;
    var $decodeUtf8 = true;

    # constructor
    function LightJsonDecoder($removeBoundingQuotes = true,
        $removeEscapingSlashes = true,
        $decodeUtf8 = true)
    {
        $this->removeBoundingQuotes = $removeBoundingQuotes;
        $this->removeEscapingSlashes = $removeEscapingSlashes;
        $this->decodeUtf8 = $decodeUtf8;
    }

    # performs decoding procedure
    function decode($jsonString) {
        $starttime = microtime(true);

        $jsonString = $this->encodeEscaped($jsonString);
        $params = $this->parseValue($jsonString);

        $endtime = microtime(true);
        $params['decoding_time'] = $endtime - $starttime;
        # print "Time " . $params['decoding_time'] . " for $jsonString\n";

        return $params;
    }

    # detects value type and parses it accordingly
    function parseValue($valueToken) {
        if ($this->isObject($valueToken)) {
            return $this->parseObject($valueToken);
        } elseif ($this->isArray($valueToken)) {
            return $this->parseArray($valueToken);
        }
        # it's the value itself
        return $this->processParameter($valueToken);
    }

    # parses value of object type
    function parseObject($objectToken) {

        # remove bounding braces
        $objectToken = substr($objectToken, 1, -1);

        # split on fields
        $fieldTokens = $this->splitTokens($objectToken);

        # field will be mapped as array of key => value
        $params = array();
        foreach ($fieldTokens as $fieldToken) {
            $pos = strpos($fieldToken, ':'); # ERROR_SYNTAX if FALSE
            $key = substr($fieldToken, 0, $pos);
            $key = $this->processParameter($key);
            $valueToken = substr($fieldToken, $pos + 1);
            $value = $this->parseValue($valueToken);
            $params[$key] = $value;
        }
        return $params;
    }

    # parses value of array type
    function parseArray($arrayToken) {

        # remove bounding brackets
        $arrayToken = substr($arrayToken, 1, -1);

        # split on elements
        $elementTokens = $this->splitTokens($arrayToken);

        # elements will be collected in plain array
        $params = array();
        foreach ($elementTokens as $elementToken) {
            $params[] = $this->parseValue($elementToken);
        }
        return $params;
    }

    # returns true if given token is of object type
    function isObject($token) {
        $len = strlen($token);
        return $len >= 2 && '{' == $token[0] && '}' == $token[$len - 1];
    }

    # returns true if given token is of array type
    function isArray($token) {
        $len = strlen($token);
        return $len >= 2 && '[' == $token[0] && ']' == $token[$len - 1];
    }

    # splits given json string on comma separated tokens
    function splitTokens($jsonString) {
        $quoteCounter   = 0;
        $braceCounter   = 0;
        $bracketCounter = 0;

        $tokens = array();
        $token = "";
        for ($i = 0; $i < strlen($jsonString); $i++) {
            $ch = $jsonString[$i];
            $token .= $ch;
            switch ($ch) {
            case '"': $quoteCounter++;   break;
            case '{': $braceCounter++;   break;
            case '}': $braceCounter--;   break; # ERROR_SYNTAX if braceCounter < 0
            case '[': $bracketCounter++; break;
            case ']': $bracketCounter--; break; # ERROR_SYNTAX if bracketCounter < 0
            case ',':
                # there should be no open block or open parameter
                if (0 == $braceCounter && 0 == $bracketCounter && 0 == $quoteCounter % 2) {
                    $tokens[] = substr($token, 0, -1); # exclude this comma
                    $token = "";
                }
                break;
            }
        }
        if ("" != $token) {
            $tokens[] = $token;
        }
        # ERROR_SYNTAX if quoteCounter % 2 != 0
        # ERROR_SYNTAX if braceCounter != 0
        # ERROR_SYNTAX if bracketCounter != 0
        return $tokens;
    }

    # encode characters preventing parsing
    function encodeEscaped($jsonString) {
        $jsonString = str_replace('\\\\', '<LJ_BACKSLASH>',    $jsonString);
        $jsonString = str_replace('\\"',  '<LJ_QUOTE>',        $jsonString);
        $jsonString = str_replace('\\:',  '<LJ_COLON>',        $jsonString);
        $jsonString = str_replace('\\,',  '<LJ_COMMA>',        $jsonString);
        $jsonString = str_replace('\\[',  '<LJ_SQ_BRACKET_L>', $jsonString);
        $jsonString = str_replace('\\]',  '<LJ_SQ_BRACKET_R>', $jsonString);
        $jsonString = str_replace('\\{',  '<LJ_BRACE_L>',      $jsonString);
        $jsonString = str_replace('\\}',  '<LJ_BRACE_R>',      $jsonString);
        return $jsonString;
    }

    # restore characters preventing parsing if any
    function decodeEscaped($jsonString) {
        $jsonString = str_replace('<LJ_BRACE_R>',       '\\}', $jsonString);
        $jsonString = str_replace('<LJ_BRACE_L>',       '\\{', $jsonString);
        $jsonString = str_replace('<LJ_SQ_BRACKET_R>',  '\\]', $jsonString);
        $jsonString = str_replace('<LJ_SQ_BRACKET_L>',  '\\[', $jsonString);
        $jsonString = str_replace('<LJ_COMMA>',         '\\,', $jsonString);
        $jsonString = str_replace('<LJ_COLON>',         '\\:', $jsonString);
        $jsonString = str_replace('<LJ_QUOTE>',         '\\"', $jsonString);
        $jsonString = str_replace('<LJ_BACKSLASH>',    '\\\\', $jsonString);
        return $jsonString;
    }

    # performs all needed conversions on ready parsed parameter
    function processParameter($param) {
        if ($this->removeBoundingQuotes && strlen($param) >= 2 &&
            '"' == $param[0] && '"' == $param[strlen($param) - 1])
        {
            $param = substr($param, 1, -1);
        }

        $param = $this->decodeEscaped($param);

        if ($this->decodeUtf8) {
            $param = decodeUtf8($param);
        }

        if ($this->removeEscapingSlashes) {
            $param = str_replace('\\', '', $param);
        }

        return $param;
    }
}


# The following function will decode \uXXXX
# sequentially, without temporary data.
#
# Taken from http://www.php.net/manual/en/function.urldecode.php#71211
# because it's the fastest from all.
function decodeUtf8($str) {
    $res = '';
    for ($i = 0; $i <= strlen($str) - 6; $i++) {
        $character = $str[$i];
        if ($character == '\\' && $str[$i + 1] == 'u') {
            $value = hexdec(substr($str, $i + 2, 4));

            if ($value < 0x0080) {
                // 1 byte: 0xxxxxxx
                $character = chr($value);
            } else if ($value < 0x0800) {
                // 2 bytes: 110xxxxx 10xxxxxx
                $character  = chr((($value & 0x07c0) >> 6) | 0xc0);
                $character .= chr(($value & 0x3f) | 0x80);
            } else {
                // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
                $character  = chr((($value & 0xf000) >> 12) | 0xe0);
                $character .= chr((($value & 0x0fc0) >> 6) | 0x80);
                $character .= chr(($value & 0x3f) | 0x80);
            }
            $i += 5;
        }
        $res .= $character;
    }
    return $res . substr($str, $i);
}
?>
