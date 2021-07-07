<?php
    function checkFile($file) { 
        $fname = $file['name'];
        $ftype = $file['type'];
        $ftemp = $file['tmp_name'];
        $ferr = $file['error'];
        $fsize = $file['size'];
        $allowed_ext = ['png', 'jpeg', 'jpg'];      

        // check to ensure there is no error with the upload
        if($ferr != 0) {
            return false;
        }  
        
        // explore the filetype and check the type and extension
        $ftype = explode("/", $ftype);
        if($ftype[0] != "image" || !in_array(end($ftype), $allowed_ext)) {
            return false;
        }

        // check filesize
        if($fsize > 5000000) {
            return false;
        }

        return true;
    }

    function saveFile($file,$dir) {
        $ftype = explode("/",$file['type']);
        $new_filename = uniqid('', false) . "." . end($ftype);
        
        $new_dest = $dir . $new_filename;

        if(move_uploaded_file($file['tmp_name'], $new_dest)) {
            return 'assets/images/posts/' . $new_dest;
          } else {
            return null;
          }
    }
?>