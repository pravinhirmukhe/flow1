<?php  
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

	function nggallery_admin_options()  {
	
	global $wpdb, $ngg, $nggRewrite;

	$old_state = $ngg->options['usePermalinks'];
	
	// same as $_SERVER['REQUEST_URI'], but should work under IIS 6.0
	$filepath    = admin_url() . 'admin.php?page='.$_GET['page'];

	if ( isset($_POST['updateoption']) ) {	
		check_admin_referer('ngg_settings');
		// get the hidden option fields, taken from WP core
		if ( $_POST['page_options'] )	
			$options = explode(',', stripslashes($_POST['page_options']));
		if ($options) {
			foreach ($options as $option) {
				$option = trim($option);
				$value = trim($_POST[$option]);
		//		$value = sanitize_option($option, $value); // This does stripslashes on those that need it
				$ngg->options[$option] = $value;
			}
		// the path should always end with a slash	
		$ngg->options['gallerypath']    = trailingslashit($ngg->options['gallerypath']);
		$ngg->options['imageMagickDir'] = trailingslashit($ngg->options['imageMagickDir']);
		// the custom sortorder must be ascending
		$ngg->options['galSortDir'] = ($ngg->options['galSort'] == 'sortorder') ? 'ASC' : $ngg->options['galSortDir'];
		}
		// Save options
		update_option('ngg_options', $ngg->options);

		// Flush ReWrite rules
		if ( $old_state != $ngg->options['usePermalinks'] )
			$nggRewrite->flush();
		
	 	nggGallery::show_message(__('Update Successfully','nggallery'));
	}		
	
	if ( isset($_POST['clearcache']) ) {
		
		$path = WINABSPATH . $ngg->options['gallerypath'] . "cache/";
		
		if (is_dir($path))
	    	if ($handle = opendir($path)) {
				while (false !== ($file = readdir($handle))) {
			    	if ($file != '.' && $file != '..') {
			          @unlink($path . '/' . $file);
	          		}
	        	}
	      		closedir($handle);
			}

		nggGallery::show_message(__('Cache cleared','nggallery'));
	}
	// message windows
	if(!empty($messagetext)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$messagetext.'</p></div>'; }
	
	?>
	<script type="text/javascript">
		jQuery(function() {
			jQuery('#slider > ul').tabs({ fxFade: true, fxSpeed: 'fast' });	
		});
	
		function insertcode(value) {
			var effectcode;
			switch (value) {
			  case "none":
			    effectcode = "";
			    jQuery('#tbImage').hide("slow");
			    break;
			  case "thickbox":
			    effectcode = 'class="thickbox" rel="%GALLERY_NAME%"';
			    jQuery('#tbImage').show("slow");
			    break;
			  case "lightbox":
			    effectcode = 'rel="lightbox[%GALLERY_NAME%]"';
			    jQuery('#tbImage').hide("slow");
			    break;
			  case "highslide":
			    effectcode = 'class="highslide" onclick="return hs.expand(this, { slideshowGroup: %GALLERY_NAME% })"';
			    jQuery('#tbImage').hide("slow");
			    break;
			  case "shutter":
			    effectcode = 'class="shutterset_%GALLERY_NAME%"';
			    jQuery('#tbImage').hide("slow");
			    break;
			  default:
			    break;
			}
			jQuery("#thumbCode").val(effectcode);
		};
		
		function setcolor(fileid,color) {
			jQuery(fileid).css("background", color );
		};
	</script>
	
	<div id="slider" class="wrap">
	
		<ul id="tabs">
			<li><a href="#generaloptions"><?php _e('General Options', 'nggallery') ;?></a></li>
			<li><a href="#thumbnails"><?php _e('Thumbnails', 'nggallery') ;?></a></li>
			<li><a href="#images"><?php _e('Images', 'nggallery') ;?></a></li>
			<li><a href="#gallery"><?php echo __ngettext( 'Gallery', 'Galleries', 1, 'nggallery' ) ;?></a></li>
			<li><a href="#effects"><?php _e('Effects', 'nggallery') ;?></a></li>
			<li><a href="#watermark"><?php _e('Watermark', 'nggallery') ;?></a></li>
			<li><a href="#slideshow"><?php _e('Slideshow', 'nggallery') ;?></a></li>
		</ul>

		<!-- General Options -->

		<div id="generaloptions">
			<h2><?php _e('General Options','nggallery'); ?></h2>
			<form name="generaloptions" method="post">
			<?php wp_nonce_field('ngg_settings') ?>
			<input type="hidden" name="page_options" value="gallerypath,deleteImg,useMediaRSS,usePicLens,usePermalinks,graphicLibrary,imageMagickDir,activateTags,appendType,maxImages" />
				<table class="form-table ngg-options">
					<tr valign="top">
						<th align="left"><?php _e('Gallery path','nggallery') ?></th>
						<td><input <?php if (IS_WPMU) echo 'readonly = "readonly"'; ?> type="text" size="35" name="gallerypath" value="<?php echo $ngg->options['gallerypath']; ?>" />
						<span class="setting-description"><?php _e('This is the default path for all galleries','nggallery') ?></span></td>
					</tr>
					<tr valign="top">
						<th align="left"><?php _e('Delete image files','nggallery') ?></th>
						<td><input <?php if (IS_WPMU) echo 'readonly = "readonly"'; ?> type="checkbox" name="deleteImg" value="1" <?php checked('1', $ngg->options['deleteImg']); ?> />
						<?php _e('Delete files, when removing a gallery in the database','nggallery') ?></td>
					</tr>
					<tr valign="top">
						<th align="left"><?php _e('Activate permalinks','nggallery') ?></th>
						<td><input type="checkbox" name="usePermalinks" value="1" <?php checked('1', $ngg->options['usePermalinks']); ?> />
						<?php _e('When you activate this option, you need to update your permalink structure one time.','nggallery') ?></td>
					</tr>
					<tr>
						<th valign="top"><?php _e('Select graphic library','nggallery') ?>:</th>
						<td><label><input name="graphicLibrary" type="radio" value="gd" <?php checked('gd', $ngg->options['graphicLibrary']); ?> /> <?php _e('GD Library', 'nggallery') ;?></label><br />
						<label><input name="graphicLibrary" type="radio" value="im" <?php checked('im', $ngg->options['graphicLibrary']); ?> /> <?php _e('ImageMagick (Experimental). Path to the library :', 'nggallery') ;?>&nbsp;
						<input <?php if (IS_WPMU) echo 'readonly = "readonly"'; ?> type="text" size="35" name="imageMagickDir" value="<?php echo $ngg->options['imageMagickDir']; ?>" /></label>
						</td>
					</tr>
					<tr>
						<th align="left"><?php _e('Activate Media RSS feed','nggallery') ?></th>
						<td><input type="checkbox" name="useMediaRSS" value="1" <?php checked('1', $ngg->options['useMediaRSS']); ?> />
						<span class="setting-description"><?php _e('A RSS feed will be added to you blog header. Usefull for CoolIris/PicLens','nggallery') ?></span></td>
					</tr>
					<tr>
						<th align="left"><?php _e('Activate PicLens/CoolIris support','nggallery') ?> (<a href="http://www.cooliris.com">CoolIris</a>)</th>
						<td><input type="checkbox" name="usePicLens" value="1" <?php checked('1', $ngg->options['usePicLens']); ?> />
						<span class="setting-description"><?php _e('When you activate this option, some javascript is added to your site footer. Make sure that wp_footer is called in your theme.','nggallery') ?></span></td>
					</tr>
				</table>
			<h3><?php _e('Tags / Categories','nggallery') ?></h3>
				<table class="form-table ngg-options">
					<tr>
						<th valign="top"><?php _e('Activate related images','nggallery') ?>:</th>
						<td><input name="activateTags" type="checkbox" value="1" <?php checked('1', $ngg->options['activateTags']); ?> />
						<?php _e('This option will append related images to every post','nggallery') ?>
						</td>
					</tr>
					<tr>
						<th valign="top"><?php _e('Match with','nggallery') ?>:</th>
						<td><label><input name="appendType" type="radio" value="category" <?php checked('category', $ngg->options['appendType']); ?> /> <?php _e('Categories', 'nggallery') ;?></label><br />
						<label><input name="appendType" type="radio" value="tags" <?php checked('tags', $ngg->options['appendType']); ?> /> <?php _e('Tags', 'nggallery') ;?></label>
						</td>
					</tr>
					<tr>
						<th valign="top"><?php _e('Max. number of images','nggallery') ?>:</th>
						<td><input type="text" name="maxImages" value="<?php echo $ngg->options['maxImages'] ?>" size="3" maxlength="3" />
						<span class="setting-description"><?php _e('0 will show all images','nggallery') ?></span>
						</td>
					</tr>
				</table> 				
			<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes') ;?>"/></div>
			</form>	
		</div>	
		
		<!-- Thumbnail settings -->
		
		<div id="thumbnails">
			<h2><?php _e('Thumbnail settings','nggallery'); ?></h2>
			<form name="thumbnailsettings" method="POST" action="<?php echo $filepath.'#thumbnails'; ?>" >
			<?php wp_nonce_field('ngg_settings') ?>
			<input type="hidden" name="page_options" value="thumbwidth,thumbheight,thumbfix,thumbcrop,thumbquality" />
				<p><?php _e('Please note : If you change the settings, you need to recreate the thumbnails under -> Manage Gallery .', 'nggallery') ?></p>
				<table class="form-table ngg-options">
					<tr valign="top">
						<th align="left"><?php _e('Width x height (in pixel)','nggallery') ?></th>
						<td><input type="text" size="4" maxlength="4" name="thumbwidth" value="<?php echo $ngg->options['thumbwidth']; ?>" /> x <input type="text" size="4" maxlength="4" name="thumbheight" value="<?php echo $ngg->options['thumbheight']; ?>" />
						<span class="setting-description"><?php _e('These values are maximum values ','nggallery') ?></span></td>
					</tr>
					<tr valign="top">
						<th align="left"><?php _e('Set fix dimension','nggallery') ?></th>
						<td><input type="checkbox" name="thumbfix" value="1" <?php checked('1', $ngg->options['thumbfix']); ?> />
						<?php _e('Ignore the aspect ratio, no portrait thumbnails','nggallery') ?></td>
					</tr>
					<tr valign="top">
						<th align="left"><?php _e('Crop square thumbnail from image','nggallery') ?></th>
						<td><input type="checkbox" name="thumbcrop" value="1" <?php checked('1', $ngg->options['thumbcrop']); ?> />
						<?php _e('Create square thumbnails, use only the width setting :','nggallery') ?> <?php echo $ngg->options['thumbwidth']; ?> x <?php echo $ngg->options['thumbwidth']; ?></td>
					</tr>
					<tr valign="top">
						<th align="left"><?php _e('Thumbnail quality','nggallery') ?></th>
						<td><input type="text" size="3" maxlength="3" name="thumbquality" value="<?php echo $ngg->options['thumbquality']; ?>" /> %</td>
					</tr>
				</table>
			<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes') ;?>"/></div>
			</form>	
		</div>
		
		<!-- Image settings -->
		
		<div id="images">
			<h2><?php _e('Image settings','nggallery'); ?></h2>
			<form name="imagesettings" method="POST" action="<?php echo $filepath.'#images'; ?>" >
			<?php wp_nonce_field('ngg_settings') ?>
			<input type="hidden" name="page_options" value="imgResize,imgWidth,imgHeight,imgQuality,imgCacheSinglePic" />
				<table class="form-table ngg-options">
					<tr valign="top">
						<th scope="row"><label for="fixratio"><?php _e('Resize Images','nggallery') ?></label></th>
						<!--TODO: checkbox fixratio can be used later -->
						<td><input type="hidden" name="imgResize" value="1" <?php checked('1', $ngg->options['imgResize']); ?> /> </td>
						<td><input type="text" size="5" name="imgWidth" value="<?php echo $ngg->options['imgWidth']; ?>" /> x <input type="text" size="5" name="imgHeight" value="<?php echo $ngg->options['imgHeight']; ?>" />
						<span class="setting-description"><?php _e('Width x height (in pixel). NextGEN Gallery will keep ratio size','nggallery') ?></span></td>
					</tr>
					<tr valign="top">
						<th align="left"><?php _e('Image quality','nggallery') ?></th>
						<td></td>
						<td><input type="text" size="3" maxlength="3" name="imgQuality" value="<?php echo $ngg->options['imgQuality']; ?>" /> %</td>
					</tr>
				</table>
				<h3><?php _e('Single picture','nggallery') ?></h3>
				<table class="form-table ngg-options">
					<tr valign="top">
						<th align="left"><?php _e('Cache single pictures','nggallery') ?></th>
						<td></td>
						<td><input <?php if (IS_WPMU) echo 'readonly = "readonly"'; ?> type="checkbox" name="imgCacheSinglePic" value="1" <?php checked('1', $ngg->options['imgCacheSinglePic']); ?> />
						<span class="setting-description"><?php _e('Creates a file for each singlepic settings. Reduce the CPU load','nggallery') ?></span></td>
					</tr>
					<tr valign="top">
						<th align="left"><?php _e('Clear cache folder','nggallery') ?></th>
						<td></td>
						<td><input type="submit" name="clearcache" class="button-secondary"  value="<?php _e('Proceed now','nggallery') ;?> &raquo;"/></td>
					</tr>
				</table>
			<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes') ;?>"/></div>
			</form>	
		</div>
		
		<!-- Gallery settings -->
		
		<div id="gallery">
			<h2><?php _e('Gallery settings','nggallery'); ?></h2>
			<form name="galleryform" method="POST" action="<?php echo $filepath.'#gallery'; ?>" >
			<?php wp_nonce_field('ngg_settings') ?>
			<input type="hidden" name="page_options" value="galNoPages,galImages,galShowSlide,galTextSlide,galTextGallery,galShowOrder,galImgBrowser,galSort,galSortDir" />
				<table class="form-table ngg-options">
					<tr>
						<th valign="top"><?php _e('Deactivate gallery page link','nggallery') ?>:</th>
						<td><input name="galNoPages" type="checkbox" value="1" <?php checked('1', $ngg->options['galNoPages']); ?> />
						<?php _e('The album will not link to a gallery subpage. The gallery is shown on the same page.','nggallery') ?>
						</td>
					</tr>
					<tr>
						<th valign="top"><?php _e('Number of images per page','nggallery') ?>:</th>
						<td><input type="text" name="galImages" value="<?php echo $ngg->options['galImages'] ?>" size="3" maxlength="3" />
						<span class="setting-description"><?php _e('0 will disable pagination, all images on one page','nggallery') ?></span>
						</td>
					</tr>
					<tr>
						<th valign="top"><?php _e('Integrate slideshow','nggallery') ?>:</th>
						<td><input name="galShowSlide" type="checkbox" value="1" <?php checked('1', $ngg->options['galShowSlide']); ?> />
							<input type="text" name="galTextSlide" value="<?php echo $ngg->options['galTextSlide'] ?>" size="20" />
							<input type="text" name="galTextGallery" value="<?php echo $ngg->options['galTextGallery'] ?>" size="20" />
						</td>
					</tr>
					<tr>
						<th valign="top"><?php _e('Show first','nggallery') ?>:</th>
						<td><label><input name="galShowOrder" type="radio" value="gallery" <?php checked('gallery', $ngg->options['galShowOrder']); ?> /> <?php _e('Thumbnails', 'nggallery') ;?></label><br />
						<label><input name="galShowOrder" type="radio" value="slide" <?php checked('slide', $ngg->options['galShowOrder']); ?> /> <?php _e('Slideshow', 'nggallery') ;?></label>
						</td>
					</tr>
					<tr>
						<th valign="top"><?php _e('Show ImageBrowser','nggallery') ?>:</th>
						<td><input name="galImgBrowser" type="checkbox" value="1" <?php checked('1', $ngg->options['galImgBrowser']); ?> />
						<?php _e('The gallery will open the ImageBrowser instead the effect.','nggallery') ?>
						</td>
					</tr>
				</table>
			<h3><?php _e('Sort options','nggallery') ?></h3>
				<table class="form-table ngg-options">
					<tr>
						<th valign="top"><?php _e('Sort thumbnails','nggallery') ?>:</th>
						<td>
						<label><input name="galSort" type="radio" value="sortorder" <?php checked('sortorder', $ngg->options['galSort']); ?> /> <?php _e('Custom order', 'nggallery') ;?></label><br />
						<label><input name="galSort" type="radio" value="pid" <?php checked('pid', $ngg->options['galSort']); ?> /> <?php _e('Image ID', 'nggallery') ;?></label><br />
						<label><input name="galSort" type="radio" value="filename" <?php checked('filename', $ngg->options['galSort']); ?> /> <?php _e('File name', 'nggallery') ;?></label><br />
						<label><input name="galSort" type="radio" value="alttext" <?php checked('alttext', $ngg->options['galSort']); ?> /> <?php _e('Alt / Title text', 'nggallery') ;?></label><br />
						<label><input name="galSort" type="radio" value="imagedate" <?php checked('imagedate', $ngg->options['galSort']); ?> /> <?php _e('Date / Time', 'nggallery') ;?></label>
						</td>
					</tr>
					<tr>
						<th valign="top"><?php _e('Sort direction','nggallery') ?>:</th>
						<td><label><input name="galSortDir" type="radio" value="ASC" <?php checked('ASC', $ngg->options['galSortDir']); ?> /> <?php _e('Ascending', 'nggallery') ;?></label><br />
						<label><input name="galSortDir" type="radio" value="DESC" <?php checked('DESC', $ngg->options['galSortDir']); ?> /> <?php _e('Descending', 'nggallery') ;?></label>
						</td>
					</tr>
				</table>
			<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes') ;?>"/></div>
			</form>	
		</div>
		
		<!-- Effects settings -->
		
		<div id="effects">
			<h2><?php _e('Effects','nggallery'); ?></h2>
			<form name="effectsform" method="POST" action="<?php echo $filepath.'#effects'; ?>" >
			<?php wp_nonce_field('ngg_settings') ?>
			<input type="hidden" name="page_options" value="thumbEffect,thumbCode" />
			<p><?php _e('Here you can select the thumbnail effect, NextGEN Gallery will integrate the required HTML code in the images. Please note that only the Thickbox effect will automatic added to your theme.','nggallery'); ?>
			<?php _e('With the placeholder','nggallery'); ?><strong> %GALLERY_NAME% </strong> <?php _e('you can activate a navigation through the images (depend on the effect). Change the code line only , when you use a different thumbnail effect or you know what you do.','nggallery'); ?></p>
				<table class="form-table ngg-options">
					<tr valign="top">
						<th><?php _e('JavaScript Thumbnail effect','nggallery') ?>:</th>
						<td>
						<select size="1" id="thumbEffect" name="thumbEffect" onchange="insertcode(this.value)">
							<option value="none" <?php selected('none', $ngg->options['thumbEffect']); ?> ><?php _e('None', 'nggallery') ;?></option>
							<option value="thickbox" <?php selected('thickbox', $ngg->options['thumbEffect']); ?> ><?php _e('Thickbox', 'nggallery') ;?></option>
							<option value="lightbox" <?php selected('lightbox', $ngg->options['thumbEffect']); ?> ><?php _e('Lightbox', 'nggallery') ;?></option>
							<option value="highslide" <?php selected('highslide', $ngg->options['thumbEffect']); ?> ><?php _e('Highslide', 'nggallery') ;?></option>
							<option value="shutter" <?php selected('shutter', $ngg->options['thumbEffect']); ?> ><?php _e('Shutter', 'nggallery') ;?></option>
							<option value="custom" <?php selected('custom', $ngg->options['thumbEffect']); ?> ><?php _e('Custom', 'nggallery') ;?></option>
						</select>
						</td>
					</tr>
					<tr valign="top">
						<th><?php _e('Link Code line','nggallery') ?> :</th>
						<td><textarea id="thumbCode" name="thumbCode" cols="50" rows="5"><?php echo htmlspecialchars(stripslashes($ngg->options['thumbCode'])); ?></textarea></td>
					</tr>
				</table>
			<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes') ;?>"/></div>
			</form>	
		</div>
		
		<!-- Watermark settings -->
		
		<?php
		$imageID = $wpdb->get_var("SELECT MIN(pid) FROM $wpdb->nggpictures");
		$imageID = $wpdb->get_row("SELECT * FROM $wpdb->nggpictures WHERE pid = '$imageID'");	
		if ($imageID) $imageURL = '<img width="75%" src="'.NGGALLERY_URLPATH.'nggshow.php?pid='.$imageID->pid.'&amp;mode=watermark&amp;width=320&amp;height=240" alt="'.$imageID->alttext.'" title="'.$imageID->alttext.'" />';

		?>
		<div id="watermark">
			<h2><?php _e('Watermark','nggallery'); ?></h2>
			<p><?php _e('Please note : You can only activate the watermark under -> Manage Gallery . This action cannot be undone.', 'nggallery') ?></p>
			<form name="watermarkform" method="POST" action="<?php echo $filepath.'#watermark'; ?>" >
			<?php wp_nonce_field('ngg_settings') ?>
			<input type="hidden" name="page_options" value="wmPos,wmXpos,wmYpos,wmType,wmPath,wmFont,wmSize,wmColor,wmText,wmOpaque" />
			<div id="wm-preview">
				<h3><?php _e('Preview','nggallery') ?></h3>
				<p style="text-align:center;"><?php echo $imageURL; ?></p>
				<h3><?php _e('Position','nggallery') ?></h3>
				<div>
				    <table id="wm-position">
					<tr>
						<td valign="top">
							<strong><?php _e('Position','nggallery') ?></strong>
							<table border="1">
								<tr>
									<td><input type="radio" name="wmPos" value="topLeft" <?php checked('topLeft', $ngg->options['wmPos']); ?> /></td>
									<td><input type="radio" name="wmPos" value="topCenter" <?php checked('topCenter', $ngg->options['wmPos']); ?> /></td>
									<td><input type="radio" name="wmPos" value="topRight" <?php checked('topRight', $ngg->options['wmPos']); ?> /></td>
								</tr>
								<tr>
									<td><input type="radio" name="wmPos" value="midLeft" <?php checked('midLeft', $ngg->options['wmPos']); ?> /></td>
									<td><input type="radio" name="wmPos" value="midCenter" <?php checked('midCenter', $ngg->options['wmPos']); ?> /></td>
									<td><input type="radio" name="wmPos" value="midRight" <?php checked('midRight', $ngg->options['wmPos']); ?> /></td>
								</tr>
								<tr>
									<td><input type="radio" name="wmPos" value="botLeft" <?php checked('botLeft', $ngg->options['wmPos']); ?> /></td>
									<td><input type="radio" name="wmPos" value="botCenter" <?php checked('botCenter', $ngg->options['wmPos']); ?> /></td>
									<td><input type="radio" name="wmPos" value="botRight" <?php checked('botRight', $ngg->options['wmPos']); ?> /></td>
								</tr>
							</table>
						</td>
						<td valign="top">
							<strong><?php _e('Offset','nggallery') ?></strong>
							<table border="0">
								<tr>
									<td>x</td>
									<td><input type="text" name="wmXpos" value="<?php echo $ngg->options['wmXpos'] ?>" size="4" /> px</td>
								</tr>
								<tr>
									<td>y</td>
									<td><input type="text" name="wmYpos" value="<?php echo $ngg->options['wmYpos'] ?>" size="4" /> px</td>
								</tr>
							</table>
						</td>
					</tr>
					</table>
				</div>
			</div> 
				<h3><label><input type="radio" name="wmType" value="image" <?php checked('image', $ngg->options['wmType']); ?> /> <?php _e('Use image as watermark','nggallery') ?></label></h3>
				<table class="wm-table form-table">
					<tr>
						<th><?php _e('URL to file','nggallery') ?> :</th>
						<td><input type="text" size="40" name="wmPath" value="<?php echo $ngg->options['wmPath']; ?>" /><br />
						<?php if(!ini_get('allow_url_fopen')) _e('The accessing of URL files is disabled at your server (allow_url_fopen)','nggallery') ?> </td>
					</tr>
				</table>	
				<h3><label><input type="radio" name="wmType" value="text" <?php checked('text', $ngg->options['wmType']); ?> /> <?php _e('Use text as watermark','nggallery') ?></label></h3>
				<table class="wm-table form-table">	
					<tr>
						<th><?php _e('Font','nggallery') ?>:</th>
						<td><select name="wmFont" size="1">	<?php 
								$fontlist = ngg_get_TTFfont();
								foreach ( $fontlist as $fontfile ) {
									echo "\n".'<option value="'.$fontfile.'" '.ngg_input_selected($fontfile, $ngg->options['wmFont']).' >'.$fontfile.'</option>';
								}
								?>
							</select><br /><span class="setting-description">
							<?php if ( !function_exists(ImageTTFBBox) ) 
									_e('This function will not work, cause you need the FreeType library','nggallery');
								  else 
								  	_e('You can upload more fonts in the folder <strong>nggallery/fonts</strong>','nggallery'); ?>
                            </span>
						</td>
					</tr>
					<tr>
						<th><?php _e('Size','nggallery') ?>:</th>
						<td><input type="text" name="wmSize" value="<?php echo $ngg->options['wmSize'] ?>" size="4" maxlength="2" /> px</td>
					</tr>
					<tr>
						<th><?php _e('Color','nggallery') ?>:</th>
						<td><input type="text" size="6" maxlength="6" id="wmColor" name="wmColor" onchange="setcolor('#previewText', this.value)" value="<?php echo $ngg->options['wmColor'] ?>" />
						<input type="text" size="1" readonly="readonly" id="previewText" style="background-color: #<?php echo $ngg->options['wmColor'] ?>" /> <?php _e('(hex w/o #)','nggallery') ?></td>
					</tr>
					<tr>
						<th valign="top"><?php _e('Text','nggallery') ?>:</th>
						<td><textarea name="wmText" cols="40" rows="4"><?php echo $ngg->options['wmText'] ?></textarea></td>
					</tr>
					<tr>
						<th><?php _e('Opaque','nggallery') ?>:</th>
						<td><input type="text" name="wmOpaque" value="<?php echo $ngg->options['wmOpaque'] ?>" size="3" maxlength="3" /> % </td>
					</tr>
				</table>
			<div class="clear"> &nbsp; </div>
			<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes') ;?>"/></div>
			</form>	
		</div>
		
		<!-- Slideshow settings -->
		
		<div id="slideshow">
		<form name="player_options" method="POST" action="<?php echo $filepath.'#slideshow'; ?>" >
		<?php wp_nonce_field('ngg_settings') ?>
		<input type="hidden" name="page_options" value="irWidth,irHeight,irShuffle,irLinkfromdisplay,irShownavigation,irShowicons,irWatermark,irOverstretch,irRotatetime,irTransition,irKenburns,irBackcolor,irFrontcolor,irLightcolor,irScreencolor,irAudio,irXHTMLvalid" />
		<h2><?php _e('Slideshow','nggallery'); ?></h2>
		<?php if (!NGGALLERY_IREXIST) { ?><p><div id="message" class="error fade"><p><?php _e('The imagerotator.swf is not in the nggallery folder, the slideshow will not work.','nggallery') ?></p></div></p><?php }?>
		<p><?php _e('The settings are used in the JW Image Rotator Version', 'nggallery') ?> 3.17 .
		   <?php _e('See more information for the Flash Player on the web page', 'nggallery') ?> <a href="http://www.longtailvideo.com/players/jw-image-rotator/" target="_blank" >JW Image Rotator from Jeroen Wijering</a>.
		</p>
				<table class="form-table ngg-options">
					<tr>
						<th><?php _e('Default size (W x H)','nggallery') ?>:</th>
						<td><input type="text" size="3" maxlength="4" name="irWidth" value="<?php echo $ngg->options['irWidth'] ?>" /> x
						<input type="text" size="3" maxlength="4" name="irHeight" value="<?php echo $ngg->options['irHeight'] ?>" /></td>
					</tr>					
					<tr>
						<th><?php _e('Shuffle mode','nggallery') ?>:</th>
						<td><input name="irShuffle" type="checkbox" value="1" <?php checked('1', $ngg->options['irShuffle']); ?> /></td>
					</tr>
					<tr>
						<th><?php _e('Show next image on click','nggallery') ?>:</th>
						<td><input name="irLinkfromdisplay" type="checkbox" value="1" <?php checked('1', $ngg->options['irLinkfromdisplay']); ?> /></td>
					</tr>					
					<tr>
						<th><?php _e('Show navigation bar','nggallery') ?>:</th>
						<td><input name="irShownavigation" type="checkbox" value="1" <?php checked('1', $ngg->options['irShownavigation']); ?> /></td>
					</tr>
					<tr>
						<th><?php _e('Show loading icon','nggallery') ?>:</th>
						<td><input name="irShowicons" type="checkbox" value="1" <?php checked('1', $ngg->options['irShowicons']); ?> /></td>
					</tr>
					<tr>
						<th><?php _e('Use watermark logo','nggallery') ?>:</th>
						<td><input name="irWatermark" type="checkbox" value="1" <?php checked('1', $ngg->options['irWatermark']); ?> />
						<span class="setting-description"><?php _e('You can change the logo at the watermark settings','nggallery') ?></span></td>
					</tr>
					<tr>
						<th><?php _e('Stretch image','nggallery') ?>:</th>
						<td>
						<select size="1" name="irOverstretch">
							<option value="true" <?php selected('true', $ngg->options['irOverstretch']); ?> ><?php _e('true', 'nggallery') ;?></option>
							<option value="false" <?php selected('false', $ngg->options['irOverstretch']); ?> ><?php _e('false', 'nggallery') ;?></option>
							<option value="fit" <?php selected('fit', $ngg->options['irOverstretch']); ?> ><?php _e('fit', 'nggallery') ;?></option>
							<option value="none" <?php selected('none', $ngg->options['irOverstretch']); ?> ><?php _e('none', 'nggallery') ;?></option>
						</select>
						</td>
					</tr>
					<tr>					
						<th><?php _e('Duration time','nggallery') ?>:</th>
						<td><input type="text" size="3" maxlength="3" name="irRotatetime" value="<?php echo $ngg->options['irRotatetime'] ?>" /> <?php _e('sec.', 'nggallery') ;?></td>
					</tr>					
					<tr>					
						<th><?php _e('Transition / Fade effect','nggallery') ?>:</th>
						<td>
						<select size="1" name="irTransition">
							<option value="fade" <?php selected('fade', $ngg->options['irTransition']); ?> ><?php _e('fade', 'nggallery') ;?></option>
							<option value="bgfade" <?php selected('bgfade', $ngg->options['irTransition']); ?> ><?php _e('bgfade', 'nggallery') ;?></option>
							<option value="slowfade" <?php selected('slowfade', $ngg->options['irTransition']); ?> ><?php _e('slowfade', 'nggallery') ;?></option>
							<option value="circles" <?php selected('circles', $ngg->options['irTransition']); ?> ><?php _e('circles', 'nggallery') ;?></option>
							<option value="bubbles" <?php selected('bubbles', $ngg->options['irTransition']); ?> ><?php _e('bubbles', 'nggallery') ;?></option>
							<option value="blocks" <?php selected('blocks', $ngg->options['irTransition']); ?> ><?php _e('blocks', 'nggallery') ;?></option>
							<option value="fluids" <?php selected('fluids', $ngg->options['irTransition']); ?> ><?php _e('fluids', 'nggallery') ;?></option>
							<option value="flash" <?php selected('flash', $ngg->options['irTransition']); ?> ><?php _e('flash', 'nggallery') ;?></option>
							<option value="lines" <?php selected('lines', $ngg->options['irTransition']); ?> ><?php _e('lines', 'nggallery') ;?></option>
							<option value="random" <?php selected('random', $ngg->options['irTransition']); ?> ><?php _e('random', 'nggallery') ;?></option>
						</select>
					</tr>
					<tr>
						<th><?php _e('Use slow zooming effect','nggallery') ?>:</th>
						<td><input name="irKenburns" type="checkbox" value="1" <?php checked('1', $ngg->options['irKenburns']); ?> /></td>
					</tr>
					<tr>
						<th><?php _e('Background Color','nggallery') ?>:</th>
						<td><input type="text" size="6" maxlength="6" id="irBackcolor" name="irBackcolor" onchange="setcolor('#previewBack', this.value)" value="<?php echo $ngg->options['irBackcolor'] ?>" />
						<input type="text" size="1" readonly="readonly" id="previewBack" style="background-color: #<?php echo $ngg->options['irBackcolor'] ?>" /></td>
					</tr>
					<tr>					
						<th><?php _e('Texts / Buttons Color','nggallery') ?>:</th>
						<td><input type="text" size="6" maxlength="6" id="irFrontcolor" name="irFrontcolor" onchange="setcolor('#previewFront', this.value)" value="<?php echo $ngg->options['irFrontcolor'] ?>" />
						<input type="text" size="1" readonly="readonly" id="previewFront" style="background-color: #<?php echo $ngg->options['irFrontcolor'] ?>" /></td>
					</tr>
					<tr>					
						<th><?php _e('Rollover / Active Color','nggallery') ?>:</th>
						<td><input type="text" size="6" maxlength="6" id="irLightcolor" name="irLightcolor" onchange="setcolor('#previewLight', this.value)" value="<?php echo $ngg->options['irLightcolor'] ?>" />
						<input type="text" size="1" readonly="readonly" id="previewLight" style="background-color: #<?php echo $ngg->options['irLightcolor'] ?>" /></td>
					</tr>
					<tr>					
						<th><?php _e('Screen Color','nggallery') ?>:</th>
						<td><input type="text" size="6" maxlength="6" id="irScreencolor" name="irScreencolor" onchange="setcolor('#previewScreen', this.value)" value="<?php echo $ngg->options['irScreencolor'] ?>" />
						<input type="text" size="1" readonly="readonly" id="previewScreen" style="background-color: #<?php echo $ngg->options['irScreencolor'] ?>" /></td>
					</tr>
					<tr>					
						<th><?php _e('Background music (URL)','nggallery') ?>:</th>
						<td><input type="text" size="50" id="irAudio" name="irAudio" value="<?php echo $ngg->options['irAudio'] ?>" /></td>
					</tr>
					<tr>
						<th><?php _e('Try XHTML validation (with CDATA)','nggallery') ?>:</th>
						<td><input name="irXHTMLvalid" type="checkbox" value="1" <?php checked('1', $ngg->options['irXHTMLvalid']); ?> />
						<span class="setting-description"><?php _e('Important : Could causes problem at some browser. Please recheck your page.','nggallery') ?></span></td>
					</tr>
					</table>
				<div class="clear"> &nbsp; </div>
				<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes') ;?>"/></div>
		</form>
		</div>
	</div>

	<?php
}

