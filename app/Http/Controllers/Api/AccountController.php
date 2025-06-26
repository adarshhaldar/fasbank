<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AccountException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\AccountService;
use Exception;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    use ResponseHelper;

    public function __construct(private AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function detail()
    {
        try {
            return $this->success('Account details found', $this->accountService->getDetail());
        } catch (AccountException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function logOut(){
        try {
            $this->accountService->logOut();
            return $this->success('Logged out successfully');
        } catch (AccountException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function logOutAll(){
        try {
            $this->accountService->logOutFromAllDevices();
            return $this->success('Logged out of all devices successfully');
        } catch (AccountException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function delete(){
         try {
            $this->accountService->delete();
            return $this->success('Account deleted successfully');
        } catch (AccountException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }
}
