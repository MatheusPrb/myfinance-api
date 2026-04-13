<?php

namespace App\Http\Controllers\V1\Admin;

use App\Application\Presenters\AdminLogPresenter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListAdminLogsRequest;
use App\Http\Responses\ApiResponse;
use App\Models\ApplicationLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class LogController extends Controller
{
    public function index(ListAdminLogsRequest $request): JsonResponse
    {
        $page = (int) $request->validated('page', 1);
        $perPage = (int) $request->validated('per_page', 25);

        $paginator = ApplicationLog::query()
            ->orderByDesc('created_at')
            ->paginate(perPage: $perPage, page: $page)
        ;

        $userIds = [];
        foreach ($paginator->items() as $log) {
            $uid = AdminLogPresenter::extractUserId(is_array($log->context) ? $log->context : null);
            if ($uid !== null && $uid !== '') {
                $userIds[$uid] = true;
            }
        }

        $users = User::query()
            ->whereIn('id', array_keys($userIds))
            ->get()
            ->keyBy('id')
        ;

        $items = [];
        foreach ($paginator->items() as $log) {
            $uid = AdminLogPresenter::extractUserId(is_array($log->context) ? $log->context : null);
            $user = ($uid !== null && $uid !== '') ? $users->get($uid) : null;
            $items[] = AdminLogPresenter::toArray($log, $user);
        }

        return ApiResponse::success([
            'items' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
        ]);
    }
}
