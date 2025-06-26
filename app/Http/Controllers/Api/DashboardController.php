<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DashboardException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ResponseHelper;

    public function __construct(private DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function detail(){
        try {
            return $this->success('Dashboard details found', $this->dashboardService->getDetail());
        } catch (DashboardException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function getTransactionDetail($filter){
        try {
            return $this->success('Transaction details found', $this->dashboardService->getTransactionsGraphData($filter));
        } catch (DashboardException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function getAmountDetail($filter){
        try {
            return $this->success('Amount details found', $this->dashboardService->getAmountGraphData($filter));
        } catch (DashboardException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }
}
