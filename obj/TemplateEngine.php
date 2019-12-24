<?php
class TemplateEngine
{
    private $templateBuffer;  # Буффер с кодом шаблона
    private $templateVars = []; # Теги и их содержимое

    /**
     * Инициализация класса
     * @param $templateName - название шаблона
     */
    public function __construct($templateName)
    {
        if (!is_file('../editor/' . $templateName) || !$this->templateBuffer = file_get_contents('../editor/' . $templateName)) 
        {
            trigger_error("Не могу загрузить шаблон {$templateName}");
        }
    }
    /**
     * Загрузка шаблона из файла, обработка и отдача в переменную
     * @param $templateName - название шаблона
     * @param $vars - теги и из значения
     * @return string - шаблон
     */
    public function templateLoadInString($templateName, $vars)
    {
        if (!is_file('dist/tpl/' . $templateName) || !$templateBuffer = file_get_contents('dist/tpl/' . $templateName)) 
        {
            return false;
        } else 
        {
            foreach ($vars as $var => $content) 
            {
                $templateBuffer = str_replace('{' . $var . '}', $content, $templateBuffer);
            }
            return $templateBuffer;
        }
    }
    /**
     * Загрузка дополнительного шаблона в тег
     * @param $subName - название шаблона
     * @param $subTag - тег
     */
    public function templateLoadSub($subName, $subTag)
    {
        if (!$subBuffer = file_get_contents('dist/tpl/' . $subName)) 
        {
            trigger_error("Ошибка при загрузке шаблона - не могу найти файл {$subName}");
        } else 
        {
            $this->templateBuffer = str_replace('{' . $subTag . '}', $subBuffer, $this->templateBuffer);
        }
    }
    /**
     * Добавление тега
     * @param $var - название тега
     * @param $content - содержимое
     */
    public function templateSetVar($var, $content)
    {
        $this->templateVars[$var] = $content;
    }
    /**
     * Удаление тега
     * @param $var - тег
     */
    public function templateUnsetVar($var)
    {
        unset($this->templateVars[$var]);
    }
    /**
     * Сборка шаблона
     */
    public function templateCompile()
    {
        foreach ($this->templateVars as $var => $content) 
        {
            $this->templateBuffer = str_replace('{' . $var . '}', $content, $this->templateBuffer);
        }
        $this->templateBuffer = preg_replace('/{(.*)}/', '', $this->templateBuffer);
    }
    /**
     * Вывод готового шаблона
     */
    public function templateDisplay()
    {
        echo $this->templateBuffer;
    }

}