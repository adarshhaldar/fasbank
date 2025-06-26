<?php

namespace App\Services;

use App\Exceptions\NotificationException;
use App\Http\Resources\Api\NotificationResource;
use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function getNewNotificationDetail()
    {
        $user = Auth::guard('api')->user();

        $newNotification = $user->newNotification;
        if ($newNotification) {
            $newNotification->is_sent = true;
            $newNotification->save();
        }
        $newNotificationCount =  $user->newNotifications ? $user->newNotifications->count() : 0;

        return ['newNotification' => $newNotification, 'newNotificationCount' => $newNotificationCount];
    }

    public function getList($currentPage, $perPage)
    {
        try {
            DB::beginTransaction();

            $user = Auth::guard('api')->user();

            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'DESC')
                ->paginate($perPage, ['*'], 'page', $currentPage);

            foreach ($notifications as $notification) {
                $notification->is_sent = true;
                $notification->is_seen = true;

                $notification->save();
            }

            DB::commit();
            return NotificationResource::collection($notifications);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::guard('api')->user();

            $notification = Notification::where(['user_id' => $user->id, 'id' => $id])->first();

            if (!$notification) {
                throw new NotificationException('Invalid id');
            }

            $notification->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteAll()
    {
        try {
            DB::beginTransaction();

            $user = Auth::guard('api')->user();

            $notifications = Notification::where('user_id', $user->id)->get();

            foreach ($notifications as $notification) {
                $notification->delete();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
