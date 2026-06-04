<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product.images', 'user'])
            ->withCount('images')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by rating
        if ($request->filled('rating') && $request->rating !== 'all') {
            $query->where('rating', $request->rating);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($u) => $u->where('full_name', 'like', "%{$search}%"));
            });
        }

        $reviews = $query->paginate(10)->withQueryString();

        $stats = [
            'total'   => Review::count(),
            'pending' => Review::where('status', 'pending')->count(),
            'avg_rating' => round(Review::avg('rating'), 1),
            'hidden'  => Review::where('status', 'hidden')->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function show($id)
    {
        $review = Review::with(['product.images', 'user', 'images'])->findOrFail($id);
        return response()->json($review);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,approved,hidden']);

        $review = Review::findOrFail($id);
        $review->status = $request->status;
        $review->save();

        $label = match($request->status) {
            'approved' => 'Đã phê duyệt',
            'hidden'   => 'Đã ẩn',
            default    => 'Đặt về chờ duyệt',
        };

        return back()->with('success', "{$label} đánh giá #{$review->review_id} thành công!");
    }

    public function destroy($id)
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
                $review = Review::with('images')->findOrFail($id);
                
                // Xóa file ảnh vật lý
                foreach ($review->images as $image) {
                    if (!str_starts_with($image->image_url, 'http')) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_url);
                        // Xóa fallback nếu lưu ngoài storage (public path)
                        $publicPath = public_path($image->image_url);
                        if (file_exists($publicPath) && is_file($publicPath)) {
                            unlink($publicPath);
                        }
                    }
                }

                // Xóa bản ghi trong DB
                $review->images()->delete();
                $review->delete();
            });

            return back()->with('success', "Đã xóa đánh giá #{$id} thành công!");
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa đánh giá: ' . $e->getMessage());
        }
    }
}
