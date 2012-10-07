<?
$first = true;
foreach ($object->getItems() as $item) {
    if ($first) {
        $first = false;
    } else {
        print "--- separator ---\n";
    }
    print preg_replace('/./uSs', "$0\n", $item->get(Item::TITLE));
}
?>
