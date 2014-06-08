<?php
/**
* 
* Version 1.0.1
* 
* Author: Roberto Serra - obi.serra@gmail.com
* 
*
* This file handle the image-upload using beforeSave and afterSave behaviour
*
* Configuration:
* 
* @imagesColumns: array
* @paths: array/string; {ID} will be replaced by the ID of the actual model
* @rename: array; {filename} will be replaced by the name of the uploaded file
* 
*
*
* -- Usage: --
*
*
* public function behaviors(){
*      return array(
*          'ImagesHandler'=>array(
*              'class'=>'application.components.ImagesHandler',
*              'imagesColumns'=>array('imageFieldName'),
*              'paths'=>'/../images/directoryName/{fieldName}',
*              'rename'=>array('{filename}_{ID}'), // {filename} and {ID} are automatically replaced by the relative values
*              'resize'=>array(array('field'=>'thumb','path'=>'/../images/galleries/{id_cartella}','size'=>'300x200'))
*          ),
*      );
*  }
* 
**/
class ImagesHandler extends CActiveRecordBehavior{
    public $imagesColumns = array();
    public $paths = array();
    public $rename = array();
    public $resize = array();
    private $oldImage;

    private $newImages;

    public function checkPath(){
        if(is_string($this->paths)){
            $paths = array();
                foreach( $this->imagesColumns as $k=>$image ){    
                    $paths[$k] = $this->paths;
                }
            $this->paths = $paths;
            }
    }
        
    public function saveOldImage(){
        foreach( $this->imagesColumns as $k=>$image ){
            $this->oldImage[$k] = $this->Owner->{$image};
        }
    }

    private function renameIt($fileSingle,$k,$image){
        if($this->rename[$k]){
            $fileName = str_replace('.'.$fileSingle->extensionName, '', $fileSingle->name);
            $this->rename[$k] = str_replace('{filename}', $fileName, $this->rename[$k]);
            $this->rename[$k] = $this->renameSmart($this->rename[$k]);
            $this->Owner->{$image} = $this->rename[$k].'.'.$fileSingle->extensionName;
        }
    }

    private function renameSmart($subject){
        $pattern = '/{[\w]*}/';
        preg_match($pattern, $subject, $matches);
        foreach ($matches as $m) {
            $orM = $m;
            $m = str_replace('{', '', $m);
            $m = str_replace('}', '', $m);
            if(isset($this->Owner->$m)){
                $subject = str_replace($orM, $this->Owner->$m, $subject);
            } else if($m === 'timestamp'){
                $subject = str_replace($orM, time(), $subject);
            }
        }
        return $subject;
    }

    public function beforeSave($event)
    {
        //$this->saveOldImage();
        $this->checkPath();
        foreach( $this->imagesColumns as $k=>$image )
        {
            $i = 0;
            $fileSingle = CUploadedFile::getInstance($this->Owner,$image);
            if(!empty($fileSingle)){
                if(!is_object($fileSingle)){
                    do{
                        $file = CUploadedFile::getInstance($this->Owner,'['.$i.']'.$image);
                        if(is_object($file)){
                            $this->renameIt($fileSingle,$k,$image);
                            $this->newImages[$k] = $file;
                        }                        
                        $i++;
                    } while($i<10);
                }
                else{
                    $this->renameIt($fileSingle,$k,$image);
                    $this->newImages[$k] = $fileSingle;
                }
            }
        }
        return parent::beforeSave($event);
    }
    public function afterSave($event){
        foreach( $this->imagesColumns as $k=>$image ){
            if(isset($this->newImages[$k])){
                $fileSingle = $this->newImages[$k];
                $this->paths[$k] = $this->renameSmart($this->paths[$k]);
                $oldName  = $this->Owner->{$image};
                $this->renameIt($fileSingle,$k,$image);
                if($oldName !== $this->Owner->{$image}){
                    $model = $this->Owner->findByPk($this->Owner->ID);
                    $model->save();
                }
                if(!is_dir(Yii::app()->basePath.$this->paths[$k].'/')) mkdir(Yii::app()->basePath.$this->paths[$k].'/', 0755);
                //echo 'SAVING '.Yii::app()->basePath.$this->paths[$k].'/'.$this->Owner->{$image}.'<br>';
                $fileSingle->saveAs(Yii::app()->basePath.$this->paths[$k].'/'.$this->Owner->{$image});
                if(!empty($this->resize)){
                    $this->resize(Yii::app()->basePath.$this->paths[$k].'/'.$this->Owner->{$image});
                }
            }
        }

        if(is_array($this->oldImage)){
            $this->checkPath();
            foreach($this->oldImage as $k =>$img){
                $this->paths[$k] = $this->renameSmart($this->paths[$k]);
                if(!empty($this->Owner->{$this->imagesColumns[$k]})){
                    if($this->Owner->{$this->imagesColumns[$k]} != $img ){
                        if(is_file(Yii::app()->basePath.$this->paths[$k].'/'.$img)){
                            unlink(Yii::app()->basePath.$this->paths[$k].'/'.$img);
                        }        
                    }
                }
            }    
        }
        return parent::afterSave($event);
    }

    public function resize($filename){
        list($width, $height) = getimagesize($filename);
        $thumb = imagecreatetruecolor($this->resize['width'], $this->resize['height']);
        $source = imagecreatefromjpeg($filename);
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $this->resize['width'], $this->resize['height'], $width, $height);
        imagejpeg($thumb, $filename, 100);

    }
    public function afterFind($event){
        $this->saveOldImage();
    }
    public function beforeDelete($event){
        $this->checkPath();
        foreach( $this->imagesColumns as $k=>$image ){
            $this->paths[$k] = $this->renameSmart($this->paths[$k]);
            if(is_file(Yii::app()->basePath.$this->paths[$k].'/'.$this->Owner->{$image}))
                unlink(Yii::app()->basePath.$this->paths[$k].'/'.$this->Owner->{$image});
            @rmdir(Yii::app()->basePath.$this->paths[$k].'/');
        }
        return parent::beforeDelete($event);
    }

    public function beforeValidate($event){
        foreach( $this->imagesColumns as $k=>$image ){
            $file = CUploadedFile::getInstance($this->Owner,$image);
            if(is_object($file)){
                $this->Owner->{$image} = str_replace('.'.$file->extensionName, '', $file->name);
            }
            else if(!empty($this->oldImage[$k])){
                $this->Owner->{$image} = $this->oldImage[$k];
            }
        }   
        return parent::beforeValidate($event);
    }
}
?>