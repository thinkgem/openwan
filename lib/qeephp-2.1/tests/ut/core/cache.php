<?php
// $Id: cache.php 2188 2009-02-04 06:47:51Z dualface $

/**
 * 测试 QeePHP 核心类缓存接口
 *
 * cache()
 * writeCache()
 * cleanCache()
 */
class UT_Core_Cache extends QTest_UnitTest_Abstract
{

    /**
     * 测试缓存的读写
     *
     * 使用 QCache_Memory
     */
    function testCache()
    {
        $backend = 'QCache_Memory';
        $id = 'test_cache_id1';

        $data = Q::cache($id, null, $backend);
        $this->assertEmpty($data);

        $data = array('value');
        Q::writeCache($id, $data, null, $backend);

        $data2 = Q::cache($id, null, $backend);
        $this->assertEquals($data, $data2);

        Q::cleanCache($id, null, $backend);
        $data2 = Q::cache($id, null, $backend);
        $this->assertEmpty($data2);
    }

}


