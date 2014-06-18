<?php
class rssMsgView extends rssSkinList 
{
	
	public $currentImage = 'http://nicecomputer.ru/uploads/posts/2013-08/1377540170_warning.png'; #'http://static.mobile9.com/img6/cube_error.png';
	public $currentMsg = 'Ошибка вывода сообщения';
	
function showDisplay()
{
?>
<mediaDisplay name="onePartView"
    
	backgroundColor="120:120:120"
    showDefaultInfo="no"
	viewAreaXPC="25"
    viewAreaYPC="25"
    viewAreaWidthPC="50"
    viewAreaHeightPC="30"
	sideLeftWidthPC="0"
    sideRightWidthPC="0"
	
	itemXPC="35"
    itemYPC="73"
    itemWidthPC="30"
    itemHeightPC="15"
	itemImageXPC="35"
    itemImageYPC="73"
    itemImageWidthPC="0"
    itemImageHeightPC="0"
    itemPerPage="1"
	
    >
    
	<text offsetXPC="0.3" offsetYPC="1" widthPC="99.7" heightPC="98.5"
        backgroundColor="41:41:41" cornerRounding="15" />

    <text offsetXPC="3" offsetYPC="5" widthPC="90" heightPC="15"
        backgroundColor="41:41:41"
        foregroundColor="130:130:130"
        align="center" fontSize="14">Сообщение</text>

    <image offsetXPC="3.5" offsetYPC="13" widthPC="24.5"
        heightPC="75"><?= $this->currentImage ?></image>

    <text offsetXPC="30" offsetYPC="30" widthPC="65" heightPC="35"
        backgroundColor="41:41:41"
        foregroundColor="200:200:200"
		fontSize="15" lines="3"><?= $this->currentMsg ?></text>

    <text offsetXPC="35" offsetYPC="73" widthPC="30" heightPC="15"
        backgroundColor="102:102:102"
        foregroundColor="255:255:255"
        align="center" fontSize="14" cornerRounding="5">OK</text>

    <itemDisplay>
        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100"
            backgroundColor="10:10:10"
            foregroundColor="255:255:255"
            align="center" fontSize="14" cornerRounding="5">OK</text>
    </itemDisplay>

</mediaDisplay>
<?php
}


public function showChannel()
{
?>
<channel>
    <item>
        <title>Ok</title>
        <onClick>
            <script>postMessage("return"); null; </script>
        </onClick>
    </item>
</channel>
<?php
}
}
?>