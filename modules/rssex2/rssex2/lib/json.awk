# Copyright (c) 2010 Dan Saar
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.


# Issues:
#
#    1) This parser handles UTF-8 input data.  Other data must be converted to UTF-8 using iconv.
#       Even so, in order to process UTF-8 characters correctly, must use 8-bit compatible AWK.  
#       For gawk, this means setting LC_ALL=C.
#
#    2) Since awk arrays are used:
#       a) SUBSEP (\f) can't be used within an object property name
#       b) An object with contiguous numeric property names starting at 1 is indistinguishable from an array 
#       c) Some awks don't like embedded newlines in strings when splitting, which affects what is allowed
#          in property names, because split() is used to parse out what was put into the array
#
#    3) Since awk values are not strongly typed:
#       a) A string that contains nothing except a valid number is indistinguishable from the latter
#       b) "<<true>>", "<<false>>", "<<null>>", and "<<novalue>>" are special
#       c) Strings starting with "ParseJSON Error: " are probably special
#
#    4) Some awks don't like embedded nulls in strings
#
#    5) It's not possible to have an empty array or empty object, because they map to an awk array instance
#       that has nothing in it.
#
#    6) It's not possible to use the empty string as a property name
#
#    7) Numbers are not actually converted to numbers, so until used as such, can contain more precision
#       than is actually possible.
#
#    8) Posix gawk and mawk interpret repeated slashes in gsub differently than do traditional awk and gawk,
#       so literal backslashes are encoded as \u005C in formatted output
#

#
# JSON Parsing Routines
#

function prsJSON_hex2num(s,     rv, ii, len, k)
{
   rv = 0
   s = tolower(s)
   len = length(s)
   
   for (ii = 1; ii <= len; ii++)
   {
      k = index("0123456789abcdef", substr(s, ii, 1))
      if (k > 0)
         rv = rv * 16 + (k-1)
      else
         break;
   }

   return rv
}

function prsJSON_EncodeAsUTF8( v,      s, p1, p2, p3, p4, cs )
{
   cs = "\200\201\202\203\204\205\206\207\210\211\212\213\214\215\216\217\220\221\222\223\224\225\226\227\230\231\232\233\234\235\236\237\240\241\242\243\244\245\246\247\250\251\252\253\254\255\256\257\260\261\262\263\264\265\266\267\270\271\272\273\274\275\276\277\300\301\302\303\304\305\306\307\310\311\312\313\314\315\316\317\320\321\322\323\324\325\326\327\330\331\332\333\334\335\336\337\340\341\342\343\344\345\346\347\350\351\352\353\354\355\356\357\360\361\362\363\364\365\366\367\370\371\372\373\374\375\376\377"
      
   if ( v < 128 )
      s = sprintf("%c", v )
      
   else if ( v < 2048 ) # 110xxxxx 10xxxxxx
   {
      p1 = int(v/64) % 32
      p2 = v % 64
      s = substr(cs, 65+p1, 1) substr(cs, p2+1, 1)
   }
   
   else if ( v < 65536 ) # 1110xxxx 10xxxxxx 10xxxxxx
   {
      p1 = int(v/4096) % 16
      p2 = int(v/64) % 64
      p3 = v % 64
      s = substr(cs, 97+p1, 1) substr(cs, p2+1, 1) substr(cs, p3+1, 1)
   }
   
   else if ( v < 1114112 ) # 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
   {
      p1 = int(v/262144) % 8
      p2 = int(v/4096) % 64
      p3 = int(v/64) % 64
      p4 = v % 64
      s = substr(cs, 113+p1, 1) substr(cs, p2+1, 1) substr(cs, p3+1, 1) substr(cs, p4+1, 1)
   }

   else
      s = ""
   
   return s;
}

