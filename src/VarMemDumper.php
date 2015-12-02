<?php

namespace VarProfile;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class VarMemDumper
{
    public static function d($varName = '', $maxDepth = 16, $maxItemsPerDepth = 128)
    {
        if (!function_exists('varprofile')) {
            throw new \Exception('varprofile extension needed! Please install it first. https://github.com/yangxikun/phpext-learning.git');
        }
        $var = varprofile();
        if (!empty($varName)) {
            $varName = explode('.', $varName);
            $count = count($varName);
            $idx = 0;
            while ($count) {
                if (isset($var[$varName[$idx]])) {
                    $var = $var[$varName[$idx]];
                    $count--;
                    $idx++;
                } else {
                    $var = [];
                    break;
                }
            }
            $var = [implode('.', $varName) => $var];
        }
        $varMemArr = [];
        foreach ($var as $key => $values) {
            $t = [];
            $tmp = [
                'k' => $key,
                'v' => self::aggregate($key, $values, $t)
            ];
            usort($t,  function ($a, $b) {
                if ($a['v'] == $b['v']) {
                    return 0;
                }
                return ($a['v'] < $b['v']) ? 1 : -1;
            });
            $tmp['s'] = $t; 
            $varMemArr[] = $tmp;
        }
        usort($varMemArr,  function ($a, $b) {
            if ($a['v'] == $b['v']) {
                return 0;
            }
            return ($a['v'] < $b['v']) ? 1 : -1;
        });

        $dumper = 'cli' === PHP_SAPI ? new CliDumper : new HtmlDumper;
        $dumper->dump(
            (new VarCloner())->cloneVar($varMemArr)
                ->withMaxDepth($maxDepth)->withMaxItemsPerDepth($maxItemsPerDepth)
        );
    }

    private static function aggregate($key, $values, &$t)
    {
        $sum = 0;
        foreach ($values as $k => $v) {
            if (is_array($v)) {
                $sub_arr = []; 
                $tmp = [
                    'k' => $key . '.' . $k,
                    'v' => self::aggregate($key . '.' . $k, $v, $sub_arr)
                ];
                usort($sub_arr, function ($a, $b) {
                    if ($a['v'] == $b['v']) {
                        return 0;
                    }
                    return ($a['v'] < $b['v']) ? 1 : -1;
                });
                $tmp['s'] = $sub_arr;
                $t[] = $tmp;
                $sum += $tmp['v'];
                continue;
            }
            $sum += $v;
            $t[] = [
                'k' => $key . '.' . $k,
                'v' => $v
            ];
        }
        return $sum;
    }
}