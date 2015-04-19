<?php
/**
 * PHP Version 5
 *
 * @category  H24
 * @package   
 * @author    "Yury Kozyrev" <yury.kozyrev@home24.de>
 * @copyright 2015 Home24 GmbH
 * @license   Proprietary license.
 * @link      http://www.home24.de
 */

namespace App\Components;


use Illuminate\Cache\CacheManager;
use Illuminate\Redis\Database;

class StorageAdapter
{
    /**
     * @var Database
     */
    protected $redis;

    public function __construct(CacheManager $redis)
    {
        $this->redis = $redis;
    }

    public function set($id, $data)
    {
        $this->redis->forever($this->getKey($id), $data);
    }

    public function get($id)
    {
        return $this->redis->get($this->getKey($id));
    }

    protected function getKey($id)
    {
        return sprintf("gde_re_s_%s", $id);
    }
}