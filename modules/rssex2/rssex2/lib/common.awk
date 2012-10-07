function unescapeXML( text ) {
  gsub( "&apos;", "'",  text );
  gsub( "&quot;", "\"", text );
  gsub( "&gt;",   ">",  text );
  gsub( "&lt;",   "<",  text );
  gsub( "&amp;",  "\\&",  text );
  return text
}
function escapeXML( text )
{
  gsub( "\\&",  "&amp;",  text );
  gsub( "'",  "&apos;", text );
  gsub( "\"", "&quot;", text );
  gsub( ">",  "&gt;",   text );
  gsub( "<",  "&lt;",   text );
  return text;
}

