<?php

namespace dbmigrate\application\sql;

class QuerySplitterTest extends \PHPUnit_Framework_TestCase
{
    public function splitQueryProvider()
    {
        return [
            'single query' => [
                'full' => "select 1",
                'expectedSplit' => [
                    'select 1',
                ],
            ],
            'single query should remove trailing semicolon' => [
                'full' => "select 1;",
                'expectedSplit' => [
                    'select 1',
                ],
            ],
            'two queries' => [
                'full' => "select 1; select 2",
                'expectedSplit' => [
                    'select 1',
                    'select 2',
                ],
            ],
            'one query multiline' => [
                'full' => "select \n 1",
                'expectedSplit' => [
                    "select \n 1",
                ],
            ],
            'two queries multiline' => [
                'full' => "select 1;\nselect     \n2",
                'expectedSplit' => [
                    "select 1",
                    "select     \n2",
                ],
            ],
            '-- comments should be removed' => [
                'full' => "select \n -- this comment should be removed\n 1",
                'expectedSplit' => [
                    "select \n  1",
                ],
            ],
            '/* */ comments should be removed' => [
                'full' => "select \n /* this \n comment should be removed\n*/ \n 1",
                'expectedSplit' => [
                    "select \n  \n 1",
                ],
            ],
        ];
    }

    /**
     * @dataProvider splitQueryProvider
     */
    public function testSplitQuery($all, $expectedSplit)
    {
        $querySplitter = new QuerySplitter();

        $actualSplit = $querySplitter->split($all);

        $this->assertEquals($expectedSplit, $actualSplit);
    }
}
