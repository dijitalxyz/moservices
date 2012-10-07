<?
$params = array('i' => Item::INFO, 't' => Item::TIME, 'p' => 'percent', 'd' => Item::DESCRIPTION);
print "<data>\n";
foreach ($object->getItems() as $item) {
    print "<c>";
    foreach ($params as $tag => $param) {
        print "<$tag>" . $item->get($param) . "</$tag>";
    }
    print "</c>\n";
}
print "</data>";
?>
