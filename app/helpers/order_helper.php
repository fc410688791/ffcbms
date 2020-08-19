<?php
defined('BASEPATH') OR exit('不允许直接访问');

if ( ! function_exists('generate_order_number'))
{
    /**
     * [generate_order_number 生成商户订单号: 订单前缀（1位数字）+3位毫秒+14位时间+9位随机数]
     *
     * @Author leeprince:2019-01-22T12:03:20+0800
     * @param  [type]                             $prefix [description]
     * @return [type]                                     [description]
     */
    function generateOrderNumber($k = 'WXAPPUU'): string
    {
        $prefix = getOrderNumberPrefix($k);
        if ( ! $prefix) {
            return null;
        }
        
        if ( ! envIsProduct()) {
            $prefix = 'test'.$prefix;
        }
        
        $time = [];
        $time[] = $prefix;
        $time[] = substr(microtime(), 2, 3);
        $time[] = date('YmdHis');
        $time[] = random_int(100000000, 999999999);
        return join('', $time);
    }
}

if ( ! function_exists('getOrderNumberPrefix'))
{
    /**
     * [getOrderNumberPrefix 生成密码的前缀]
     *
     * @Author leeprince:2019-01-22T14:16:14+0800
     * @param  [type]                             $k [description]
     * @return [type]                                [description]
     */
    function getOrderNumberPrefix($k): string
    {
        switch ($k) {
            case 'WXAPPUU':
                return 1;
                break;
            case 'WXPARTNER':
                return 2;
                break;
            case 'userRefund':
                return 3;
                break;
                
            default:
                return null;
                break;
        }
    }
}