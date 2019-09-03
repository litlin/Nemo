<?php
namespace BaseServices\Struct;

/**
 * 规范用于传递Table数据的结构
 *
 * @author lin
 *        
 */
class TableStruct
{

    /**
     * 格式化数据
     *
     * @param array $th
     *            TH行数据
     * @param array $td
     *            其余表格数据
     * @throws \Exception
     * @return \ArrayIterator
     */
    public static function format(array $th, array $td): \ArrayIterator
    {
        $data = [
            'th' => [],
            'td' => []
        ];
        $count = 0;
        array_walk($th, function (&$each) {
            if (! is_array($each)) {
                $each = [
                    $each
                ];
            }
        });
        // 判断作为标题的行每列值是否为相应的基本类型字符或者数字
        if (array_walk($th, function ($row) use (&$count) {
            if (is_string($row[0]) || is_numeric($row[0])) {} else {
                $count ++;
            }
        }) && $count === 0) {
            $data['th'] = [
                $th
            ];
        } else {
            throw new \Exception("th行值不符合要求");
        }
        $count = 0;
        if (array_walk($td, function ($row) use (&$count) {
            if (is_array($row)) {
                $count ++;
            }
        }) && $count === 0 && count($td) === count($th)) {
            $data['td'] = [
                $td
            ];
        } elseif ($count === count($td) && count($th) === count($td[0])) {
            $data['td'] = $td;
        } else {
            throw new \Exception("输入数据不符合要求");
        }
        return new \ArrayIterator($data);
    }
}

// try {
//     $data = TableStruct::format(array(
//         ["日期",'id="date"'],
//         [ "时间",'class="time"']
//     ), [
//         array(
//             date('Y-m-d'),
//             date('H:i:s')
//         ),
//         array(
//             [date('Y-m-d'),'class="date"'],
//             date('H:i:s', time() - 10 * 60 * 60)
//         )
//     ]);
// //     ini_set("error_reporting", "E_ALL & ~E_NOTICE");
//     while ($data->valid()) {
//         foreach ($data->current() as $row) {
//             foreach ($row as $cell) {
//                 if (is_array($cell)) {
//                     echo "<" . $data->key() . " " . ($cell[1] ?? "") . ">" . ($cell[0] ?? $cell) . "</" . $data->key() . ">\n";
//                 }else {
//                     echo "<" . $data->key() .  ">" . $cell . "</" . $data->key() . ">\n";
//                 }
               
//             }
//         }
//         $data->next();
//     }
// } catch (\Exception $e) {
//     var_dump($e->getMessage());
// }

