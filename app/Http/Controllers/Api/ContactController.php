<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ContactException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\ContactService;
use Exception;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ResponseHelper;

    public function __construct(private ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function list($currentPage = 1, $perPage = 20, $search = null)
    {
        try {
            return $this->success('Contact list found', $this->contactService->getList($currentPage, $perPage, $search), true);
        } catch (ContactException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function findNew($search = null)
    {
        try {
            return $this->success('Contact found', $this->contactService->findNew($search));
        } catch (ContactException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function addNew($fbid)
    {
        try {
            $this->contactService->addNew($fbid);
            return $this->success('Contact added successfully');
        } catch (ContactException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function recentList($currentPage = 1, $perPage = 20, $search = null)
    {
        try {
            return $this->success('Recent list found', $this->contactService->getRecentList($currentPage, $perPage, $search), true);
        } catch (ContactException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }
}
