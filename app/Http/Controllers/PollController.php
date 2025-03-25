<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function show(Poll $poll)
    {
        $poll->load('options');
        return view('polls.show', compact('poll'));
    }

    public function vote(Request $request, Poll $poll)
    {
        if (session()->has('voted_polls') && in_array($poll->id, session('voted_polls'))) {
            return redirect()->back()->with('error', 'Bạn đã bình chọn cho cuộc bình chọn này rồi.');
        }

        $request->validate([
            'option' => 'required|exists:poll_options,id',
        ]);

        $option = PollOption::findOrFail($request->option);
        $option->increment('votes');

        $votedPolls = session()->get('voted_polls',);
        session()->put('voted_polls', array_merge($votedPolls, [$poll->id]));

        return redirect()->back()->with('success', 'Cảm ơn bạn đã bình chọn.');
    }
}
