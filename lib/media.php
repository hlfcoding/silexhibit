<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Media class
 * Resizes and thumbnails images
 * @version 1.1
 * @package Indexhibit
 * @subpackage Indexhibit CMS
 * @author Vaska 
 * @author Peng Wang <peng@pengxwang.com>
 */

class Media
{
    public $image;
    public $path;
    public $filename;
    public $quality;
    public $filemime;
    public $maxsize;
    public $thumbsize;
    public $sizelimit;
    public $size       = array();
    public $new_size   = array();
    public $makethumb  = false;
    public $final_size = array();
    public $out_size   = array();
    public $uploads    = array();
    public $sys_thumb  = 100;
    public $offset     = array();
    public $sys_size   = array();
    public $type;
    public $input_image;
    public $upload_max_size;
    public $file_size;
    public $tRed;
    public $tBlue;
    public $tGreen;
    public $tFlag      = false;
    
    public function __construct ()
    {
        global $uploads;
        $this->uploads = $uploads;
        $this->upload_max_size();
    }

    /**
     * Set filetype by file extension
     **/
    public function getFileType ()
    {
        $type = explode('.', $this->filename);
        $this->filemime = array_pop($type);
    }
    
    /**
     * @return array image filetypes
     **/
    public function allowThumbs ()
    {
        return $this->uploads['images'];
    }
    
    public function upload_max_size ()
    {
        $upload_max_filesize = ini_get('upload_max_filesize');
        $upload_max_filesize = preg_replace('/M/', '000000', $upload_max_filesize);
        
        $post_max_size = ini_get('post_max_size');
        $post_max_size = preg_replace('/M/', '000000', $post_max_size);
        
        $this->upload_max_size = ($post_max_size >= $upload_max_filesize) ? $upload_max_filesize : $post_max_size;
    }
    
    /**
     * Destroys input image
     **/
    public function uploader ()
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
        if (($this->makethumb === true) && (in_array($this->filemime, $this->allowThumbs()))) {
            $this->upload_image($this->thumbsize, true);
        }
        
