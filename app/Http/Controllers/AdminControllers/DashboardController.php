<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Role;
use App\Models\Visitor;

use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request){


        $overallVisitor = Visitor::count();
        $visitorDuration = Visitor::avg('duration'); // Tính trung bình thời gian truy cập
        $pagesPerVisit = Visitor::avg('pages_visited');
 

        $viewsData = Post::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as views'))
        ->whereDate('created_at', '>=', now()->subDays(6))
        ->groupBy('date')
        ->orderBy('date')
        ->pluck('views', 'date')
        ->toArray();

    // Lấy dữ liệu bình luận theo ngày
        $commentsData = Comment::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as comments'))
        ->whereDate('created_at', '>=', now()->subDays(6))
        ->groupBy('date')
        ->orderBy('date')
        ->pluck('comments', 'date')
        ->toArray();

    // Tạo mảng labels chứa các ngày
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format('d/m/Y');
            $date = now()->subDays($i)->format('Y-m-d');
            // Đảm bảo có giá trị 0 nếu không có dữ liệu cho ngày đó
            if (!isset($viewsData[$date])) {
                $viewsData[$date] = 0;
            }
            if (!isset($commentsData[$date])) {
                $commentsData[$date] = 0;
            }
        }








        $countPost = Post::all()->count();
        $countCategories = Category::all()->count();

        $role_admin = Role::where('name','!=','user')->first();
        $countAdmin = User::all()->where('role_id', $role_admin->id)->count();

        $role_user = Role::where('name','user')->first();
        $countUser = User::all()->where('role_id', $role_user->id)->count();

        $postAll = Post::all();

        $countView = 0;
        $countComments = 0;
        foreach ($postAll as $post) {
            $countView =  $countView + $post->views;
            $countComments =  $countComments + $post->comments()->count();
        }


        return view('admin_dashboard.index',[
            'countPost' => $countPost,
            'countCategories' => $countCategories,
            'countAdmin' => $countAdmin,
            'countUser' => $countUser,
            'countView' => $countView,
            'countComments' => $countComments,
            'overallVisitor' => $overallVisitor,
            'visitorDuration' => $visitorDuration,
            'pagesPerVisit' => $pagesPerVisit,
            'viewsData' => array_values($viewsData),
            'commentsData' => array_values($commentsData),
            'labels' => $labels,
        ]);
    }

}
