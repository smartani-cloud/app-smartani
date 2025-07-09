<<<<<<< HEAD
<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher;

class DgContainer
{
    /**
     * Drawing index, 1-based.
     *
     * @var int
     */
    private $dgId;

    /**
     * Last shape index in this drawing.
     *
     * @var int
     */
    private $lastSpId;

    private $spgrContainer;

    public function getDgId()
    {
        return $this->dgId;
    }

    public function setDgId($value): void
    {
        $this->dgId = $value;
    }

    public function getLastSpId()
    {
        return $this->lastSpId;
    }

    public function setLastSpId($value): void
    {
        $this->lastSpId = $value;
    }

    public function getSpgrContainer()
    {
        return $this->spgrContainer;
    }

    public function setSpgrContainer($spgrContainer)
    {
        return $this->spgrContainer = $spgrContainer;
    }
}
=======
<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher;

class DgContainer
{
    /**
     * Drawing index, 1-based.
     *
     * @var int
     */
    private $dgId;

    /**
     * Last shape index in this drawing.
     *
     * @var int
     */
    private $lastSpId;

    private $spgrContainer;

    public function getDgId()
    {
        return $this->dgId;
    }

    public function setDgId($value): void
    {
        $this->dgId = $value;
    }

    public function getLastSpId()
    {
        return $this->lastSpId;
    }

    public function setLastSpId($value): void
    {
        $this->lastSpId = $value;
    }

    public function getSpgrContainer()
    {
        return $this->spgrContainer;
    }

    public function setSpgrContainer($spgrContainer)
    {
        return $this->spgrContainer = $spgrContainer;
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
