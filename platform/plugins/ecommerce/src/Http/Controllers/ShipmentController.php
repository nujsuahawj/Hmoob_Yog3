<?php

namespace Botble\Ecommerce\Http\Controllers;

use Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingCodStatusEnum;
use Botble\Ecommerce\Enums\ShippingCrossCheckingStatusEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShipmentController extends BaseController
{
    /**
     * @var OrderInterface
     */
    protected $orderRepository;

    /**
     * @var ShipmentInterface
     */
    protected $shipmentRepository;

    /**
     * @var OrderHistoryInterface
     */
    protected $orderHistoryRepository;

    /**
     * @var ShipmentHistoryInterface
     */
    protected $shipmentHistoryRepository;

    /**
     * @param OrderInterface $orderRepository
     * @param ShipmentInterface $shipmentRepository
     * @param OrderHistoryInterface $orderHistoryRepository
     * @param ShipmentHistoryInterface $shipmentHistoryRepository
     */
    public function __construct(
        OrderInterface $orderRepository,
        ShipmentInterface $shipmentRepository,
        OrderHistoryInterface $orderHistoryRepository,
        ShipmentHistoryInterface $shipmentHistoryRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->shipmentHistoryRepository = $shipmentHistoryRepository;
    }

    /**
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        Assets::addStylesDirectly('vendor/core/plugins/ecommerce/css/ecommerce.css')
            ->addScriptsDirectly('vendor/core/plugins/ecommerce/js/shipment.js');

        $shipment = $this->shipmentRepository->findOrFail($id);
        page_title()->setTitle(__('Edit shipment :code ', ['code' => get_shipment_code($id)]));

        return view('plugins/ecommerce::shipments.edit', compact('shipment'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postUpdateStatus($id, Request $request, BaseHttpResponse $response)
    {
        $shipment = $this->shipmentRepository->findOrFail($id);
        $this->shipmentRepository->createOrUpdate(['status' => $request->input('status')], compact('id'));

        $this->shipmentHistoryRepository->createOrUpdate([
            'action'      => 'update_status',
            'description' => __('Changed status of shipment to : :status . Updated by: %user_name%', [
                'status' => ShippingStatusEnum::getLabel($request->input('status')),
            ]),
            'shipment_id' => $id,
            'order_id'    => $shipment->order_id,
            'user_id'     => Auth::user()->getKey() ?? 0,
        ]);

        switch ($request->input('status')) {
            case ShippingStatusEnum::DELIVERED:
                $this->orderRepository->createOrUpdate(['status' => OrderStatusEnum::COMPLETED],
                    ['id' => $shipment->order_id]);

                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'update_status',
                    'description' => 'Order confirmed by %user_name%',
                    'order_id'    => $shipment->order_id,
                    'user_id'     => Auth::user()->getKey() ?? 0,
                ]);
                break;
            case ShippingStatusEnum::CANCELED:
                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'cancel_shipment',
                    'description' => __('Shipping is cancelled by %user_name%'),
                    'order_id'    => $shipment->order_id,
                    'user_id'     => Auth::user()->getKey(),
                ]);
                break;
        }

        return $response->setMessage(__('Update shipping status successfully!'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postUpdateCodStatus($id, Request $request, BaseHttpResponse $response)
    {
        $shipment = $this->shipmentRepository->findOrFail($id);
        $this->shipmentRepository->createOrUpdate(['cod_status' => $request->input('status')], compact('id'));

        $this->shipmentHistoryRepository->createOrUpdate([
            'action'      => 'update_cod_status',
            'description' => __('Updated COD status to :status . Updated by: %user_name%', [
                'status' => ShippingCodStatusEnum::getLabel($request->input('status')),
            ]),
            'shipment_id' => $id,
            'order_id'    => $shipment->order_id,
            'user_id'     => Auth::user()->getKey() ?? 0,
        ]);

        return $response->setMessage(__('Updated COD status of shipment successfully!'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postUpdateCrossCheckingStatus($id, Request $request, BaseHttpResponse $response)
    {
        $shipment = $this->shipmentRepository->findOrFail($id);
        $this->shipmentRepository->createOrUpdate(['cross_checking_status' => $request->input('status')],
            compact('id'));

        $this->shipmentHistoryRepository->createOrUpdate([
            'action'      => 'update_cross_checking_status',
            'description' => __('Updated cross checking status to :status . Updated by: %user_name%', [
                'status' => ShippingCrossCheckingStatusEnum::getLabel($request->input('status')),
            ]),
            'shipment_id' => $id,
            'order_id'    => $shipment->order_id,
            'user_id'     => Auth::user()->getKey() ?? 0,
        ]);

        return $response->setMessage(__('Updated cross checking status successfully!'));
    }
}
