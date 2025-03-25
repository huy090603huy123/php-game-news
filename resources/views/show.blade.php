<div class="poll--widget">
    <ul class="nav">
        <li class="title">
            <h3 class="h4">{{ $poll->question }}</h3>
        </li>

        <li class="options">
            <form action="{{ route('polls.vote', $poll->id) }}" method="POST">
                @csrf
                @foreach ($poll->options as $option)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="option" id="option_{{ $option->id }}" value="{{ $option->id }}">
                        <label class="form-check-label" for="option_{{ $option->id }}">
                            {{ $option->option_text }}
                        </label>
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary mt-3">Bình chọn</button>
            </form>
        </li>
    </ul>
</div>