<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PaymentException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PaymentPayOrRequestRequest;
use App\Http\Requests\Api\PaymentPayRequestRequest;
use App\Http\Requests\Api\TransactionListRequest;
use App\Http\Resources\Api\UserResource;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ResponseHelper;

    public function __construct(private PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function getToUserDetail($toUserFbid)
    {
        try {
            $toUser = $this->paymentService->getToUserFromFbid($toUserFbid);
            return $this->success('Account found', $toUser ? new UserResource($toUser) : null);
        } catch (PaymentException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function list($toUserFbid, $currentPage = 1, $perPage = 20)
    {
        try {
            return $this->success('Payment list found', $this->paymentService->getList($toUserFbid, $currentPage, $perPage), true);
        } catch (PaymentException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function pay(PaymentPayOrRequestRequest $request)
    {
        try {
            return $this->success('Payment has been done', $this->paymentService->pay($request));
        } catch (PaymentException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function request(PaymentPayOrRequestRequest $request)
    {
        try {
            return $this->success('Payment has been requested', $this->paymentService->request($request));
        } catch (PaymentException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function payRequest(PaymentPayRequestRequest $request){
        try {
            return $this->success('Payment has been done', $this->paymentService->payRequest($request));
        } catch (PaymentException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function transactionList(TransactionListRequest $request, $currentPage = 1, $perPage = 20)
    {
        try {
            return $this->success('Transaction list found', $this->paymentService->getTransactionList($request, $currentPage, $perPage), true);
        } catch (PaymentException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }
}
