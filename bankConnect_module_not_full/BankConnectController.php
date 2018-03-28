<?php

namespace BankGateway\Controller;

use BankGateway\Service\BankConnectService;
use PaymentOrders\Entity\PaymentOrder;
use Common\Controller\AbstractActionController;
use PaymentOrders\Repository\PaymentOrdersRepository;
use Zend\View\Model\ViewModel;

class BankConnectController extends AbstractActionController
{
    public function indexAction()
    {
        $name = str_replace(["Controller", "BankGateway", "\\"], "", get_class($this));
        $transferPayments = [];
        $getCustomerStatement = [];
        $bank = 'SYBKDK22';

        /** @var BankConnectService $BCService */
        $BCService = $this->getServiceLocator()->get(strtolower($name).'_service')->setConfig($this->getServiceLocator()->get('service_manager')->get('Config')[strtolower($name)]);

//        $BCService->createKeyPair();
//        $data[] = $BCService->getBankCertificate();
//        $data[] = $BCService->activateServiceAgreement();
        $getCustomerStatement = $BCService->getCustomerStatement();
//
//        /**
//         * @var PaymentOrdersRepository $paymentOrdersRepository
//         */
//        $paymentOrdersRepository = $this->getEntityManager()->getRepository(PaymentOrder::class);
//        $paymentOrdersRepository->setProcessingStatus($bank);
//
//        if ($queryResult = $paymentOrdersRepository->getPaymentOrdersForBank($bank)->getResult()) {
//            if ($paymentOrders = $this->getServiceLocator()->get('sydbank_iso20022_xml_generator')->createExportFile($queryResult)) {
//                if ($transferPayments = $BCService->transferPayments($paymentOrders)) {
//                    $paymentOrdersRepository->setApiInfo($bank, $BCService->getLastEntity());
//                }
//            }
//        }

        $vm = new ViewModel([
            'content' => [
                $transferPayments,
                $getCustomerStatement,
            ]
        ]);

        $vm->setTemplate(strtolower($name).'/'.strtolower($name).'/index');

        return $vm;
    }
}