        imagedestroy($this->input_image);
    }

    /**
     * Deals with the bits
     * @param integer
     * @param boolean
     * @return integer
     * @todo messy
     * @todo it sucks that PHP auto sets background to black
     * @link http://be.php.net/manual/en/function.imagesavealpha.php
     **/
    public function upload_image ($maxwidth, $thumb = false)
    {
        if (($maxwidth !== 9999) || ($thumb === true)) {
            // get the new sizes
            $this->resizing($maxwidth);
            $output_image = imagecreatetruecolor($this->new_size['w'], $this->new_size['h']);
            // if we have transparency in the image
            if ($this->tFlag === true) {
                imagecolortransparent($output_image, imagecolorallocate($output_image, 
                    $this->tRed, $this->tGreen, $this->tBlue));
            }
            // png special handling rules
            if ($this->filemime === 'png') {
                imagealphablending($output_image, false);
                imagesavealpha($output_image, true);
            }
            // resizing
            @imagecopyresampled($output_image,  $this->input_image, 0, 0, 0, 0,
                $this->new_size['w'], $this->new_size['h'], $this->size[0], $this->size[1]);
            // how do we flag when we are working on thumbs>
            if ($thumb === true) {
                $this->image =  $this->path . 'th-' . $this->filename;
            }
            $this->do_output($output_image, $this->image);
            imagedestroy($output_image);
        } else {
            // no resize - get file x, y
            $this->out_size['x'] = $this->size[0];
            $this->out_size['y'] = $this->size[1];
            return;
        }
        if ($thumb === false) {
            $this->out_size['x'] = $this->new_size['w'];
            $this->out_size['y'] = $this->new_size['h'];
        }
        return;
    }
    
    /**
     * Sets file size
     **/
    public function file_size ()
    {
        $size = str_replace('.', '', @filesize($this->image));
        $this->file_size = ($size === 0) ? 0 : $size;
    }
    
    /**
     * Sets input image according to type
     **/
    public function get_input ()
    {
        switch($this->filemime) {
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
     * We need to determine transparency for gifs
     * @link http://be.php.net/imagecolortransparent
     **/
    public function checkBackground ()
    {
        $fp                 = fopen($this->image, 'rb');
        $result             = fread($fp, 13);
        $colorFlag          = ord(substr($result, 10, 1)) >> 7;
        $background         = ord(substr($result, 11));
        if ($colorFlag) {
            $tableSizeNeeded = ($background + 1) * 3;
            $result         = fread($fp, $tableSizeNeeded);
            $this->tRed     = ord(substr($result, $background * 3, 1));
            $this->tGreen   = ord(substr($result, $background * 3 + 1, 1));        
            $this->tBlue    = ord(substr($result, $background * 3 + 2, 1));
            if (isset($this->tRed) && isset($this->tGreen) && isset($this->tBlue)) {
                $this->tFlag = true;
            }           
        }
        fclose($fp);
        return;
    }
    
    /**
     * Sets output image according to type
     * @param string name
     * @param string name
     **/
    public function do_output ($output_image, $image)
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
     * Returns array of file's natural dimensions
     * @param integer 
     * @return array
     **/
    public function resizing ($maxwidth)
    {
        $width_percentage = $maxwidth / $this->size[0];
        $height_percentage = $maxwidth / $this->size[1];
        if (($this->size[0] > $maxwidth) || ($this->size[1] > $maxwidth)) {
            if ($width_percentage <= $height_percentage) {
                $this->new_size['w'] = round($width_percentage * $this->size[0]);
                $this->new_size['h'] = round($width_percentage * $this->size[1]);
            } else {
                $this->new_size['w'] = round($height_percentage * $this->size[0]);
                $this->new_size['h'] = round($height_percentage * $this->size[1]);
            }   
        } else { // is square
            $this->new_size['w'] = $this->size[0];
            $this->new_size['h'] = $this->size[1];
        }
    }
    
    /**
     * Returns array of file size
     * (square thumbnails)
     **/
    public function sys_resize ()
    {
        $this->sys_size['w'] = $this->size[0];
        $this->sys_size['h'] = $this->size[1];
        if ($this->sys_size['w'] > $this->sys_size['h']) 
        {
           $this->offset['w'] = ($this->sys_size['w'] - $this->sys_size['h'])/2;
           $this->offset['h'] = 0;
           $this->sys_size['w'] = $this->sys_size['h'];
        } elseif ($this->sys_size['h'] > $this->sys_size['w']) {
           $this->offset['w'] = 0;
           $this->offset['h'] = ($this->sys_size['h'] - $this->sys_size['w'])/2;
           $this->sys_size['h'] = $this->sys_size['w'];
        } else {
            $this->offset['w'] = 0;
            $this->offset['h'] = 0;
            $this->sys_size['w'] = $this->sys_size['h'];
        }
    }
    
    /**
     * Destroy input image
     * @param integer
     * @see this::upload_image 
     * @todo refactor
     **/
    public function sys_thumb ($maxwidth)
    {
        $this->sys_resize();
        $output_image = imagecreatetruecolor($this->sys_thumb, $this->sys_thumb);
        if ($this->tFlag === true) {
            imagecolortransparent($output_image, imagecolorallocate($output_image, 
                $this->tRed, $this->tGreen, $this->tBlue));
        }
        // png special handling rules
        if ($this->filemime === 'png') {
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
     * @param string
     * @return string
     **/
    public function checkName ($filename)
    {
        static $v = 1;
        if (file_exists($this->path . '/' . $filename . $this->type)) {
            // remove the previous version number
            $filename = preg_replace('/_v[0-9]{1,3}$/i', '', $filename);
            $v++;
            $filename = $filename . '_v' . $v;
            $filename = $this->checkName($filename);
        } else {
            $v = 1;
            return $filename;
        }
        return $filename;
    }

}
