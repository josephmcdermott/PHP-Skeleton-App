<?php
/**
 * The PHP Skeleton App
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @copyright   2015 Goran Halusa
 * @link        https://github.com/ghalusa/PHP-Skeleton-App
 * @license     https://github.com/ghalusa/PHP-Skeleton-App/wiki/License
 * @version     0.1.1
 * @package     PHP Skeleton App
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phpskeleton\models;

use finfo;

/**
 * Utility
 *
 * Utility class for the PHP Skeleton App.
 *
 * @author      Goran Halusa <gor@webcraftr.com>
 * @since       0.1.0
 */
class utility {

  /**
   * Constructor
   *
   * Placeholder in case we want to put something here later
   */
  public function __construct(){
  	// Placeholder in case we want to put something here later.
  }

  /**
   * Include All Files In Directory
   *
   * Includes all files in a directory, with the option of recursively scanning the directory.
   *
   * @param     string    $dir          The directory
   * @param     bool      $recursive    Scan the directory recursively
   * @return    void
   */
	public static function include_all_files_in_directory($dir, $recursive = false){
		if(is_dir($dir)){
			if ($handle = opendir($dir)) {
			    while (false !== ($entry = readdir($handle))) {
			    	$file_type = filetype($dir . "/" . $entry);
			        if ($entry != "." && $entry != "..") {
			        	if($file_type == "file"){
			        		require_once $dir . "/" . $entry;
			        	}elseif($file_type == "dir"){
			        		if($recursive){
				        		self::include_all_files_in_directory($dir . "/" . $entry,true);
				        	}
			        	}
			        }
			    }
			    closedir($handle);
			}
		}
	}

  /**
   * Gump Parse Errors
   *
   * Parse errors returned by Gump.
   *
   * @param     array     $gump_failed_validation_array    The failed validation array returned by Gump
   * @param     string    $array_prepend                   Prepend the array with the supplied character
   * @return    array     The array of errors
   */
	public static function gump_parse_errors($gump_failed_validation_array, $array_prepend = false){
		$error_array = array();
		foreach($gump_failed_validation_array as $single_error){
			if($single_error["rule"] == "validate_required"){
				$error_array[$single_error["field"]] = ucwords(str_replace("_", " ",$single_error["field"])) . " is missing.";
			}else{
				if($single_error["value"]){
					$error_array[$single_error["field"]] = ucwords(str_replace("_", " ",$single_error["field"])) . " is invalid.";
				}
			}
		}
		if(!empty($array_prepend) && !empty($error_array)){
			return array($array_prepend => $error_array);
		}else{
			return $error_array;
		}
	}

  /**
   * Array Flatten
   *
   * Flatten an array, returning a single level array.
   *
   * @param       object    $passed_array    The multidimensional array
   * @param       array     $output_array    The outputted array
   * @param       string    $array_key       The array key
   * @return      array     The single level array
   */
	public static function array_flatten($passed_array, &$output_array = false, $array_key = false){
	    if(!is_array($output_array)){
	        $output_array = array();
	    }
	    foreach($passed_array as $passed_key => $passed_array_value){
	    	if(is_array($passed_array_value)){
	            self::array_flatten($passed_array_value, $output_array,$array_key);
	        }else{
	        	if(empty($array_key) || $array_key == $passed_key){
	            	array_push($output_array, $passed_array_value);
				}
	        }
	    }
	    return $output_array;
	}