function prsJSON_UnescapeString(jsonString,     matchedString, matchedValue)
{
   if (jsonString == "\"\"")
      return ""

   if (jsonString ~ /^".+"$/) 
      jsonString = substr(jsonString,2,length(jsonString)-2)

   gsub(/\\\\/, "\\u005C", jsonString)
   gsub(/\\"/, "\"", jsonString)
   gsub(/\\\//, "/", jsonString)
   gsub(/\\b/, "\b", jsonString)
   gsub(/\\f/, "\f", jsonString)
   gsub(/\\n/, "\n", jsonString)
   gsub(/\\r/, "\r", jsonString)
   gsub(/\\t/, "\t", jsonString)

   if (match(jsonString, /\\[^u]/))
      return "ParseJSON Error: Invalid String at " jsonString
   
   # handle encoded UTF-16 surrogates
   while (match(jsonString, /\\uD[89AaBb][0123456789AaBbCcDdEeFf][0123456789AaBbCcDdEeFf]\\uD[CcDdEeFf][0123456789AaBbCcDdEeFf][0123456789AaBbCcDdEeFf]/))
   {
      matchedValue = (prsJSON_hex2num(substr(jsonString, RSTART+2, 4)) % 1024) * 1024 + prsJSON_hex2num(substr(jsonString, RSTART+8, 4)) % 1024 + 65536
      #print matchedValue, substr(jsonString, RSTART+2, 4), substr(jsonString, RSTART+8, 4)
      matchedString = prsJSON_EncodeAsUTF8( matchedValue )
      sub(/\\uD[89AaBb][0123456789AaBbCcDdEeFf][0123456789AaBbCcDdEeFf]\\uD[CcDdEeFf][0123456789AaBbCcDdEeFf][0123456789AaBbCcDdEeFf]/, matchedString, jsonString)
   }
   
   while (match(jsonString, /\\u[0123456789AaBbCcDdEeFf][0123456789AaBbCcDdEeFf][0123456789AaBbCcDdEeFf][0123456789AaBbCcDdEeFf]/))
   {
      matchedValue = prsJSON_hex2num(substr(jsonString, RSTART+2, 4))
      matchedString = prsJSON_EncodeAsUTF8( matchedValue )
      sub(/\\u[0123456789AaBbCcDdEeFf][0123456789AaBbCcDdEeFf][0123456789AaBbCcDdEeFf][0123456789AaBbCcDdEeFf]/, matchedString, jsonString)
   }
   
   return jsonString;
}

function prsJSON_ValidString(jsonString)
{
   return jsonString !~ /^ParseJSON Error: Invalid String at /
}

function prsJSON_SetDataValue(jsonData, prefix, value)
{
   jsonData[prefix] = value
}

function prsJSON_Error(jsonStringArr, cnt, idx, jsonData, message)
{
   split("", jsonData)
   prsJSON_SetDataValue(jsonData, "1", sprintf("ParseJSON Error: %s at ", message) (idx <= cnt ? jsonStringArr[idx] : ""))
   split("", jsonStringArr)
   return cnt + 1
}

function prsJSON_CopyError(jsonData, tv)
{
   split("", jsonData)
   prsJSON_SetDataValue(jsonData, "1", tv[1])
}

function prsJSON_ParseNumber(jsonStringArr, cnt, idx, jsonData, prefix)
{
   if (idx <= cnt)
   {
      if (match(jsonStringArr[idx], /^(\-?)(0|[123456789][0123456789]*)(\.[0123456789]+)?([eE][+-]?[0123456789]+)?/)) 
      {
         prsJSON_SetDataValue(jsonData, prefix, substr(jsonStringArr[idx], 1, RLENGTH))
         jsonStringArr[idx] = length(jsonStringArr[idx]) >= RLENGTH+1 ? substr(jsonStringArr[idx], RLENGTH+1) : ""
      }
      else
         idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Number not found") # starts like a number, but doesn't match the REGEX
   }

   return idx
}

function prsJSON_ParseString(jsonStringArr, cnt, idx, jsonData, prefix,      jsonString, idxn, idxs, idxq, t)
{
   if (idx <= cnt && length(jsonStringArr[idx]) > 0 && substr(jsonStringArr[idx], 1, 1) == "\"")
   {
      idxn = 2
      jsonString = jsonStringArr[idx]

      do
      {
         t = length(jsonString) >= idxn ? substr(jsonString, idxn) : ""
         idxs = index(t, "\\")
         idxq = index(t, "\"")

         # no valid close quote found
         if (idxq == 0)
         {
            if (idx == cnt)
               break;

            idx++
            jsonString = jsonString "," jsonStringArr[idx]
         }

         # a valid close quote was found - not before a slash
         if (idxq != 0 && (idxs == 0 || (idxs != 0 && idxq < idxs)))
            break;

         if (idxs != 0 && idxq == idxs + 1) # slash quote
            idxn = idxn + idxq

         else
            idxn = idxn + idxs + 1

      } while (1)

      if (idxq > 0)
      {
         t = substr(jsonString, 1, idxn+idxq-1)
         if (match(t, /[\001\002\003\004\005\006\007\010\011\012\013\014\015\016\017\020\021\022\023\024\025\026\027\030\031\032\033\034\035\036\037]/) == 0)
         {
            t = prsJSON_UnescapeString(t)
            if ( prsJSON_ValidString(t) )
            {
               prsJSON_SetDataValue(jsonData, prefix, t)
               jsonStringArr[idx] = length(jsonString) >= idxn+idxq ? substr(jsonString,idxn+idxq) : ""
            }
            else
               idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Invalid string") 
         }
         else
            idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Invalid character in string") 
      }
      else
         idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Unterminated string") 
   }
   else
      idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "String expected")
      
   return idx
}

