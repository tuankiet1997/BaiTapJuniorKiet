<?php

namespace Magenest\RedirectPage\Plugin;

use Magento\Framework\App\Router\DefaultRouter;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ActionFactory;
use Magenest\RedirectPage\Helper\Data;

/**
 * Class RouterRedirect
 * @package Magenest\PageRedirect\Plugin
 */
class Redirect
{
    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    protected $data;

    /**
     * RouterRedirect constructor.
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
     * @param ManagerInterface $messageManager
     * @param Data $data
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
        ManagerInterface $messageManager,
        Data $data
    ) {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
        $this->messageManager = $messageManager;
        $this->data = $data;
    }

    /**
     * @param DefaultRouter $subject
     * @param callable $proceed
     * @param RequestInterface $request
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundMatch(DefaultRouter $subject, callable $proceed, RequestInterface $request)
    {
        $identifier = explode('/', trim($request->getPathInfo(), '/'));
        foreach ($identifier as $singlePath) {
            $category = $this->data->searchCategory($singlePath);
            if ($category->getId()) {
                $request->setModuleName('catalog')->setControllerName('category')->setActionName('view')
                    ->setParams(['id' => $category->getId()]);
                $requestPath = $this->data->getRequestPath('category', $category->getId());
                $this->response->setRedirect($requestPath);
                $this->messageManager->addNoticeMessage(__('The path %1 does not exists. Are you looking for %2 ?', $request->getPathInfo(), $category->getName()));
                $this->data->setCookie($this->data->getMessages());
                return $this->actionFactory->create(\Magento\Framework\App\Action\Redirect::class);
            }
            $searchTerm = $this->data->searchSearchTerm(urldecode($singlePath));
            if ($searchTerm->getId()) {
                $request->setModuleName('catalogsearch')->setControllerName('result')->setActionName('index')
                    ->setParams(['q' => urlencode($searchTerm->getQueryText())]);
                $this->response->setRedirect('catalogsearch/result/?q='.urlencode($searchTerm->getQueryText()));
                return $this->actionFactory->create(\Magento\Framework\App\Action\Redirect::class);
            }
            if (strlen($singlePath) > 3) {
                $product = $this->data->searchProduct($singlePath);
                if ($product->getId()) {
                    $request->setModuleName('catalog')->setControllerName('product')->setActionName('view')
                        ->setParams(['id' => $product->getId()]);
                    $requestPath = $this->data->getRequestPath('product', $product->getId());
                    $this->response->setRedirect($requestPath);
                    return $this->actionFactory->create(\Magento\Framework\App\Action\Redirect::class);
                }
            }
        }
        return $proceed($request);
    }
}