function ngg_get_TTFfont() {
	
	$ttf_fonts = array ();
	
	// Files in wp-content/plugins/nggallery/fonts directory
	$plugin_root = NGGALLERY_ABSPATH . 'fonts';
	
	$plugins_dir = @ dir($plugin_root);
	if ($plugins_dir) {
		while (($file = $plugins_dir->read()) !== false) {
			if (preg_match('|^\.+$|', $file))
				continue;
			if (is_dir($plugin_root.'/'.$file)) {
				$plugins_subdir = @ dir($plugin_root.'/'.$file);
				if ($plugins_subdir) {
					while (($subfile = $plugins_subdir->read()) !== false) {
						if (preg_match('|^\.+$|', $subfile))
							continue;
						if (preg_match('|\.ttf$|', $subfile))
							$ttf_fonts[] = "$file/$subfile";
					}
				}
			} else {
				if (preg_match('|\.ttf$|', $file))
					$ttf_fonts[] = $file;
			}
		}
	}

	return $ttf_fonts;
}

/**********************************************************/
// taken from WP Core

function ngg_input_selected( $selected, $current) {
	if ( $selected == $current)
		return ' selected="selected"';
}
	
function ngg_input_checked( $checked, $current) {
	if ( $checked == $current)
		return ' checked="checked"';
}
?>