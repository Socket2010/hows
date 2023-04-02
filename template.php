<?PHP
class template
{
        private $file = '';
        private $template = false;
    private $vars = array();
 
        function __construct($file)
        {
                $this->file = $file;
 
                if(empty($this->file) or !file_exists($this->file))
                {
                        exit('Не верный файл шаблона!');
                }
 
                $this->template = file_get_contents($this->file);
                return true;
 
        }
 
        function set($key,$var)
        {
                $this->vars[$key] = $var;
        }
 
        function parse()
        {
                if(count($this->vars) < 1) return false;
 
                foreach($this->vars as $find => $replace)
                {
                        $this->template = str_replace($find, $replace, $this->template);
                }
 
                echo $this->template;
                return true;
        }
}
 
/*
$tpl = new template(filename);  // (string) Имя файла с шаблоном
$tpl->set(find, replace);               // (string) Что менять и на что менять
$tpl-parse();                   //  Заменяем и выводим в браузер
*/