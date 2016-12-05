# Magento2 Module Dompdf

Result class for rendering a PDF using [Dompdf](https://github.com/dompdf/dompdf)

## Installation

1. `composer require weprovide/magento2-module-dompdf`
2. `bin/magento setup:upgrade`

## Usage

### Render using plain string

```php
<?php
namespace YourNameSpace\YourModule\Controller\Download;

use Magento\Framework\App\Action\Context;
use WeProvide\Dompdf\Controller\Result\DompdfFactory;
use WeProvide\Dompdf\Controller\Result\Dompdf;

class Pdf extends \Magento\Framework\App\Action\Action
{
    protected $dompdfFactory;
    protected $layoutFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param DompdfFactory $dompdfFactory
     *
     */
    public function __construct(
        Context $context,
        DompdfFactory $dompdfFactory
    ) {
        $this->dompdfFactory = $dompdfFactory;
        parent::__construct($context);
    }

    public function getHtmlForPdf() {
        return '
        <html>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>';
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var Dompdf $response */
        $response = $this->dompdfFactory->create();
        $response->setData($this->getHtmlForPdf());

        return $response;
    }
}
```

### Render using block and template

Create controller

```php
<?php
namespace YourNameSpace\YourModule\Controller\Download;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use WeProvide\Dompdf\Controller\Result\Dompdf;
use WeProvide\Dompdf\Controller\Result\DompdfFactory;

class Pdf extends \Magento\Framework\App\Action\Action
{
    protected $dompdfFactory;
    protected $layoutFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param DompdfFactory $dompdfFactory
     *
     */
    public function __construct(
        Context $context,
        DompdfFactory $dompdfFactory,
        LayoutFactory $layoutFactory
    ) {
        $this->dompdfFactory = $dompdfFactory;
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
    }

    public function getHtmlForPdf()
    {
        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $this->layoutFactory->create()->createBlock('Magento\Framework\View\Element\Template');
        $block->setTemplate('YourNameSpace_YourModule::pdf.phtml');

        $data = [
            'foo' => 'bar'
        ];
        $block->setData($data);

        return $block->toHtml();
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var Dompdf $response */
        $response = $this->dompdfFactory->create();
        $response->setData($this->getHtmlForPdf());

        return $response;
    }
}
```

Create template file in `YourNameSpace/YourModule/view/frontend/template`

## Api

All public methods are listed below:

`public $pdf` => Dompdf instance. Useful for setting custom options like orientation and paper size. List of all options can be found [here](https://github.com/dompdf/dompdf/wiki/Usage#method-summary).

`public function setData($html)` => Equal to `$dompdf->loadHtml($html)`.

`public function setFileName($fileName)` => Set output fileName.

`public function setAttachment($mode)` => Mode can either be `attachment` (download) or `inline` (no download).

`public function renderOutput()` => Equal to running `$dompdf->render()` & `$dompdf->output()`. Will cache the output since Dompdf doesn't support rendering twice

For reference also check [the code](Controller/Result/Dompdf.php)
