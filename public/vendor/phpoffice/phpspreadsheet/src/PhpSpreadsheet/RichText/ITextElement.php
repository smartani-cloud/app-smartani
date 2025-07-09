<<<<<<< HEAD
<?php

namespace PhpOffice\PhpSpreadsheet\RichText;

interface ITextElement
{
    /**
     * Get text.
     *
     * @return string Text
     */
    public function getText();

    /**
     * Set text.
     *
     * @param $text string Text
     *
     * @return ITextElement
     */
    public function setText($text);

    /**
     * Get font.
     *
     * @return null|\PhpOffice\PhpSpreadsheet\Style\Font
     */
    public function getFont();

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode();
}
=======
<?php

namespace PhpOffice\PhpSpreadsheet\RichText;

interface ITextElement
{
    /**
     * Get text.
     *
     * @return string Text
     */
    public function getText();

    /**
     * Set text.
     *
     * @param $text string Text
     *
     * @return ITextElement
     */
    public function setText($text);

    /**
     * Get font.
     *
     * @return null|\PhpOffice\PhpSpreadsheet\Style\Font
     */
    public function getFont();

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode();
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