function prsJSON_ParseObject(jsonStringArr, cnt, idx, jsonData, prefix,     tv )
{
   if (idx <= cnt)
   {
      sub(/^\{[ \t\r\n\f]*/, "", jsonStringArr[idx]) #skip open { and skipwhite

      while (idx <= cnt && length(jsonStringArr[idx]) > 0 && substr(jsonStringArr[idx], 1, 1) != "}")
      {
         idx = prsJSON_ParseString(jsonStringArr, cnt, idx, tv, "1")
         
         if (idx <= cnt && length(tv[1]) == 0)
             idx = prsJSON_Error(jsonStringArr, cnt, idx, tv, "Empty string used for property name")

         if (idx <= cnt)
         {
            sub(/^[ \t\r\n\f]+/, "", jsonStringArr[idx]) #skipwhite
      
            if ( length(jsonStringArr[idx]) > 0 && substr(jsonStringArr[idx], 1, 1) == ":" )
            {
               sub(/^:[ \t\r\n\f]*/, "", jsonStringArr[idx]) #skip colon and skipwhite
          
               if ( length(jsonStringArr[idx]) > 0 )
               {
                  idx = prsJSON_ParseJSONInt(jsonStringArr, cnt, idx, jsonData, prefix != "" ? prefix SUBSEP tv[1] : tv[1])
                  if (idx <= cnt)
                  {
                     sub(/^[ \t\r\n\f]+/, "", jsonStringArr[idx]) #skipwhite
       
                     if (length(jsonStringArr[idx]) == 0 && idx < cnt)
                     {
                        idx++
                        sub(/^[ \t\r\n\f]+/, "", jsonStringArr[idx]) #skipwhite
                        if (length(jsonStringArr[idx]) == 0 || substr(jsonStringArr[idx], 1, 1) == "}")
                           idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Expected object property")
                     }
         
                     else if (length(jsonStringArr[idx]) == 0 || substr(jsonStringArr[idx], 1, 1) != "}")
                        idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Expected object property or closing brace")
                  }
               }
               else
                  idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Expected JSON value (1)")
            }
            else
               idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Expected colon")
         }
         else
            prsJSON_CopyError(jsonData, tv)
      }
   
      if (idx <= cnt && (length(jsonStringArr[idx]) == 0 || substr(jsonStringArr[idx], 1, 1) != "}"))
         idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Expected closing brace")
   
      if (idx <= cnt && length(jsonStringArr[idx]) > 0 && substr(jsonStringArr[idx], 1, 1) == "}")
         sub(/^\}[ \t\r\n\f]*/, "", jsonStringArr[idx]) #skip close } and skipwhite
   }  

   return idx
}

