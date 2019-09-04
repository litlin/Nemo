<?php
namespace BaseServices\Prepare;

/**
 *
 * @author lin
 *        
 */
class Prepare
{

    public static function p($v, $attr = ""): string
    {
        return "<p $attr>$v</p> ";
    }

    public static function cla($v): string
    {
        return 'class="' . $v . '" ';
    }

    public static function id($v): string
    {
        return 'id="' . $v . '" ';
    }

    public static function href($v): string
    {
        return 'href="' . $v . '" ';
    }

    public static function div($v, $attr = ""): string
    {
        return "<div $attr>$v</div> ";
    }

    public static function span($v, $attr = ""): string
    {
        return "<span $attr>$v</span> ";
    }

    public static function a($v, $attr = ""): string
    {
        return "<a $attr>$v</a> ";
    }

    public static function b($v, $attr = ""): string
    {
        return "<b $attr>$v</b> ";
    }

    public static function blockquote($v, $attr = ""): string
    {
        return "<blockquote $attr>$v</blockquote> ";
    }

    public static function attr($name, $value): string
    {
        return $name . '="' . $value . '" ';
    }

    public static function strong($v, $attr = ""): string
    {
        return "<strong $attr>$v</strong> ";
    }

    public static function pair($name, $value, $attr = ""): string
    {
        return "<$name $attr>$value</$name> ";
    }

    public static function table(\ArrayIterator $table, $attr = ""): string
    {
        $data = "<table $attr>";
        while ($table->valid()) {
            foreach ($table->current() as $row) {
                $key=array_key_first($row);
                if ($key!==0) {
                    $data .= "<tr $key>";  
                    $row= $row[$key];
                }else{
                    $data .= "<tr>";
                }
                foreach ($row as $cell) {
                    if (is_array($cell)) {
                        $data .= "<" . $table->key() . (" " . $cell[1] ?? "") . ">" . ($cell[0] ?? $cell) . "</" . $table->key() . ">";
                    } else {
                        $data .= "<" . $table->key() . ">" . $cell . "</" . $table->key() . ">";
                    }
                }
                $data .= "</tr>";
            }
            $table->next();
        }
        $data .= "</table>";
        return $data;
    }
}

