<?php

namespace App\Libs;

use Image;

class ImageFile{
    private $file;
    private $game;
    private $file_name;
    public $extension;
    private $path;
    
    public function __construct($name, $file, $game){
        $this->file = $file;
        $this->game = $game;
        $this->extension = $file->clientExtension();
        $this->file_name = $name . '.' . $this->extension;
        $this->path = base_path() . config($this->game . '.raw_images') . "/" . $this->file_name;
    }
    /**
    * Take an uploaded image file and stores it into the proper
    * image folder.
    * 
    * @param file Uploaded image file
    * @param String $destination Path to destination folder
    * @param String $name Name to be assigned to the resulting file.
    * 
    * @return Bool Wheter resulting file was created or not.
    */
    public function store(){
        try{
            $img = Image::make($this->file);
            $img->save($this->path);
            $final_path = base_path() . config($this->game . '.final_images') . "/" . $this->file_name;
            $this->resize($this->path, $final_path, config('images.size_array'));
            $status = 'ok';
            $message = '';
        }
        catch(Exception $e){
            $status = 'error';
            $message = $e->getMessage();
        }
        return (object)array(
            'status' => $status,
            'message' => $message,
        );
    }
    
    public function remove(){
        unlink($this->path);
    }
    
    /**
    * Takes an stored image updates its size and saves to 
    * the specified path.
    * 
    * @param String $source Path to the stored image.
    * @param String $destination Path to destination folder.
    * @param Array Array indicating desired width and height for
    *   resulting image.
    */
    public function resize($source, $destination, $size){
        try{
            $img = Image::make($source);
            $img->resize($size[0], $size[1]);
            $img->save($destination);
            $res = file_exists($destination);
        }
        catch(Exception $e){
            $res = false;
        }
        return $res;
    }

    public function set_image_name($name){
        $this->file_name = $name . '.' . $this->extension;
    }
    
    public function get_image_name(){
        return $this->file_name;
    }
}