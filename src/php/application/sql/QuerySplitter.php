<?php

namespace dbmigrate\application\sql;

class QuerySplitter
{
    public function split($sql, $delimiter = null)
    {
        $sql = $this->removeComments($sql);


        $splitQueries = [];

        if(!$delimiter) {
            $delimiter = $this->autodetectDelimiter($sql);
        }

        $queries = explode($delimiter, $sql);

        foreach ($queries as $query) {
            $query = trim($query);
            $query = trim($query, $delimiter);

            if (!empty($query)) {
                $splitQueries[] = $query;
            }
        }

        return $splitQueries;

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

    /**
     * @return string
     */
    private function autodetectDelimiter($sql)
    {
        // TODO: Autodetect delimiter
        $delimiter = ';';

        return $delimiter;
    }


}
