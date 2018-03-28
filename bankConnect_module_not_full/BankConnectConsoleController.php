<?php
namespace BankGateway\Controller\Console;

use BankGateway\Entity\BankGateway;
use BankGateway\Repository\BankGatewayRepository;
use BankGateway\Service\BankConnectService;

use Common\Tool\FilesTool;
use PaymentOrders\Entity\PaymentOrder;
use PaymentOrders\Repository\PaymentOrdersRepository;
use Payments\Entity\PaymentFileImport;
use Payments\Controller\SydbankGatewayController;

use Common\Controller\AbstractConsoleController;

use Symfony\Component\Filesystem\Filesystem;

class BankConnectConsoleController extends AbstractConsoleController
{
    /**
     * @return BankGatewayRepository
     */
    private function getBankGatewayRepository()
    {
        /**
         * @var BankGatewayRepository $entityRepository
         */
        $entityRepository = $this->getEntityManager()->getRepository(BankGateway::class);

        return $entityRepository;
    }

    public function importPaymentOrdersAction()
    {
        $file_path = ROOT_PATH . '/data/files/payments_import/BankConnect/';

        /** @var BankConnectService $BCService */
        $BCService = $this->getServiceLocator()->get('bankconnect_service')->setConfig($this->getServiceLocator()->get('service_manager')->get('Config')['bankconnect']);

        $getCustomerStatement = $BCService->getCustomerStatement();

        $em = $this->getEntityManager();

        $file_path .= date("Y/m/d") . "/";
        $file_name = 'BankConnect_' . time() . '.xml';
        $file = $file_path . $file_name;

        $fs = new Filesystem();

        if (!$fs->exists($file)) {
            $old = umask(0);

            $fs->mkdir($file_path);
            $fs->touch($file);

            file_put_contents($file, $getCustomerStatement);

            umask($old);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($finfo, $file);
        finfo_close($finfo);

        $paymentFileImport = new PaymentFileImport();
        $paymentFileImport->setBankName('');
        $paymentFileImport->setBankAccount('');
        $paymentFileImport->setAction('');
        $paymentFileImport->setStatus(PaymentFileImport::STATUS_UNPROCESSED);
        $paymentFileImport->setIsManual(false);

        $paymentFileImport->setApiInfo($BCService->getLastEntity());

        $paymentFileImport->setFileMimeType($type);
        $paymentFileImport->setFileSize(filesize($file));
        $paymentFileImport->setFilePath($file);
        $paymentFileImport->setFileName($file_name);

        $em->persist($paymentFileImport);
        $em->flush();

        $paymentFileImport->setFileName(uniqid() . '_' . $paymentFileImport->getFileName());
        $em->flush();

        $this->writeLine('File were imported' .$paymentFileImport->getFileName());

        try {
            $newPath = ROOT_PATH . SydbankGatewayController::DK_NEW_ALERTS_PATH . $paymentFileImport->getFileName();
            $old = umask(0);
            FilesTool::copy($paymentFileImport->getFilePath(), $newPath);
            umask($old);
        } catch (\Exception $ex) {
            $ex->getMessage();
            /* @TODO: JB - Maybe there is point to save it somewhere. Ask @Roman */

            $em->remove($paymentFileImport);
            $em->flush();
        }
    }

    public function exportPaymentOrdersAction()
    {
        $em = $this->getEntityManager();

        /** @var BankConnectService $BCService */
        $BCService = $this->getServiceLocator()->get('bankconnect_service')->setConfig($this->getServiceLocator()->get('service_manager')->get('Config')['bankconnect']);
        $bank = 'SYBKDK22';

        /**
         * @var PaymentOrdersRepository $paymentOrdersRepository
         */
        $paymentOrdersRepository = $this->getEntityManager()->getRepository(PaymentOrder::class);
        $paymentOrdersRepository->setProcessingStatus($bank);

        /**
         * @var PaymentOrder $paymentOrders
         */
        if ($paymentOrders = $paymentOrdersRepository->getPaymentOrdersForBank($bank)->getResult()) {
		
            if ($exportFile = $this->getServiceLocator()->get('sydbank_iso20022_xml_generator')->createExportFile($paymentOrders)) {
                if ($transferPayments = $BCService->transferPayments($exportFile) && preg_match($transferPayments, 'Error')) {

                    $apiGateway = $BCService->getLastEntity();

					if (is_array($paymentOrders)) {
						foreach($paymentOrders as $paymentOrder) {
							 $apiGateway->setClient($paymentOrder->getClient());						
						}
					} else {
						 $apiGateway->setClient($paymentOrders->getClient());
					}

                    $paymentOrdersRepository->setApiInfo($bank, $apiGateway);

                    $em->flush();
                } else {
					dd($transferPayments);
				}
            }
        }
    }
}