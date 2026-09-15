<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BuildingController extends Controller
{
    /**
     * Khởi tạo và bảo vệ phân quyền Landlord
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập trước.');
            }

            $user = Auth::user();
            if (!$user->canAccessLandlordDashboard()) {
                abort(403, 'Bạn không có quyền truy cập chức năng này.');
            }

            return $next($request);
        });
    }

    /**
     * Danh sách cơ sở lưu trú
     */
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');

        $query = Building::where('tenant_id', $tenantId)
            ->withCount([
                'rooms',
                'rooms as occupied_rooms_count' => fn ($q) => $q->where('status', 'occupied'),
                'rooms as empty_rooms_count' => fn ($q) => $q->where('status', 'empty'),
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status && in_array($status, ['active', 'maintenance', 'inactive'], true)) {
            $query->where('status', $status);
        }

        $buildings = $query->orderByDesc('id')->paginate(9)->appends($request->query());

        // Tổng hợp thống kê
        $totalBuildings = Building::where('tenant_id', $tenantId)->count();
        $totalRooms = \App\Models\Room::where('tenant_id', $tenantId)->count();
        $occupiedRooms = \App\Models\Room::where('tenant_id', $tenantId)->where('status', 'occupied')->count();

        return view('admin.buildings.index', compact(
            'buildings',
            'search',
            'status',
            'totalBuildings',
            'totalRooms',
            'occupiedRooms'
        ));
    }

    /**
     * Màn hình tạo mới cơ sở
     */
    public function create()
    {
        return view('admin.buildings.create');
    }

    /**
     * Lưu mới cơ sở lưu trú
     */
    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'total_floors' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,maintenance,inactive',
            'description' => 'nullable|string|max:2000',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_url' => 'nullable|url|max:500',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:100',
        ], [
            'name.required' => 'Vui lòng nhập tên cơ sở lưu trú.',
            'address.required' => 'Vui lòng nhập địa chỉ cơ sở.',
            'total_floors.min' => 'Số tầng tối thiểu là 1.',
            'image_file.image' => 'File tải lên phải là hình ảnh hợp lệ.',
            'image_file.max' => 'Ảnh không được vượt quá 5MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('buildings', 'public');
        } elseif (!empty($validated['image_url'])) {
            $imagePath = $validated['image_url'];
        }

        $building = Building::create([
            'tenant_id' => $tenantId,
            'name' => $validated['name'],
            'address' => $validated['address'],
            'phone' => $validated['phone'] ?? null,
            'total_floors' => $validated['total_floors'],
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'amenities' => $request->input('amenities', []),
        ]);

        AdminActivityLogger::log(
            'create',
            'buildings',
            'Thêm cơ sở lưu trú ' . $building->name,
            $building,
            ['building_name' => $building->name],
            null,
            $building->only(['name', 'address', 'phone', 'total_floors', 'status'])
        );

        return redirect()->route('admin.buildings.index')
            ->with('success', "Đã thêm cơ sở lưu trú \"{$building->name}\" thành công.");
    }

    /**
     * Màn hình chỉnh sửa cơ sở
     */
    public function edit($id)
    {
        $tenantId = Auth::user()->tenant_id;
        $building = Building::where('tenant_id', $tenantId)->findOrFail($id);

        return view('admin.buildings.edit', compact('building'));
    }

    /**
     * Cập nhật thông tin cơ sở
     */
    public function update(Request $request, $id)
    {
        $tenantId = Auth::user()->tenant_id;
        $building = Building::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'total_floors' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,maintenance,inactive',
            'description' => 'nullable|string|max:2000',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_url' => 'nullable|url|max:500',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:100',
        ], [
            'name.required' => 'Vui lòng nhập tên cơ sở lưu trú.',
            'address.required' => 'Vui lòng nhập địa chỉ cơ sở.',
            'total_floors.min' => 'Số tầng tối thiểu là 1.',
        ]);

        $before = $building->only(['name', 'address', 'phone', 'total_floors', 'status']);

        $imagePath = $building->image;
        if ($request->hasFile('image_file')) {
            if ($imagePath && !str_starts_with($imagePath, 'http') && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image_file')->store('buildings', 'public');
        } elseif ($request->filled('image_url')) {
            $imagePath = $validated['image_url'];
        }

        $building->update([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'phone' => $validated['phone'] ?? null,
            'total_floors' => $validated['total_floors'],
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'amenities' => $request->input('amenities', []),
        ]);

        AdminActivityLogger::log(
            'update',
            'buildings',
            'Cập nhật cơ sở lưu trú ' . $building->name,
            $building,
            ['building_name' => $building->name],
            $before,
            $building->fresh()->only(['name', 'address', 'phone', 'total_floors', 'status'])
        );

        return redirect()->route('admin.buildings.index')
            ->with('success', "Cập nhật cơ sở lưu trú \"{$building->name}\" thành công.");
    }

    /**
     * Xóa cơ sở lưu trú (Có Guard Check an toàn)
     */
    public function destroy($id)
    {
        $tenantId = Auth::user()->tenant_id;
        $building = Building::where('tenant_id', $tenantId)->findOrFail($id);

        // Guard Check: Chặn xóa nếu còn phòng trực thuộc
        $roomsCount = $building->rooms()->count();
        if ($roomsCount > 0) {
            return redirect()->route('admin.buildings.index')
                ->with('error', "Không thể xóa cơ sở \"{$building->name}\" vì vẫn còn {$roomsCount} phòng trọ trực thuộc. Vui lòng chuyển hoặc xóa các phòng trước.");
        }

        $before = $building->only(['name', 'address', 'phone']);
        $buildingName = $building->name;
        $building->delete();

        AdminActivityLogger::log(
            'delete',
            'buildings',
            'Xóa cơ sở lưu trú ' . $buildingName,
            null,
            ['building_name' => $buildingName],
            $before,
            null
        );

        return redirect()->route('admin.buildings.index')
            ->with('success', "Đã xóa cơ sở lưu trú \"{$buildingName}\" thành công.");
    }
}
