<<<<<<< HEAD
<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class BaseParserClass
{
    protected static function boolean($value)
    {
        if (is_object($value)) {
            $value = (string) $value;
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return $value === strtolower('true');
    }
}
=======
<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class BaseParserClass
{
    protected static function boolean($value)
    {
        if (is_object($value)) {
            $value = (string) $value;
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return $value === strtolower('true');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
