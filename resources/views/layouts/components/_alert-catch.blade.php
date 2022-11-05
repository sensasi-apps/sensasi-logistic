@if (Session::has('notifications'))
    @foreach (Session::get('notifications') as $notification)
        @php
            $message = $notification;
            $color = 'info';
            
            if (is_array($notification)) {
                $message = $notification['message'] ?? $notification[0];
                $color = $notification['class'] ?? ($notification[1] ?? 'info');
            }
        @endphp

        @include('components._alert', [
            'message' => $message,
            'color' => $color,
        ])
    @endforeach
@endif

@if ($errors->any())
    @foreach ($errors->all() as $error)
        @include('components._alert', ['message' => $error])
    @endforeach
@endif