function prsJSON_ParseArray(jsonStringArr, cnt, idx, jsonData, prefix,     ii)
{
   if (idx <= cnt)
   {
      sub(/^\[[ \t\r\n\f]*/, "", jsonStringArr[idx]) #skip open bracket and skipwhite
      ii = 1

      while (idx <= cnt && length(jsonStringArr[idx]) > 0 && substr(jsonStringArr[idx], 1, 1) != "]")
      {
         idx = prsJSON_ParseJSONInt(jsonStringArr, cnt, idx, jsonData, prefix != "" ? prefix SUBSEP ii : ii )
         ii++
    
         if (idx <= cnt)  
         {
            sub(/^[ \t\r\n\f]+/, "", jsonStringArr[idx]) #skipwhite
      
            if (length(jsonStringArr[idx]) == 0 && idx < cnt)
            {
               idx++;
               sub(/^[ \t\r\n\f]+/, "", jsonStringArr[idx]) #skipwhite
               if (length(jsonStringArr[idx]) == 0 || substr(jsonStringArr[idx], 1, 1) == "]")
                  idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Expected array value")
            }
         
            else if (length(jsonStringArr[idx]) == 0 || substr(jsonStringArr[idx], 1, 1) != "]")
               idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Expected array value or closing bracket")
         }
      }
      
      if (idx <= cnt && (length(jsonStringArr[idx]) == 0 || substr(jsonStringArr[idx], 1, 1) != "]"))
         idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Expected closing bracket")
   
      if (idx <= cnt && length(jsonStringArr[idx]) > 0 && substr(jsonStringArr[idx], 1, 1) == "]")
         sub(/^\][ \t\r\n\f]*/, "", jsonStringArr[idx]) #skip close bracket and skipwhite
   }
      
   return idx
}

function prsJSON_ParseJSONInt(jsonStringArr, cnt, idx, jsonData, prefix,     tk)
{
   if (idx <= cnt)
   {
      sub(/^[ \t\r\n\f]+/, "", jsonStringArr[idx]) #skipwhite

      if (length(jsonStringArr[idx]) > 0)
      {
         tk = substr(jsonStringArr[idx], 1, 1)
         if (tk == "\"" && prefix != "")
            idx = prsJSON_ParseString(jsonStringArr, cnt, idx, jsonData, prefix)
         else if (tk ~ /^[0123456789-]/ && prefix != "") 
            idx = prsJSON_ParseNumber(jsonStringArr, cnt, idx, jsonData, prefix)
         else if (jsonStringArr[idx] ~ /^true/ && prefix != "") 
         {
            prsJSON_SetDataValue(jsonData, prefix, "<<true>>")
            jsonStringArr[idx] = length(jsonStringArr[idx]) <= 4 ? "" : substr(jsonStringArr[idx],5)
         }
         else if (jsonStringArr[idx] ~ /^false/ && prefix != "") 
         {
            prsJSON_SetDataValue(jsonData, prefix, "<<false>>")
            jsonStringArr[idx] = length(jsonStringArr[idx]) <= 5 ? "" : substr(jsonStringArr[idx],6)
         }
         else if (jsonStringArr[idx] ~ /^null/ && prefix != "") 
         {
            prsJSON_SetDataValue(jsonData, prefix, "<<null>>")
            jsonStringArr[idx] = length(jsonStringArr[idx]) <= 4 ? "" : substr(jsonStringArr[idx],5)
         }
         else if (tk == "{") 
            idx = prsJSON_ParseObject(jsonStringArr, cnt, idx, jsonData, prefix)
         else if (tk == "[") 
            idx = prsJSON_ParseArray(jsonStringArr, cnt, idx, jsonData, prefix)
         else
            idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Expected JSON value (2)")

         if (idx <= cnt)
            sub(/^[ \t\r\n\f]+/, "", jsonStringArr[idx]) #skipwhite
      }
   
      if (prefix == "" && idx <= cnt && length(jsonStringArr[idx]) != 0)
         idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Expected end of JSON text")
      else if (prefix == "" && idx+1 <= cnt)
      {
         idx++
         idx = prsJSON_Error(jsonStringArr, cnt, idx, jsonData, "Expected end of JSON text (2)")
      }
         
   }

   return idx
}

