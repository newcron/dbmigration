<?php

namespace dbmigrate\application\sql;

class QuerySplitter
{
    /**
     * @param $sql
     *
     * @return array
     */
    public function split($sql)
    {
        $sql = $this->removeComments($sql);

        return array_values(array_filter(explode(PHP_EOL."".PHP_EOL, $sql)));
    }

    /**
     * @param $sql
     * @return string|string[]|null
     */
    private function removeComments($sql)
    {
        $sql = preg_replace('@--.*?\n@is', '', $sql);
        $sql = preg_replace('@\/\*.*?\*\/@is', '', $sql);
        return $sql;
    }
}
