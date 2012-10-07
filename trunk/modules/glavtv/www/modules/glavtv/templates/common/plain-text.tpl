<?
require_once 'interfaces/message.inc';
require_once 'interfaces/channel.inc';

if ($object instanceof Message) {
    $text = $object->getText();
} else if ($object instanceof Channel) {
    $text = $object->get(Channel::LINK);
} else {
    $text = $object;
}

print str_replace('&', '&amp;', $text);
?>