#
# JSON Formatting Routines
#

function useJSON_ArrayCount( possibleArray,     a, min, max, cnt, rv)
{
   cnt = 0
   
   for ( a in possibleArray )
   {
      if (possibleArray[a] "" !~ /^[0123456789][0123456789]*$/)
         return -1
         
      if ( cnt == 0 )
      {
         min = possibleArray[a]
         max = possibleArray[a]
      }
      else
      {
         if (min == possibleArray[a] || max == possibleArray[a])
            return -1
            
         if (possibleArray[a] < min)
            min = possibleArray[a]
            
         if (max < possibleArray[a])
            max = possibleArray[a]
      }
      
      cnt++
   }
   
   if (min == 1 && max == cnt)
      return cnt
      
   return -1
}

function useJSON_GetObjectMembers(jsonSchema, prefix)
{
   if (prefix == "") prefix = "<<novalue>>"
   return prefix in jsonSchema ? jsonSchema[prefix] : ""
}

# quick sort array arr
function utlJSON_qsortArray(arr, left, right,   i, last, t)
{
   if (left >= right)   # do nothing if array has less than 2 elements
      return
   i = left + int((right-left+1)*rand())
   t = arr[left]; 
   arr[left] = arr[i]; 
   arr[i] = t
   last = left  # arr[left] is now partition element
   for (i = left+1; i <= right; i++)
   {
      if (arr[i] < arr[left])
      {
         last++
         t = arr[last]; 
         arr[last] = arr[i]; 
         arr[i] = t
      }
   }
   t = arr[left]; 
   arr[left] = arr[last]; 
   arr[last] = t
   utlJSON_qsortArray(arr, left, last-1)
   utlJSON_qsortArray(arr, last+1, right)
}
    
function useJSON_GetSchema(jsonData, jsonSchema,    a, tidx, tv, sv, idx)
{
   split("", jsonSchema)
   for (a in jsonData)
   {
      while (match(a, SUBSEP "[^" SUBSEP "]+$"))
      {
         tidx = substr(a,1,RSTART-1)
         tv = substr(a,RSTART+1)
         sv = (tidx in jsonSchema) ? jsonSchema[tidx] : ""
         # if ( sv != tv && sv !~ "^" tv SUBSEP && sv !~ SUBSEP tv "$" && sv !~ SUBSEP tv SUBSEP )
         # Rephrase this using index so object member names with regex characters work
         if ( sv != tv && index(sv, tv SUBSEP) != 1 && (length(sv) <= length(tv)+1 || substr(sv, length(sv)-length(tv)) != SUBSEP tv) && index(sv, SUBSEP tv SUBSEP) == 0 )
            jsonSchema[tidx] = sv (sv == "" ? "" : SUBSEP)  tv
         a = tidx
      }
      
      tidx = "<<novalue>>"
      tv = a
      sv = (tidx in jsonSchema) ? jsonSchema[tidx] : ""
      if ( sv != tv && sv !~ "^" tv SUBSEP && sv !~ SUBSEP tv "$" && sv !~ SUBSEP tv SUBSEP )
         jsonSchema[tidx] = sv (sv == "" ? "" : SUBSEP)  tv
   }
}

