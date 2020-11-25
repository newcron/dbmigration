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
        $sql = $this->removeCommentsAndSpaces($sql);

        return array_values(array_filter(explode(PHP_EOL."".PHP_EOL, $sql)));
    }

    /**
     * @param $sql
     * @return string|string[]|null
     */
    private function removeCommentsAndSpaces($sql)
    {
        $sql = preg_replace('@\/\*.*?\*\/@mis', '', $sql);
        /* Trim spaces on blank lines */
        $sql = preg_replace('@^\s*\n@mis', "\n", $sql);
        return $sql;
    }
}
