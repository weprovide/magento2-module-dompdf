<?php
namespace WeProvide\Dompdf\Controller\Result;

use Dompdf\Dompdf as PDF;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\Controller\AbstractResult;

class Dompdf extends AbstractResult
{
    public $pdf;

    protected $fileName = 'document.pdf';

    protected $attachment = 'attachment';

    protected $output;

    /**
     * Dompdf constructor.
     */
    public function __construct(PDF $domPdf)
    {
        $this->pdf = $domPdf;
    }

    /**
     * Load html
     *
     * @param $html
     */
    public function setData($html)
    {
        $this->pdf->loadHtml($html);
    }

    /**
     * Set output from $this->renderOutput() to allow multiple renders
     *
     * @param $output
     */
    public function setOutput($output) {
        $this->output = $output;
    }

    /**
     * Set filename
     *
     * @param $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Set attachment type, either 'attachment' or 'inline'
     *
     * @param $mode
     */
    public function setAttachment($mode)
    {
        $this->attachment = $mode;
    }

    /**
     * Render PDF output
     *
     * @return string
     */
    public function renderOutput()
    {
        if($this->output) {
            return $this->output;
        }

        $this->pdf->render();

        return $this->pdf->output();
    }

    /**
     * Render PDF
     *
     * @param ResponseInterface $response
     *
     * @return $this
     */
    protected function render(HttpResponseInterface $response)
    {
        $output = $this->renderOutput();

        // Below is a port of Dompdf's stream function
        // https://github.com/dompdf/dompdf/blob/af914c2cdcaea0c4b3e0efed3354f58caecf13ba/lib/Cpdf.php#L3479-L3536
        $response->setHeader('Cache-Control', 'private');
        $response->setHeader('Content-type', 'application/pdf');
        $response->setHeader('Content-Length', mb_strlen($output, '8bit'));

        $filename = $this->fileName;
        $filename = str_replace(["\n", "'"], '', basename($filename, '.pdf')) . '.pdf';

        $encoding                = mb_detect_encoding($filename);
        $fallbackfilename        = mb_convert_encoding($filename, "ISO-8859-1", $encoding);
        $encodedfallbackfilename = rawurlencode($fallbackfilename);
        $encodedfilename         = rawurlencode($filename);

        $response->setHeader(
            'Content-Disposition',
            $this->attachment . '; filename=' . $encodedfallbackfilename . "; filename*=UTF-8''" . $encodedfilename
        );

        $response->setBody($output);

        return $this;
    }
}
