<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\NotificationException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use ResponseHelper;

    public function __construct(private NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function new()
    {
        try {
            return response()->json($this->notificationService->getNewNotificationDetail());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function list($currentPage = 1, $perPage = 20)
    {
        try {
            return $this->success('Notification list found', $this->notificationService->getList($currentPage, $perPage), true);
        } catch (NotificationException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            return $this->success('Notification deleted successfully', $this->notificationService->delete($id));
        } catch (NotificationException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    public function deleteAll()
    {
        try {
            return $this->success('All notification deleted successfully', $this->notificationService->deleteAll());
        } catch (NotificationException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->exception($e->getMessage());
        }
    }
}
