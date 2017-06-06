<?php
	require_once "Header.php";
	
	// Random alphanumerical(lower&upper case) string with given length
	function random_string($len)
	{
		$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$res = "";
		for($i = 0; $i < $len; $i++)
		{
			$res = $res.$chars[rand(0, strlen($chars)-1)];
		}
		return $res;
	}
	// Non exisiting filename in folder $targetFolder with any extension
	function generate_filename($targetFolder)
	{
		$filename = "";
		do
		{
			$filename = random_string(8);
		}
		while(count(glob($targetFolder."/".$filename.".*")) > 0);
		return $filename;
	}
	
	// Retreives the information of a user by giving the user's API key
	// returns an array of the user's information with:
	// { id, name, last_upload }
	// Returns an empty array if the key is invalid or not registered
	function get_user($key)
	{
		$con = open_db();
	
		$query = "SELECT id,name,last_upload, flags FROM tdrz_users WHERE `key`=?";
		$stmt = $con->prepare($query);
		$stmt->bind_param("s", $key);
		$stmt->bind_result($id, $name, $last_upload, $flags);
		$stmt->execute();
		
		$ret = [];
		
		$user_id = false;
		if($stmt->fetch())
		{
			$ret = array(
				"id" => $id, 
				"name" => $name, 
				"last_upload" => $last_upload,
				"flags" => $flags);
		}
		
		$stmt->execute();
		$stmt->close();
		
		return $ret;
	}
	function check_flags($flags)
	{
		global $user_info; 
		return ($user_info["flags"] & $flags) == $flags;
		return true;
	}
	
	// Register a new file in the database
	// given file ID (which is also the filename -extension)
	// hash of the file
	// the original file's extension
	// and the original file name
	function register_file($dst_id, $hash, $extension, $ofn, $owner_id)
	{
		global $result;
		$con = open_db();
	
		$query = "SELECT upload_delay FROM tdrz_users WHERE id=?";
		$stmt = $con->prepare($query);
		$stmt->bind_param("i", $owner_id);
		$stmt->bind_result($upload_delay);
		$stmt->execute();
		if(!$stmt->fetch())
		{
			$result["error"] = "Failed to get upload_delay for user";
			return false;
		}
		$stmt->close();
		$result["upload_delay"] = $upload_delay;
	
		$query = "SELECT last_upload FROM tdrz_users WHERE id=?";
		$stmt = $con->prepare($query);
		$stmt->bind_param("i", $owner_id);
		$stmt->bind_result($last_upload);
		$stmt->execute();
		if(!$stmt->fetch())
		{
			$result["error"] = "Failed to find last_upload for user";
			return false;
		}
		$stmt->close();
		$last_upload = strtotime($last_upload);
		
		$delta = time() - $last_upload;
		if($delta < $upload_delay)
		{
			$result["upload_timeout"] = ($upload_delay - $delta);
			$result["error"] = "Please wait ".($result["upload_timeout"])." more seconds before uploading again";
			return false;
		}
		
		// Register the file
		$query = "INSERT INTO tdrz_files(id, hash, ofn, extension, date, owner) VALUES(?,?,?,?,NOW(),?)";
		$stmt = $con->prepare($query);
		$stmt->bind_param("ssssi", $dst_id, $hash, $ofn, $extension, $owner_id);
		$stmt->execute();
		$stmt->close();
		
		// Update upload date
		$query = "UPDATE tdrz_users SET last_upload=NOW() WHERE id=?";
		$stmt = $con->prepare($query);
		$stmt->bind_param("i", $owner_id);
		$stmt->execute();
		$stmt->close();
	
		return true;	
	}
	function deregister_file($id, $owner_id)
	{
		$con = open_db();
	
		$query = "DELETE FROM tdrz_files WHERE id=? && owner=?";
		$stmt = $con->prepare($query);
		
		$stmt->bind_param("si", $id, $owner_id);
		$stmt->execute();
		
		$stmt->close();
		
		remove_thumbnail($id);
		
		return true;
	}
	
	// Finds a file in the database given the file's md5 hash
	// Returned is empty array or array with data:
	// {id, ofn, extension, date}
	function find_file_by_hash($hash, $owner_id)
	{
		$con = open_db();
	
		$query = "SELECT id,ofn,extension,date,owner FROM tdrz_files WHERE hash=? && owner=?";
		$stmt = $con->prepare($query);
		
		$stmt->bind_param("si", $hash, $owner_id);
		$stmt->execute();
		$stmt->bind_result($id, $ofn, $ext, $date, $owner);
		if($stmt->fetch())
		{
			$ret = array(
				"id" => $id, 
				"ofn" => $ofn, 
				"extension" => $ext, 
				"date" => $date);
		}
		else
		{
			return [];
		}
		
		$stmt->close();
		return $ret;
	}
	
	// Finds a file in the database given it's ID
	// returns either an empty array or an array with the file data containing the following information:
	// {hash, ofn, extension, date}
	function find_file($id)
	{
		$con = open_db();
	
		$query = "SELECT hash,ofn,extension,date,owner FROM tdrz_files WHERE id=?";
		$stmt = $con->prepare($query);
		
		$stmt->bind_param("s", $id);
		$stmt->execute();
		$stmt->bind_result($hash, $ofn, $ext, $date, $owner);
		if($stmt->fetch())
		{
			$ret = array(
				"hash" => $hash, 
				"ofn" => $ofn, 
				"extension" => $ext, 
				"date" => $date);
		}
		else
		{
			return [];
		}
		
		$stmt->close();
		return $ret;
	}

	function remove_thumbnail($id)
	{
		global $data_dir;
		$tdir = $data_dir."/thumbs";
		$tname = $tdir."/".$id.".jpg";
		if(file_exists($tname))
		{
			unlink($tname);
		}
	}
	function get_thumbnail_url($file)
	{
		global $data_dir;
		$tdir = $data_dir."/thumbs";
		if(!file_exists($tdir))
		{
            return "";
		}
		
		$tname = $tdir."/".pathinfo($file, PATHINFO_FILENAME).".jpg";
		
		// Generate image?
		if(!file_exists($tname))
		{
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			
			//$font = $tdir."/_font.ttf";
			$im = imagecreatetruecolor(128, 128);
			$src = false;
			if($ext == "png")
			{
				$src = imagecreatefrompng($file);
			}
			else if($ext == "jpg" || $ext === "jpeg")
			{
				$src = imagecreatefromjpeg($file);
			}
			else if($ext == "gif")
			{
				$src = imagecreatefromgif($file);
			}
			else
			{
				return "";
			}
			
			$ox = 0;
			$oy = 0;
			$sx = 128;
			$sy = 128;
			if(imagesx($src) > imagesy($src))
			{
				$ar = imagesy($src)/imagesx($src);
				$sy = $sx * $ar;
				$oy = (128-$sy)/2;
			}
			else
			{
				$ar = imagesx($src)/imagesy($src);
				$sx = $sy * $ar;
				$ox = (128-$sx)/2;
			}
			
			imagecopyresized($im, $src, $ox, $oy, 0, 0, $sx, $sy, imagesx($src), imagesy($src));
			imagejpeg($im, $tname);
		}
		
		return $tname;
	}
?>