  /**
   * Gen UUID
   *
   * Generate a UUID.
   *
   * @return    string    The UUID
   */
	public static function gen_uuid() {
	    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	        // 32 bits for "time_low"
	        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

	        // 16 bits for "time_mid"
	        mt_rand( 0, 0xffff ),

	        // 16 bits for "time_hi_and_version",
	        // four most significant bits holds version number 4
	        mt_rand( 0, 0x0fff ) | 0x4000,

	        // 16 bits, 8 bits for "clk_seq_hi_res",
	        // 8 bits for "clk_seq_low",
	        // two most significant bits holds zero and one for variant DCE1.1
	        mt_rand( 0, 0x3fff ) | 0x8000,

	        // 48 bits for "node"
	        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	    );
	}

  /**
   * Format Bytes
   *
   * Return a human readable format of a file size.
   * Taken from the php.net filesize page in the comments.
   *
   * @param     int     $a_bytes    The integer
   * @return    string  The integer with the appropriate units
   */
	public static function _format_bytes($a_bytes){
	    if ($a_bytes < 1024) {
	        return $a_bytes .' B';
	    } elseif ($a_bytes < 1048576) {
	        return round($a_bytes / 1024, 2) .' KB';
	    } elseif ($a_bytes < 1073741824) {
	        return round($a_bytes / 1048576, 2) . ' MB';
	    } elseif ($a_bytes < 1099511627776) {
	        return round($a_bytes / 1073741824, 2) . ' GB';
	    } elseif ($a_bytes < 1125899906842624) {
	        return round($a_bytes / 1099511627776, 2) .' TB';
	    } elseif ($a_bytes < 1152921504606846976) {
	        return round($a_bytes / 1125899906842624, 2) .' PB';
	    } elseif ($a_bytes < 1180591620717411303424) {
	        return round($a_bytes / 1152921504606846976, 2) .' EB';
	    } elseif ($a_bytes < 1208925819614629174706176) {
	        return round($a_bytes / 1180591620717411303424, 2) .' ZB';
	    } else {
	        return round($a_bytes / 1208925819614629174706176, 2) .' YB';
	    }
	}

  /**
   * Get MIME Type
   *
   * Return the MIME type of a file.
   *
   * @param     string    $file    The file
   * @return    string    The MIME type
   */
	public static function get_mime_type($file){
		$arrayZips = array("application/zip", "application/x-zip", "application/x-zip-compressed");
		$mime_map = array(
			"docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
			,"dotx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.template"
			,"pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation"
			,"xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
		);

		// Get the extension.
		$ext = "";
		$ext_array = explode(".",$file);
		if(count($ext_array) > 1){
			$ext = $ext_array[count($ext_array) - 1];
		}

		$finfo = new finfo(FILEINFO_MIME);
		$type = $finfo->file($file);
		$type = substr($type, 0, strpos($type, ';'));

		if (in_array($type, $arrayZips) && array_key_exists($ext, $mime_map)){
		   return $mime_map[$ext];
		}else{
			return $type;
		}
	}

  /**
   * Subvalue Sort
   *
   * Description coming soon.
   *
   * @param     string     $passed_array   The passed array
   * @param     string     $subkey         Subkey
   * @param     string     $sort           Sort
   * @param     bool       $start          Start
   * @param     bool       $stop           Stop
   * @return    array      The final array
   */
	public function subvalue_sort($passed_array, $subkey, $sort = "DESC", $start = false, $stop = false){
        $first_temp_array = array();
		$second_temp_array = array();
        foreach($passed_array as $k => $v){
            $first_temp_array[$k] = strip_tags(strtolower($v[$subkey]));
        }
        if($sort == "DESC"){
            asort($first_temp_array);
        }elseif($sort == "ASC"){
            arsort($first_temp_array);
        }

        foreach($first_temp_array as $key => $val){
        	if(is_numeric($key)){
        		$second_temp_array[] = $passed_array[$key];
        	}else{
        		$second_temp_array[$key] = $passed_array[$key];
        	}
        }

        if($start || $stop){
            $final_array = array_slice($second_temp_array, $start, $stop);
        }else{
            $final_array = $second_temp_array;
        }

        return $final_array;
    }

  /**
   * Check Date
   *
   * Description coming soon.
   *
   * @param     string     $passed_date      The passed date
   * @param     string     $input_format     Input format
   * @param     string     $output_format    Output format
   * @return    string     The date
   */
	public function check_date($passed_date, $input_format = "form", $output_format = "form"){
		$form_values = array(
			"form" => array(
				"zeros_check" => "00/00/0000"
				,"1969_check" => "12/31/1969"
				,"year_adjust" => "1/1/" . $passed_date
				,"format_code" => 'm/d/Y'
			)
			,"mysql" => array(
				"zeros_check" => "0000-00-00"
				,"1969_check" => "1969-12-31"
				,"year_adjust" => $passed_date . "/01/01"
				,"format_code" => 'Y-m-d'
			)
		);
        switch($passed_date){
            case $form_values[$input_format]["zeros_check"]:
            case null:
            case "":
            case $form_values[$input_format]["1969_check"]:
                return false;
                break;
            default:
				if(strlen($passed_date) == 4){
					$passed_date = $form_values[$input_format]["year_adjust"];
				}
                $new_date = date($form_values[$output_format]["format_code"], strtotime($passed_date));
                $new_date = $new_date . "";
                switch($new_date){
                  case $form_values[$output_format]["zeros_check"]:
                  case null:
                  case "":
                  case $form_values[$output_format]["1969_check"]:
                    return false;
                    break;
                  default:
                    return date($form_values[$output_format]["format_code"], strtotime($passed_date));
                    break;
                }
        }
    }
}
?>