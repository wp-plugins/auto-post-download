<?php

class APD_Attatchment {

	private $postId;
	private $post;
	private $image;
	private $uploadDir;
	private $zipFile;
	private $zipArchive;
	private $options;

	function __construct($postId){
		$this->postId = $postId;
		$this->post = get_post($this->postId);
		$this->image = wp_get_attachment_metadata(get_post_thumbnail_id($this->postId));
		$this->uploadDir = wp_upload_dir();
		$this->zipFile = $this->uploadDir['path']."/{$this->postId}.zip";
		$this->zipArchive  = new ZipArchive();
		$this->options = get_option('apd_options');
		add_action('publish_post', 'apd_generate_attachment' );
	}

	public function generate(){
		$cats = $this->options['apd_cats'];

		if($cats == null || in_category( $cats, $this->postId)){
			$this->generateZipFile();
			$this->generateAttachment();
		}
	}

	private function generateZipFile(){
		if (!$this->zipArchive->open($this->zipFile, ZIPARCHIVE::OVERWRITE)){
			die("Failed to create archive\n");
		}

		// do we have image attached to this post?
		if(is_array($this->image) && $this->getItemsIncluded() != 'text'){
			$filetype = wp_check_filetype(basename($this->image['file']));
			$this->zipArchive->addFile(WP_CONTENT_DIR . "/uploads/" . $this->image['file'], "image." . $filetype['ext']);
		}

		if($this->getItemsIncluded() != 'image'){
			if($this->options['apd_format'] == null || $this->options['apd_format'] == 'html'){
				$this->zipArchive->addFromString("content.html", $this->generateHTMLContent());
			} else {
				$this->zipArchive->addFromString("content.txt", $this->generatePlainContent());
			}
		}
		
		if (!$zipArchive->status == ZIPARCHIVE::ER_OK)
			echo "Failed to write local files to zip\n";

		$this->zipArchive->close();
	}

	private function generateHTMLContent(){
		$content = '<!DOCTYPE html><html lang="en"><head><meta charset=utf-8 /><title></title></head><body>';

		$meta_list = $this->options['apd_meta'];

		foreach($meta_list as $meta){
			$content .= '<p>';
			
			if($this->options['apd_meta_title'] == true){
					$content .= '<b>' . $meta . ':</b> ';
			}
			
			$content .=  get_post_meta($this->postId, $meta, true) . '</p>';
			
			$content .= '<hr/>';
			
		}

		$content .= $this->post->post_content . '</body></html>';

		return $content;
	}

	private function generatePlainContent(){
		$content = null;

		$meta_list = $this->options['apd_meta'];

		foreach($meta_list as $meta){			
			if($this->options['apd_meta_title'] == true){
				$content .= $meta . ': ';
			}			
			
			$content .= wp_strip_all_tags(get_post_meta($this->postId, $meta, true));
			$content .= '------';
		}

		$content .= wp_strip_all_tags($this->post->post_content);

		return $content;
	}

	private function generateAttachment(){
		if(get_post_meta($this->postId, 'adp_attachment_id', true) == null){
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$filetype = wp_check_filetype(basename($this->zipFile), null);
			$attachment = array(
					'guid' => $upload_dir['url'] . '/' . basename( $zipFile ),
					'post_mime_type' => $filetype['type'],
					'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $zipFile ) ),
					'post_content' => '',
					'post_status' => 'inherit'
			);
			$attachId = wp_insert_attachment($attachment, $this->zipFile, $this->postId);
			$attachData = wp_generate_attachment_metadata($attachId, $this->zipFile );
			wp_update_attachment_metadata( $attachId, $attachData );

			add_post_meta($this->postId, 'adp_attachment_id', $attachId);
		}
	}

	private function getItemsIncluded(){
		$type = $this->options['apd_content'];

		switch($type){
			case 'image':
				return 'image'; break;
			case 'text':
				return 'text'; break;
			default:
				return 'both'; break;
		}
	}

}

?>