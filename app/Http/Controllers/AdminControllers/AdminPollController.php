<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Http\Request;

class AdminPollController extends Controller
{
    public function index()
    {
        $polls = Poll::withCount('options')->paginate(10);
        return view('admin_dashboard.polls.index', compact('polls'));
    }

    public function create()
    {
        return view('admin_dashboard.polls.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
        ]);

        $poll = Poll::create(['question' => $request->question]);
        foreach ($request->options as $option) {
            $poll->options()->create(['option_text' => $option]);
        }

        return redirect()->route('admin.polls.index')->with('success', 'Thêm bình chọn thành công.');
    }

    public function destroy(Poll $poll)
{
    $poll->delete();
    return redirect()->route('admin.polls.index')->with('success', 'Xóa bình chọn thành công.');
}
public function edit(Poll $poll)
{
    $poll->load('options');
    return view('admin_dashboard.polls.edit', compact('poll'));
}

public function update(Request $request, Poll $poll)
{
    $request->validate([
        'question' => 'required|string|max:255',
        'options' => 'required|array',
        'options.*' => 'required|string|max:255',
    ]);

    $poll->update(['question' => $request->question]);

    foreach ($request->options as $optionId => $optionText) {
        $option = $poll->options()->find($optionId);
        if ($option) {
            $option->update(['option_text' => $optionText]);
        } else {
            $poll->options()->create(['option_text' => $optionText]);
        }
    }

    return redirect()->route('admin.polls.index')->with('success', 'Cập nhật bình chọn thành công.');
}

}