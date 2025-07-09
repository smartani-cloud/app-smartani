<<<<<<< HEAD
<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

class DefaultReadFilter implements IReadFilter
{
    /**
     * Should this cell be read?
     *
     * @param string $column Column address (as a string value like "A", or "IV")
     * @param int $row Row number
     * @param string $worksheetName Optional worksheet name
     *
     * @return bool
     */
    public function readCell($column, $row, $worksheetName = '')
    {
        return true;
    }
}
=======
<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

class DefaultReadFilter implements IReadFilter
{
    /**
     * Should this cell be read?
     *
     * @param string $column Column address (as a string value like "A", or "IV")
     * @param int $row Row number
     * @param string $worksheetName Optional worksheet name
     *
     * @return bool
     */
    public function readCell($column, $row, $worksheetName = '')
    {
        return true;
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
