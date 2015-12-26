<?php

/**
 * Created by PhpStorm.
 * User: YEMASKY
 * Date: 2015/11/28
 * Time: 18:32
 */

class Template{
    //通过assign函数传入的变量临时存放数组
    private $templateVar = array();
    //模板目录
    private $templateDir = '';
    //编译目录
    private $templateCompileDir = '';

    private $fileName = '';
    /**
     * 构造函数
     * @param string $templateDir 模板目录
     * @param string $templateComplieDir 模板编译目录
     */
    public function __construct($templateDir,$templateComplieDir){
        $this->templateDir = $templateDir;
        $this->templateCompileDir = $templateComplieDir;
    }
    /**
     * 显示模板
     * @param string $fileName 模板文件名
     */
    public function display($fileName){
        $this->fileName = $fileName;
        if(file_exists($this->templateDir.'/'.$this->fileName)){
            $compileFileName = $this->templateCompileDir.'/'.$this->file_safe_name().'.php';
            if(!file_exists($compileFileName) || filemtime($compileFileName)< filemtime($this->templateDir.'/'.$this->fileName)){
                $this->del_old_file();
                $this->compile();
            }
            extract($this->templateVar);
            include $compileFileName;
        }else{
            throw new Exception('the template file '.$this->fileName.' does not exist!!');
        }
    }
    /**
     * 获取编译文件名
     */
    private function get_compile_file(){
        $compileFile = explode('.',$this->fileName);
        unset($compileFile[count($compileFile)-1]);
        return implode('.',$compileFile);
    }
    /**
     * 编译
     */
    private function compile(){
        $fileHandle = fopen($this->templateDir.'/'.$this->fileName, 'r');
        while(!feof($fileHandle)){
            $fileContent = fread($fileHandle,1024);
        }
        fclose($fileHandle);
        $fileContent = $this->template_replace($fileContent);
        //$compileFile = $this->get_compile_file($fileName);
        $fileHandle = fopen($this->templateCompileDir.$this->file_safe_name().'.php','w');
        //echo $this->templateCompileDir.$this->file_safe_name().'.php';
        if($fileHandle){
            fwrite($fileHandle, $fileContent);
            fclose($fileHandle);
        }else{
            throw new Exception('Sorry,Compile dir can not write!');
        }
    }
    /**
     * 模板传值
     * @param string $valueName 模板中使用的变量名
     * @param $value 变量值
     */
    public function assign($valueName,$value){
        $this->templateVar[$valueName] = $value;
    }

    /**
     * 模板正则替换
     * @param string $content 替换内容
     * @return string 替换过后的内容
     */
    private function template_replace($content){
        $orginArray = array(
            '/<%loop\s+\$(\w+)\s+\$(\w+)%>/s',
            '/<%loop\s+\$(\w+)\s+\$(\w+)\s+\$(\w+)%>/s',
            '/<%elseloop%>(.+?)<%\/loop%>/s',
            '/<%endloop%>/s',
            '/<%if\s+\((.+?)\)%>/s',
            '/<%endif%>/s',
            '/<%elseif\s+\((.+?)\)%>/s',
            '/<%else%>/s',
            '/<%P:(.+?)%>/s',
            '/<%C:(\w+)%>/s',
            '/<%include:(.+?)%>/s',
            '/<%F:(.+?)%>/s',
            '/<%EF:(.+?)%>/s',
            '/<%([a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)%>/s',
        );

        $changeArray = array(
            '<?php if(!empty($$1)&&is_array($$1)){$countLoop = 1;foreach($$1 as $$2){$countLoop++;?>',
            '<?php if(!empty($$1)&&is_array($$1)){$countLoop = 1;foreach($$1 as $$2=>$$3){$countLoop++;?>',
            '<?php }if(!empty($countLoop))$countLoop--;}else{?>$1<?php }?>',
            '<?php }if(!empty($countLoop))$countLoop--;}?>',
            '<?php if($1){?>',
            '<?php }?>',
            '<?php }elseif($1){?>',
            '<?php }else{?>',
            '<?php $1?>',
            '<?php echo $1;?>',
            '<?php include_once "'.$this->templateDir.'/$1";?>',
            '<?php $1;?>',
            '<?php echo $1;?>',
            '<?php echo $$1;?>',
        );
        return $repContent=preg_replace($orginArray,$changeArray,$content);
    }
    /**
     * 删除旧文件
     */
    private function del_old_file(){
        $compileFile = $this->get_compile_file($this->fileName);
        $files = glob($this->templateCompileDir.'/'.$compileFile.'*.php');
        // print_r($files);
        if($files){
            @unlink($files[0]);
        }
    }
    /**
     * 编译文件名安全处理方法
     * @return string 返回编译文件名
     */
    private function file_safe_name(){
        $compileFile = $this->get_compile_file($this->fileName);
        return $compileFile.filemtime($this->templateDir.'/'.$this->fileName);
    }

}
