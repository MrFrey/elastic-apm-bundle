<?php

namespace Goksagun\ElasticApmBundle\EventListener;

use Goksagun\ElasticApmBundle\Apm\ElasticApmAwareInterface;
use Goksagun\ElasticApmBundle\Apm\ElasticApmAwareTrait;
use Goksagun\ElasticApmBundle\Utils\RequestProcessor;
use PhilKra\Exception\Transaction\DuplicateTransactionNameException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ApmTransactionRegisterListener implements ElasticApmAwareInterface, LoggerAwareInterface
{
    use ElasticApmAwareTrait, LoggerAwareTrait;

    public function onKernelRequest(GetResponseEvent $event)
    {
        $config = $this->apm->getConfig();

        $transactions = $config->get('transactions');

        if (!$event->isMasterRequest() || !$config->get('active') || !$transactions['enabled']) {
            return;
        }

        try {
            $this->apm->startTransaction(
                $name = RequestProcessor::getTransactionName(
                    $event->getRequest()
                )
            );
        } catch (DuplicateTransactionNameException $e) {
            return;
        }

        if (null !== $this->logger) {
            $this->logger->info(sprintf('Transaction started for "%s"', $name));
        }
    }
}