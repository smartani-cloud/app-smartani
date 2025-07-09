<<<<<<< HEAD
<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Mimetype extends WriterPart
{
    /**
     * Write mimetype to plain text format.
     *
     * @param Spreadsheet $spreadsheet
     *
     * @return string XML Output
     */
    public function write(?Spreadsheet $spreadsheet = null)
    {
        return 'application/vnd.oasis.opendocument.spreadsheet';
    }
}
=======
<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Mimetype extends WriterPart
{
    /**
     * Write mimetype to plain text format.
     *
     * @param Spreadsheet $spreadsheet
     *
     * @return string XML Output
     */
    public function write(?Spreadsheet $spreadsheet = null)
    {
        return 'application/vnd.oasis.opendocument.spreadsheet';
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
