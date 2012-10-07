<?php

exec( "$mos/etc/init/S14spindown down" );

// ------------------------------------
// send RSS

header( "Content-type: text/plain" );
echo "<?xml version=\"1.0\" encoding=\"UTF8\" ?>\n";

?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<onEnter>
  postMessage("return");
</onEnter>
</rss>
<?php

exit;

?>
