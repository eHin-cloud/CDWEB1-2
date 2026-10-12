<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Room;
use App\Models\RoomReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * FEAT_25_REVIEW_REPORT: Đánh giá phòng thực tế & Báo cáo sai phạm
 * Phụ trách: Huỳnh Văn Vĩnh Em
 */
class RoomReviewReportController extends Controller
{
    /**
     * POST /renty/room/{id}/review
     * Đánh giá phòng thực tế (Chỉ người có hợp đồng thuê mới được review)
     */
    public function storeReview(Request $request, int|string $id): JsonResponse|RedirectResponse
    {
        $room = Room::find($id);
        if (!$room) {
            abort(404, 'Không tìm thấy phòng trọ.');
        }

        $rating = $request->input('rating');
        $comment = trim((string) $request->input('comment', ''));

        $errors = [];
        $errorCode = null;

        // 1. Kiểm tra số sao đánh giá (ERR_25_01)
        if (empty($rating) || !is_numeric($rating) || (int) $rating < 1 || (int) $rating > 5) {
            $errorCode = 'ERR_25_01';
            $errors['rating'] = ['Vui lòng chọn số sao xếp hạng (từ 1 đến 5 sao).'];
        }

        // 2. Kiểm tra nội dung nhận xét (ERR_25_02: tối thiểu 10 ký tự)
        if ($comment === '' || mb_strlen($comment) < 10) {
            $errorCode = $errorCode ?? 'ERR_25_02';
            $errors['comment'] = ['Nội dung nhận xét đánh giá phải có độ dài tối thiểu 10 ký tự.'];
        }

        if (!empty($errors)) {
            $firstMessage = reset($errors)[0];

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'code' => $errorCode ?? 'VALIDATION_ERROR',
                    'message' => $firstMessage,
                    'errors' => $errors,
                ], 422);
            }

            return redirect()->back()
                ->withErrors($errors)
                ->withInput()
                ->with('error', $firstMessage)
                ->with('error_code', $errorCode);
        }

        // 3. Lưu đánh giá vào database
        $user = Auth::user();
        $authorName = $request->input('author_name')
            ?: ($user ? $user->name : 'Khách thuê');

        $review = Review::create([
            'room_id' => $room->id,
            'user_id' => $user?->id,
            'author_name' => $authorName,
            'rating' => (int) $rating,
            'comment' => $comment,
        ]);

        $successMessage = 'Cảm ơn bạn đã gửi đánh giá thực tế!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'data' => $review,
            ], 201);
        }

        return redirect()->back()->with('success', $successMessage);
    }

    /**
     * POST /renty/room/{id}/report
     * Gửi báo cáo vi phạm phòng trọ: lừa đảo, sai giá, thông tin ảo
     */
    public function storeReport(Request $request, int|string $id): JsonResponse|RedirectResponse
    {
        $room = Room::find($id);
        if (!$room) {
            abort(404, 'Không tìm thấy phòng trọ.');
        }

        $reportReason = trim((string) ($request->input('report_reason') ?? $request->input('reason') ?? ''));
        $description = trim((string) $request->input('description', ''));

        // Kiểm tra lý do báo cáo vi phạm (ERR_25_03)
        if ($reportReason === '') {
            $errorMessage = 'Vui lòng chọn lý do vi phạm (Lừa cọc, Ảnh sai thực tế, Giá ảo...).';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'code' => 'ERR_25_03',
                    'message' => $errorMessage,
                    'errors' => [
                        'report_reason' => [$errorMessage],
                    ],
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['report_reason' => $errorMessage])
                ->withInput()
                ->with('error', $errorMessage)
                ->with('error_code', 'ERR_25_03');
        }

        $user = Auth::user();

        $report = RoomReport::create([
            'room_id' => $room->id,
            'reporter_name' => $request->input('reporter_name') ?? $user?->name ?? 'Người dùng',
            'reporter_phone' => $request->input('reporter_phone') ?? $user?->phone,
            'reason' => $reportReason,
            'description' => $description !== '' ? $description : $reportReason,
            'status' => 'pending',
        ]);

        $successMessage = 'Đã tiếp nhận báo cáo vi phạm. Quản trị viên sẽ kiểm duyệt tin này trong 12h.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'code' => 'ERR_25_04',
                'message' => $successMessage,
                'data' => $report,
            ], 201);
        }

        return redirect()->back()
            ->with('success', $successMessage)
            ->with('code', 'ERR_25_04');
    }
}
