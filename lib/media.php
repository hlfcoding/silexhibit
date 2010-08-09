<?php if (!defined('SITE')) exit('No direct script access allowed');


/**
* Media class
*
* Resizes and thumbnails images
* 
* @version 1.0
* @author Vaska 
*/
class Media
{
    var $image;
    var $path;
    var $filename;
    var $quality;
    var $filemime;
    var $maxsize;
    var $thumbsize;
    var $sizelimit;
    var $size       = array();
    var $new_size   = array();
    var $makethumb  = FALSE;
    var $final_size = array();
    var $out_size   = array();
    var $uploads    = array();
    var $sys_thumb  = 100;
    var $offset     = array();
    var $sys_size   = array();
    var $type;
    var $input_image;
    var $upload_max_size;
    var $file_size;
    var $tRed;
    var $tBlue;
    var $tGreen;
    var $tFlag      = FALSE;
    
    /**
    * Returns allowed uploads (filetypes from config.php) array and max size
    *
    * @param void
    * @return mixed
    */
    function Media()
    {
        global $uploads;
        $this->uploads = $uploads;
        $this->upload_max_size();
    }

    /**
    * Returns filetype by file extension
    *
    * @param void
    * @return string
    */
    function getFileType()
    {
        $type = explode('.', $this->filename);
        $this->filemime = array_pop($type);
    }
    
    /**
    * Returns array of image filetypes
    *
    * @param void
    * @return array
    */
    function allowThumbs()
    {
        return $this->uploads['images'];
    }
    
    /**
    * Returns server settings for max upload size
    *
    * @param void
    * @return integer
    */
    function upload_max_size()
    {
        $upload_max_filesize = ini_get('upload_max_filesize');
        $upload_max_filesize = preg_replace('/M/', '000000', $upload_max_filesize);
        
        $post_max_size = ini_get('post_max_size');
        $post_max_size = preg_replace('/M/', '000000', $post_max_size);
        
        $this->upload_max_size = ($post_max_size >= $upload_max_filesize) ? $upload_max_filesize : $post_max_size;
    }
    
    /**
    * Return destroys input image
    *
    * @param void
    * @return mixed
    */
    function uploader()
    {
        $this->getFileType();
        $this->get_input();
        $this->size = getimagesize($this->image);
        
        // first image
        $this->upload_image($this->maxsize);
        $this->file_size();
        
        // system thumbnail
        $this->sys_thumb($this->sys_thumb);
        
        // we'll need to distinguish this for only images
        if (($this->makethumb == TRUE) && (in_array($this->filemime, $this->allowThumbs())))
        {
            $this->upload_image($this->thumbsize, TRUE);
        }
        
        imagedestroy($this->input_image);
    }

    /**
    * Deals with the bits
    * Oh. So. Messy. ;)
    *
    * @param integer $maxwidth
    * @param boolean $thumb
    * @return integer
    */
    function upload_image($maxwidth, $thumb=FALSE)
    {
        if (($maxwidth != 9999) || ($thumb == TRUE))
        {
            // get the new sizes
            $this->resizing($maxwidth);
            
            $output_image = imagecreatetruecolor($this->new_size['w'], $this->new_size['h']);
            
            // if we have transparency in the image
            // it sucks that PHP auto sets background to black!!!!!!!
            if ($this->tFlag == TRUE)
            {
                imagecolortransparent($output_image, imagecolorallocate($output_image, 
                    $this->tRed, $this->tGreen, $this->tBlue));
            }

            // png special handling rules
            if ($this->filemime == 'png')
            {
                // http://be.php.net/manual/en/function.imagesavealpha.php
                imagealphablending($output_image, false);
                imagesavealpha($output_image, true);
            }

            // resizing
            @imagecopyresampled($output_image,  $this->input_image, 0, 0, 0, 0,
                $this->new_size['w'], $this->new_size['h'], $this->size[0], $this->size[1]);
            
            // how do we flag when we are working on thumbs>
            if ($thumb == TRUE) 
            {
                $this->image =  $this->path . 'th-' . $this->filename;
            }
        
            $this->do_output($output_image, $this->image);
            imagedestroy($output_image);
        }
        else
        {
            // no resize - get file x, y
            $this->out_size['x'] = $this->size[0];
            $this->out_size['y'] = $this->size[1];
            
            return;
        }
            
            
        if ($thumb == FALSE)
        {
            $this->out_size['x'] = $this->new_size['w'];
            $this->out_size['y'] = $this->new_size['h'];
        }
        
        return;
    }
    
    /**
    * Returns file size
    *
    * @param void
    * @return integer
    */
    function file_size()
    {
        $size = str_replace('.', '', @filesize($this->image));
        $this->file_size = ($size == 0) ? 0 : $size;
    }
    
    
    /**
    * Returns input image according to type
    *
    * @param void
    * @return variable
    */
    function get_input()
    {
        switch($this->filemime)
        {
            case 'gif':
                $this->checkBackground();
                $this->input_image = imagecreatefromgif($this->image);
                break;
            case 'jpg':
                $this->input_image = imagecreatefromjpeg($this->image);
                break;
            case 'jpeg':
                $this->input_image = imagecreatefromjpeg($this->image);
                break;
            case 'png':
                $this->input_image = imagecreatefrompng($this->image);
                break;
        }
    }
    
