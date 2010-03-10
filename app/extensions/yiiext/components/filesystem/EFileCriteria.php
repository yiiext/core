<?php
/**
 * EFileCriteria class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class EFileCriteria {
    public $dir; // директория поиска
    public $base; // родительская директория, будет отсекаться из названия файла
    public $fileTypes; // массив расширений
    public $exclude; // названия файлов исключающиеся
    public $level; // уровень вложености
    public $limit; // лимит количество файлов
    public $sort; // сортировка, пока не думал как сделать как булевое или ключевый слова типа desc, asc и еще пару мыслей как можно сортировать файлы
}