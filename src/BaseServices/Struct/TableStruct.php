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
        // 判断作为标题的行每列值是否为相应的基本类型字符或者数字
        if (array_walk($th, function ($row) use (&$count) {
            if (is_string($row) || is_numeric($row)) {} else {
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
//     $data = TableStruct::format([
//         '日期',
//         'B'
//     ], [
//         [
//             2,
//             3
//         ],
//         [
//             '4',
//             '5'
//         ],
//         [
//             '6',
//             '7'
//         ]
//     ]);
//     while ($data->valid()) {
//         echo $data->key();
//         foreach ($data->current() as $row) {
//             var_dump($row);
//         }
//         $data->next();
//     }
// } catch (\Exception $e) {
//     var_dump($e->getMessage());
// }