    /**
    * Checks file to find background transparency
    *
    * @param void
    * @return string
    */
    function checkBackground()
    {
        // we need to determine transparency for gifs
        // http://be.php.net/imagecolortransparent
        $fp                 = fopen($this->image, 'rb');
        $result             = fread($fp, 13);
        $colorFlag          = ord(substr($result, 10, 1)) >> 7;
        $background         = ord(substr($result, 11));
        if ($colorFlag)
        {
            $tableSizeNeeded = ($background + 1) * 3;
            $result         = fread($fp, $tableSizeNeeded);
            $this->tRed     = ord(substr($result, $background * 3, 1));
            $this->tGreen   = ord(substr($result, $background * 3 + 1, 1));        
            $this->tBlue    = ord(substr($result, $background * 3 + 2, 1));
        
            if (isset($this->tRed) && isset($this->tGreen) && isset($this->tBlue))
            {
                $this->tFlag = TRUE;
            }           
        }
        fclose($fp);
        
        return;
    }
    
    /**
    * Returns output image according to type
    *
    * @param string $output_image
    * @param string $image
    * @return string
    */
    function do_output($output_image, $image)
    {
        switch($this->filemime) {
            case 'gif':
                imagegif($output_image, $image);
                break;
            case 'jpg':
                imagejpeg($output_image, $image, $this->quality);
                break;
            case 'jpeg':
                imagejpeg($output_image, $image, $this->quality);
                break;
            case 'png':
                imagepng($output_image, $image);
                break;
        }
    }

    /**
    * Returns array of file size
    * (natural dimensions)
    *
    * @param integer $maxwidth
    * @return array
    */
    function resizing($maxwidth)
    {
        $width_percentage = $maxwidth / $this->size[0];
        $height_percentage = $maxwidth / $this->size[1];

        if (($this->size[0] > $maxwidth) || ($this->size[1] > $maxwidth))
        {
            if ($width_percentage <= $height_percentage)
            {
                $this->new_size['w'] = round($width_percentage * $this->size[0]);
                $this->new_size['h'] = round($width_percentage * $this->size[1]);
            } 
            else
            {
                $this->new_size['w'] = round($height_percentage * $this->size[0]);
                $this->new_size['h'] = round($height_percentage * $this->size[1]);
            }   
        }
        else
        {  // square images ?
            $this->new_size['w'] = $this->size[0];
            $this->new_size['h'] = $this->size[1];
        }
    }
    
    /**
    * Returns array of file size
    * (square thumbnails)
    *
    * @param void
    * @return array
    */
    function sys_resize()
    {
        $this->sys_size['w'] = $this->size[0];
        $this->sys_size['h'] = $this->size[1];
        
        if ($this->sys_size['w'] > $this->sys_size['h']) 
        {
           $this->offset['w'] = ($this->sys_size['w'] - $this->sys_size['h'])/2;
           $this->offset['h'] = 0;
           $this->sys_size['w'] = $this->sys_size['h'];
        } 
        elseif ($this->sys_size['h'] > $this->sys_size['w']) 
        {
           $this->offset['w'] = 0;
           $this->offset['h'] = ($this->sys_size['h'] - $this->sys_size['w'])/2;
           $this->sys_size['h'] = $this->sys_size['w'];
        } 
        else
        {
            $this->offset['w'] = 0;
            $this->offset['h'] = 0;
            $this->sys_size['w'] = $this->sys_size['h'];
        }
    }
    
    /**
    * Returns imagedestroy of input image
    *
    * @param integer $maxwidth
    * @return mixed
    */
    function sys_thumb($maxwidth)
    {
        $this->sys_resize();

        $output_image = imagecreatetruecolor($this->sys_thumb, $this->sys_thumb);
        
        // if we have transparency in the image
        // it sucks that PHP auto sets background to black!!!!!!!
        if ($this->tFlag == TRUE)
        {
            imagecolortransparent($output_image, imagecolorallocate($output_image, 
                $this->tRed, $this->tGreen, $this->tBlue));
        }

        // png special handling rules
        if ($this->filemime == 'png')
        {
            // http://be.php.net/manual/en/function.imagesavealpha.php
            imagealphablending($output_image, false);
            imagesavealpha($output_image, true);
        }
        
        @imagecopyresampled($output_image, $this->input_image, 0, 0, 
            $this->offset['w'], $this->offset['h'],
            $this->sys_thumb, $this->sys_thumb, 
            $this->sys_size['w'], $this->sys_size['h']);

        // for sys- naming convention
        $image =  $this->path . 'sys-' . $this->filename;

        $this->do_output($output_image, $image);
        imagedestroy($output_image);

        return;
    }

    /**
    * Returns new file name based upon exiting files to prevent name collisions
    *
    * @param string $filename
    * @return string
    */
    function checkName($filename)
    {
        static $v = 1;
        
        if (file_exists($this->path . '/' . $filename . $this->type))
        {
            // remove the previous version number
            $filename = preg_replace('/_v[0-9]{1,3}$/i', '', $filename);
            $v++;
            $filename = $filename . '_v' . $v;
            $filename = $this->checkName($filename);
        }
        else
        {
            $v = 1;
            return $filename;
        }
        
        return $filename;
    }

}
