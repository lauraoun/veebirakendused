<?php
	class Upload_photo {
		private $photo_to_upload;
		private $image_file_type;
		private $temp_image;
		public $new_temp_image; //hiljem, kui class hakkab kõike ise tegema, siis ilmselt private
		
		function __construct($photo_to_upload, $image_file_type){
			$this->photo_to_upload = $photo_to_upload;
			$this->image_file_type = $image_file_type;//see tuleks edaspidi klassis kohapeal kindlaks teha
			//ka test, kas on üldse pilt ja sobiv, peaks siin klassis olema
			$this->temp_image = $this->create_image_from_file($this->photo_to_upload["tmp_name"], $this->image_file_type);
		}
		
		private function create_image_from_file($image, $image_file_type){
			$temp_image = null;
			if($image_file_type == "jpg"){
				$temp_image = imagecreatefromjpeg($image);
			}
			if($image_file_type == "png"){
				$temp_image = imagecreatefrompng($image);
			}
			return $temp_image;
		}
    

        public function resize_photo($src, $w, $h, $keep_orig_proportion = true){
            $image_w = imagesx($this->temp_image);
            $image_h = imagesy($this->temp_image);
            $new_w = $w;
            $new_h = $h;
            $cut_x = 0;
            $cut_y = 0;
            $cut_size_w = $image_w;
            $cut_size_h = $image_h;
            
            if($w == $h){
                if($image_w > $image_h){
                    $cut_size_w = $image_h;
                    $cut_x = round(($image_w - $cut_size_w) / 2);
                } else {
                    $cut_size_h = $image_w;
                    $cut_y = round(($image_h - $cut_size_h) / 2);
                }	
            } elseif($keep_orig_proportion){//kui tuleb originaaproportsioone säilitada
                if($image_w / $w > $image_h / $h){
                    $new_h = round($image_h / ($image_w / $w));
                } else {
                    $new_w = round($image_w / ($image_h / $h));
                }
            } else { //kui on vaja kindlasti etteantud suurust, ehk pisut ka kärpida
                if($image_w / $w < $image_h / $h){
                    $cut_size_h = round($image_w / $w * $h);
                    $cut_y = round(($image_h - $cut_size_h) / 2);
                } else {
                    $cut_size_w = round($image_h / $h * $w);
                    $cut_x = round(($image_w - $cut_size_w) / 2);
                }
            }
            
            //loome uue ajutise pildiobjekti
            $this->my_new_image = imagecreatetruecolor($new_w, $new_h);
            //kui on läbipaistvusega png pildid, siis on vaja säilitada läbipaistvusega
            imagesavealpha($my_new_image, true);
            $trans_color = imagecolorallocatealpha($my_new_image, 0, 0, 0, 127);
            imagefill($my_new_image, 0, 0, $trans_color);
            imagecopyresampled($my_new_image, $this->temp_image, 0, 0, $cut_x, $cut_y, $new_w, $new_h, $cut_size_w, $cut_size_h);
            
        }
	}//class lõppeb