function useJSON_EscapeString(s,     ii, c, t, t2, t3, t4, cs)
{
   cs = "\200\201\202\203\204\205\206\207\210\211\212\213\214\215\216\217\220\221\222\223\224\225\226\227\230\231\232\233\234\235\236\237\240\241\242\243\244\245\246\247\250\251\252\253\254\255\256\257\260\261\262\263\264\265\266\267\270\271\272\273\274\275\276\277\300\301\302\303\304\305\306\307\310\311\312\313\314\315\316\317\320\321\322\323\324\325\326\327\330\331\332\333\334\335\336\337\340\341\342\343\344\345\346\347\350\351\352\353\354\355\356\357\360\361\362\363\364\365\366\367\370\371\372\373\374\375\376\377"
   gsub(/\\/, "\\u005C", s)
   gsub(/"/, "\\\"", s)
   #gsub(/\//, "\\/", s) # required to decode, but not to encode
   gsub(/\b/, "\\b", s)
   gsub(/\f/, "\\f", s)
   gsub(/\n/, "\\n", s)
   gsub(/\r/, "\\r", s)
   gsub(/\t/, "\\t", s)
   
   for ( ii = 1 ; ii <= length(s) ; ii++ )
   {
      t = substr(s,ii,1)
      
      if (t == "\000") # having \000 in list below doesn't work in all awks
      {
         c = 0
         s = (ii > 1 ? substr(s, 1, ii-1) : "") sprintf("\\u%04X", c) (ii==length(s) ? "" : substr(s, ii+1))
         ii += 5
      }
      else
      {
         c = index("\001\002\003\004\005\006\007\010\011\012\013\014\015\016\017\020\021\022\023\024\025\026\027\030\031\032\033\034\035\036\037", t)
         c = c == 0 ? -1 : c
         
         if ( c >= 0 )
         {
            s = (ii > 1 ? substr(s, 1, ii-1) : "") sprintf("\\u%04X", c) (ii==length(s) ? "" : substr(s, ii+1))
            ii += 5
         }
      }

      t = index(cs, t)
      t2 = ii+1 <= length(s) ? index(cs, substr(s,ii+1,1)) : 0
      t3 = ii+2 <= length(s) ? index(cs, substr(s,ii+2,1)) : 0
      t4 = ii+3 <= length(s) ? index(cs, substr(s,ii+3,1)) : 0
        
      if ( c < 0 && t > 64 && t <= 96 && ii+1 <= length(s) && t2 > 0 && t2 <= 64) # two character UTF-8 sequence
      {
         c = (t - 65)*64 + (t2-1)
         s = (ii > 1 ? substr(s, 1, ii-1) : "") sprintf("\\u%04X", c) (ii+1==length(s) ? "" : substr(s, ii+2))
         ii += 5
      }
            
      else if ( c < 0 && t > 96 && t <= 112 && ii+2 <= length(s) && t2 > 0 && t2 <= 64 && t3 > 0 && t3 <= 64) # three character UTF-8 sequence
      {
         c = (t - 97)*4096 + (t2-1)*64 + (t3-1)
         if ( c < 65536 )
         {
            s = (ii > 1 ? substr(s, 1, ii-1) : "") sprintf("\\u%04X", c) (ii+2==length(s) ? "" : substr(s, ii+3))
            ii += 5
         }
         else
         {
            # encode in JSON-style with two \u#### UTF-16 surrogates
            # printf("1: %08X\n", c)
            s = (ii > 1 ? substr(s, 1, ii-1) : "") sprintf("\\u%04X\\u%04X", (c/1024)%1024 + 55296, c%1024 + 56320) (ii+3==length(s) ? "" : substr(s, ii+4))
            ii += 11
         }
      }
            
      # four character UTF-8 sequence, encode in JSON-style with two \u#### UTF-16 surrogates
      else if ( c < 0 && t > 112 && t <= 120 && ii+3 <= length(s) && t2 > 0 && t2 <= 64 && t3 > 0 && t3 <= 64 && t4 > 0 && t4 <=  64) 
      {
         c = (t - 113)*262144 + (t2-1)*4096 + (t3-1)*64 + (t4-1)
         # printf("2: %08X, %d, %d, %d, %d\n", c, t, t2, t3, t4)
         # printf("\\u%04X\\u%04X\n", (c/1024)%1024 + 55296, c%1024 + 56320)
         c -= 65536
         s = (ii > 1 ? substr(s, 1, ii-1) : "") sprintf("\\u%04X\\u%04X", (c/1024)%1024 + 55296, c%1024 + 56320) (ii+3==length(s) ? "" : substr(s, ii+4))
         ii += 11
      }
   }
         
   return "\"" s "\""
}

