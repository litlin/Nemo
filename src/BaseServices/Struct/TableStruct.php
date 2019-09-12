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
        $key = array_key_first($th);
        if ($key !== 0) {
            $th = $th[$key];
        }
        // array_walk($th, function (&$each) {
        // if (! is_array($each)) {
        // $each = [
        // $each
        // ];
        // }
        // });
        // 判断作为标题的行每列值是否为相应的基本类型字符或者数字
        if (array_walk($th, function ($row) use (&$count) {
            if (is_string($row) || is_numeric($row) || is_string($row[0]) || is_numeric($row[0])) {} else {
                $count ++;
            }
        }) && $count === 0) {
            $data['th'] = [
                $key !== 0 ? [
                    $key => $th
                ] : $th
            ];
        } else {
            throw new \Exception("th行值不符合要求");
        }

        $deep = function ($input) {
            $result = array(
                0
            );
            try {
                $iteriter = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($input));
                while ($iteriter->valid()) {
                    $result[] = $iteriter->getDepth() + 1;
                    $iteriter->next();
                }
            } catch (\Exception $e) {}
            return max($result);
        };

        if (array_walk_recursive($td, function ($v, $k) use (&$count) {
            if (is_numeric($k)) {
                if (is_string($v) || is_numeric($v)) {} else {
                    $count ++;
                }
            }
        }) && $count === 0 && ($deep($td) < 4 || array_key_first($td) === 0)) {
            $data['td'] = $td;
        } else {
            throw new \Exception("输入数据不符合要求");
        }

        // if (array_walk($td, function ($row) use (&$count) {
        // if (is_array($row)) {
        // $count ++;
        // }
        // }) && $count === 0 && count($td) === count($th)) {
        // $data['td'] = [
        // $td
        // ];
        // } elseif ($count === count($td) && count($th) === count($td[0])) {
        // $data['td'] = $td;
        // } else {
        // throw new \Exception("输入数据不符合要求");
        // }
        return new \ArrayIterator($data);
    }
}

// try {
//     $startMem = memory_get_usage();
//     $st = microtime(TRUE);
//     $data = TableStruct::format(array(
//         'class="tabletitle"' => array(
//                 "日期",   
//             [
//                 "时间",
//                 'class="time"'
//             ]
//         )
//     ), [
//         // 'cd'=>[[1,'a=b'],"245",345]
//         array(
//             'class ="tdabc"' => [
//                 date('Y-m-d'),
//                 [
//                     date('H:i:s'),
//                     'id="time"'
//                 ]
//             ]
//         ),
//         array(
//             [
//                 date('Y-m-d'),
//                 'class="date"'
//             ],
//             date('H:i:s', time() - 10 * 60 * 60) . "a:b:c:d:e:f:g:h{{a}}[{b}]"
//         ),
//         [
//             'class ="tdabc"' => [
//                 time(),
//                 date('H:i:s', time() - 7 * 60 * 60)
//             ]
//         ]
//     ]);
//     while ($data->valid()) {

//         foreach ($data->current() as $row) {
//             $key = array_key_first($row);
//             if ($key !== 0) {
//                 echo "<tr $key>";
//                 $row = $row[$key];
//             } else {
//                 echo "<tr>";
//             }

//             foreach ($row as $cell) {
//                 if (is_array($cell)) {
//                     echo "<" . $data->key() . " " . ($cell[1] ?? "") . ">" . ($cell[0] ?? $cell) . "</" . $data->key() . ">";
//                 } else {
//                     echo "<" . $data->key() . ">" . $cell . "</" . $data->key() . ">";
//                 }
//             }
//             echo "</tr>\n";
//         }
//         $data->next();
//     }
//     printf("内存使用%d,时间使用%d", memory_get_usage() - $startMem, microtime(TRUE) - $st);
// } catch (\Exception $e) {
//     var_dump($e->getMessage());
// }

