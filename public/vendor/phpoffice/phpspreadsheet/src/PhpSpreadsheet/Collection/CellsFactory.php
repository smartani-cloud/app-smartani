<<<<<<< HEAD
<?php

namespace PhpOffice\PhpSpreadsheet\Collection;

use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class CellsFactory
{
    /**
     * Initialise the cache storage.
     *
     * @param Worksheet $parent Enable cell caching for this worksheet
     *
     * @return Cells
     * */
    public static function getInstance(Worksheet $parent)
    {
        return new Cells($parent, Settings::getCache());
    }
}
=======
<?php

namespace PhpOffice\PhpSpreadsheet\Collection;

use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class CellsFactory
{
    /**
     * Initialise the cache storage.
     *
     * @param Worksheet $parent Enable cell caching for this worksheet
     *
     * @return Cells
     * */
    public static function getInstance(Worksheet $parent)
    {
        return new Cells($parent, Settings::getCache());
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