function useJSON_GetDataValue(jsonData, prefix)
{
   return prefix in jsonData ? jsonData[prefix] : "<<novalue>>"
}

function useJSON_PrettyFormat(s, pretty)
{
   if (s == "" || pretty <= 0) return s

   # don't sprintf the whole thing, some awks have short buffers for sprintf
   return sprintf("%*.*s", (pretty-1)*3, (pretty-1)*3, "") s (s == "}" || s == "]" ? "" : "\n")
}

function useJSON_FormatInt(jsonData, jsonSchema, prefix, pretty,     allLines, member, memberArr, memberList, arrCount, a, ii)
{
   memberList = useJSON_GetObjectMembers(jsonSchema, prefix)
   
   if ( memberList == "" )
   {
      a = useJSON_GetDataValue(jsonData, prefix)
      if ( a == "<<true>>" ) return "true"
      if ( a == "<<false>>" ) return "false"
      if ( a == "<<null>>" ) return "null"
      if ( a == "<<novalue>>" ) return "" # <<novalue>> is a help for dealing with empty arrays and objects

      # if it looks like a number, encode it as such.  Can't tell a string from a number.
      if (a "" ~ /^(\-?)(0|[123456789][0123456789]*)(\.[0123456789]+)?([eE][+-]?[0123456789]+)?$/)
         return a
         
      return useJSON_EscapeString(a)
   }
   
   split(memberList, memberArr, SUBSEP)
   arrCount = useJSON_ArrayCount( memberArr )

   if ( arrCount >= 0 )
   {
      allLines = "[" (pretty == 0 ? "" : "\n")
      
      for ( ii = 1 ; ii <= arrCount ; ii++ )
         allLines = allLines useJSON_PrettyFormat(useJSON_FormatInt( jsonData, jsonSchema, prefix (prefix == "" ? "" : SUBSEP) ii, (pretty != 0 ? pretty+1 : 0)) (ii < arrCount ? "," : ""), pretty != 0 ? pretty+1 : 0)
      allLines = allLines useJSON_PrettyFormat("]", pretty)
      
      return allLines
   }
   
   allLines = "{" (pretty == 0 ? "" : "\n")
      
   ii = 0

   arrCount = 0
   for (a in memberArr)
      arrCount++
      
   utlJSON_qsortArray(memberArr, 1, arrCount)
   
   for ( ii = 1 ; ii <= arrCount ; ii++ )
      allLines = allLines useJSON_PrettyFormat(useJSON_EscapeString(memberArr[ii]) (pretty == 0 ? ":" : " : ") useJSON_FormatInt(jsonData, jsonSchema, prefix (prefix == "" ? "" : SUBSEP) memberArr[ii], (pretty != 0 ? pretty+1 : 0)) (ii < arrCount ? "," : ""), pretty != 0 ? pretty+1 : 0)
      
   allLines = allLines useJSON_PrettyFormat("}", pretty)
   
   return allLines
}

