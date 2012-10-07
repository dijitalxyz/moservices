{* 
	This subtemplate calculates image height from given width or width from given height for img and thumbnail.
	Input: any of smarty variables 
				 imgWidth, 
				 imgHeight, 
				 thumbWidth , 
				 thumbHeight
		   RSS variable 
				 itemIndex
	Used config: [_SERVICE_NAME_ Templates]/img_ratio
				 [_SERVICE_NAME_ Templates]/thumbnail_ratio
				 [templates]/thumbnail_ratio
	Output: RSS variables 
				 calculatedImgWidth ,
				 calculatedImgHeight, 
				 calculatedThumbWidth, 
				 calculatedThumbHeight
*}
{$screen_ratio = $config->get(screen_ratio,templates)}

{$smary_sect   		= $config->get(name,Service)|cat:" Templates"}
{$cfg_img_ratio		= $config->get(img_ratio,$smary_sect)}
{$cfg_thumb_ratio	= $config->get(thumbnail_ratio,$smary_sect)}

<script>
	imgRatio = getItemInfo(getQueryItemIndex(),"img_ratio");
	/*first fallback*/
	if(imgRatio == 0 || imgRatio == "") {
		imgRatio = {$cfg_img_ratio};
	}
	/*second fallback*/
	if(imgRatio == 0 || imgRatio == "") {
		imgRatio = 1;
	}

	thumbRatio = getItemInfo(getQueryItemIndex(),"thumbnail_ratio");
	/*first fallback*/
	if(thumbRatio == 0 || thumbRatio == "") {
		thumbRatio = {$cfg_thumb_ratio};
	}
	/*second fallback*/
	if(thumbRatio == 0 || thumbRatio == "") {
		thumbRatio = 1;
	}
	{if $screen_ratio == 0}
		{$screen_ratio = 1}
	{/if} 
	calculatedImgWidth  	= {$imgHeight   / $screen_ratio} * imgRatio;
	calculatedImgHeight 	= {$imgWidth    * $screen_ratio} / imgRatio;
	calculatedThumbWidth 	= {$thumbHeight / $screen_ratio} * thumbRatio;
	calculatedThumbHeight 	= {$thumbWidth  * $screen_ratio} / thumbRatio;
	print("calculatedImgWidth = ({$imgHeight}  * {$screen_ratio} *", imgRatio,") = ", calculatedImgWidth);
	print("calculatedImgHeight = ({$imgWidth}  * {$screen_ratio} *", imgRatio,") = ", calculatedImgHeight);
	print("calculatedThumbWidth = ({$thumbHeight}  * {$screen_ratio} *", thumbRatio,") = ", calculatedThumbWidth);
	print("calculatedThumbHeight = ({$thumbWidth}  * {$screen_ratio} *", thumbRatio,") = ", calculatedThumbHeight);	
</script>