#
# Entry Points
#

#
# ParseJSON : Parse JSON text into an awk array
#
#    jsonString : JSON text
#    jsonData : array of parsed JSON data
#
#    returns : N/A
#
function ParseJSON(jsonString, jsonData,      jsonStringArr, cnt)
{
   # newlines split differently in some awks, replace them with formfeeds (also white space)
   # if (split("1\n2\n3", jsonData, ",") == 3) # is this an awk that splits newlines differently?
   gsub(/\n/, "\f", jsonString) # always replace literal newlines - allows compatibility when testing

   split("", jsonData) # clear the array jsonData
   cnt = split(jsonString, jsonStringArr, ",")
   prsJSON_ParseJSONInt(jsonStringArr, cnt, 1, jsonData, "")
}

#
# FormatJSON : Format parsed JSON data back into JSON text
#
#    jsonData : array of parsed JSON data
#    pretty : 0 = compact format, non-zero = pretty format
#
#    returns : string with JSON text
#
function FormatJSON(jsonData, pretty,    jsonSchema)
{
   useJSON_GetSchema(jsonData, jsonSchema)
   return useJSON_FormatInt(jsonData, jsonSchema, "", pretty ? 1 : 0)
}

#
# JSONArrayLength : Find number of members in a JSON array
#
#    jsonData : array of parsed JSON data
#    prefix : array name
#
#    returns : number of entries in the array
#
function JSONArrayLength(jsonData, prefix,     a, cnt, tv)
{
   cnt = -1
   
   for (a in jsonData)
   {
      if (prefix == "" || index(a, prefix) == 1)
      {
         tv = substr(a, prefix == "" ? 1 : (1+length(prefix)+1))
         if ( index(tv, SUBSEP) )
            tv = substr(tv, 1, index(tv, SUBSEP)-1)
         tv = tv + 0
         if ( tv > cnt )
            cnt = tv
      }
   }

   return cnt
}

#
# JSONUnescapeString : turn a JSON-escaped string into UTF-8
#
#    jsonString : the escaped JSON string to convert
#
#    returns : the string in UTF-8
#
function JSONUnescapeString(jsonString)
{
   return prsJSON_UnescapeString(jsonString)
}

#
# JSONIsTrue : return non-zero if the value is the true value
#
#    jsonValue : the value to test
#
#    returns : true or false
#
function JSONIsTrue(jsonValue)
{
   return jsonValue == "<<true>>";
}

#
# JSONIsFalse : return non-zero if the value is the false value
#
#    jsonValue : the value to test
#
#    returns : true or false
#
function JSONIsFalse(jsonValue)
{
   return jsonValue == "<<false>>";
}

#
# JSONIsNull : return non-zero if the value is the null value
#
#    jsonValue : the value to test
#
#    returns : true or false
#
function JSONIsNull(jsonValue)
{
   return jsonValue == "<<null>>";
}

#
# JSONObjectMembers : get the set of members of an object
#
#    jsonData : array of parsed JSON data
#    prefix : object name
#    memberArr : [out] an array of the names of the object members, if the target was an object or an array
#
#    returns : If the target was actually an array rather than an object, the number of elements in the array
#              Else, zero if the target was an object or a value
#
function JSONObjectMembers(jsonData, prefix, memberArr,     jsonSchema, memberList, rv, a)
{
   useJSON_GetSchema(jsonData, jsonSchema)
   memberList = useJSON_GetObjectMembers(jsonSchema, prefix)

   if ( memberList == "" )
   {
      split("", memberArr)
      return 0
   }
      
   split(memberList, memberArr, SUBSEP)
   rv = useJSON_ArrayCount( memberArr )
   if ( rv == -1 ) # not an array, sort the object member names
   {
      rv = 0
      for (a in memberArr)
         rv++
      
      utlJSON_qsortArray(memberArr, 1, rv)
      rv = 0
   }
   return rv
}
