@php use App\Models\Student\Quiz; @endphp
@extends('student.layouts.main')


@section('content')
    @php
        $date = Quiz::getAttachmentById($quiz->id)->date;
        $time = Quiz::getAttachmentById($quiz->id)->time;
        $dateTime = strtotime($date. " " .$time);
        $dateTimeFormat = date('H:i:s', $dateTime);

    @endphp
    <div class="card">
        <h6 class="text-md-end p-3">
            <i class="ri-time-fill"></i> <span id="timer">{{$dateTimeFormat}}</span>
        </h6>
        <form action="{{route('student.quiz.store')}}" id="quizForm" method="post">
            @csrf
            @php $qCount = 0 @endphp
            @foreach($questions as $key => $question)
                <div class="card-header">
                    <strong>{{++$key}})</strong> {{$question->name}}
                </div>
                    <?php
                    $options = Quiz::getOptionById($question->id)
                    ?>
                Subject<input type="text" name="subjectId" id="" value="{{$subject->id}}">
                Quiz <input type="text" name="quizId" id="" value="{{$quiz->id}}">
                Question <input type="text" name="question[]" id="" value="{{$question->id}}">
                Answer <input type="text" name="option[]" id="ans_{{$key}}" value="">

                <ol type="A">
                    @foreach($options as $k => $option)
                        <div class="card-body">
                            <li>
                                &nbsp;<input type="radio" class="selectOption" id="" name="options{{$key}}"
                                             value="{{$option->id}}"
                                             data-id="{{$key}}">
                                <label for="ans_{{$key}}">{{$option->name}}</label>
                            </li>
                        </div>
                    @endforeach
                </ol>
                <hr>
                @php ++$qCount @endphp
            @endforeach
            <button id="submitBtn" class="badge bg-label-hover-success text-md-end" type="submit">Testni yakunlash
            </button>
        </form>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            $(".selectOption").click(function () {
                let no = $(this).attr('data-id');
                $("#ans_" + no).val($(this).val());
            })

            let hours = 0, minutes = 0, seconds = 0;
            let quizId = @json($quiz->id);
            let subjectId = "{{$subject->id}}"
            let userId = @json(Auth::user()->id);

            // Timer funksiyasi
            function startTimer() {
                let timer = setInterval(() => {
                    $("#timer").text(
                        hours.toString().padStart(2, "0") + ":" +
                        minutes.toString().padStart(2, "0") + ":" +
                        seconds.toString().padStart(2, "0")
                    );

                    if (hours === 0 && minutes === 0 && seconds === 0) {
                        clearInterval(timer);
                        clearTimeInDatabase()
                        $('#quizForm').submit(); // Testni yakunlash
                    }

                    if (seconds === 0) {
                        if (minutes === 0) {
                            if (hours > 0) {
                                hours--;
                                minutes = 59;
                                seconds = 59;
                            }
                        } else {
                            minutes--;
                            seconds = 59;
                        }
                    } else {
                        seconds--;
                    }

                    saveTimeToDatabase(); // Har bir soniyada vaqtni saqlash
                }, 1000);
            }

            // Vaqtni serverga saqlash funksiyasi
            function saveTimeToDatabase() {
                // Yozilgan vaqtni tekshirish
                if (isNaN(hours) || isNaN(minutes) || isNaN(seconds)) {
                    console.error("Vaqt noto'g'ri: ", hours, minutes, seconds);
                    return; // Agar noto'g'ri bo'lsa, saqlashni to'xtatish
                }

                $.ajax({
                    url: "{{ route('student.quiz.saveTime') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        quizId: quizId,
                        subjectId: subjectId,
                        userId: userId,
                        hours: hours,
                        minutes: minutes,
                        seconds: seconds
                    },
                    success: function (response) {
                        console.log("Vaqt saqlandi:", response);
                    },
                    error: function (xhr) {
                        console.error("Vaqtni saqlashda xatolik yuz berdi:", xhr.responseText);
                    }
                });
            }

            // Saqlangan vaqtni yuklash
            function loadTimeFromDatabase() {
                $.ajax({
                    url: "{{ route('student.quiz.getTime') }}",
                    method: "GET",
                    data: {
                        quizId: quizId,
                        userId: userId
                    },
                    success: function (response) {
                        if (response && !isNaN(response.hours) && !isNaN(response.minutes) && !isNaN(response.seconds)) {
                            hours = parseInt(response.hours);
                            minutes = parseInt(response.minutes);
                            seconds = parseInt(response.seconds);
                            startTimer(); // Vaqtni yuklab, timerni boshlash
                        } else {
                            // Agar noto'g'ri qiymatlar bo'lsa, standart vaqtni o'rnatish
                            let timeParts = @json(explode(":", $dateTimeFormat));
                            hours = timeParts[0] ? parseInt(timeParts[0]) : 0; // Agar bo'sh bo'lsa, 0 qiymat berilsin
                            minutes = timeParts[1] ? parseInt(timeParts[1]) : 0;
                            seconds = timeParts[2] ? parseInt(timeParts[2]) : 0;
                            startTimer();
                        }
                    },
                    error: function (xhr) {
                        let timeParts = @json(explode(":", $dateTimeFormat));
                        hours = timeParts[0] ? parseInt(timeParts[0]) : 0; // Agar bo'sh bo'lsa, 0 qiymat berilsin
                        minutes = timeParts[1] ? parseInt(timeParts[1]) : 0;
                        seconds = timeParts[2] ? parseInt(timeParts[2]) : 0;
                        startTimer();
                        console.error("Vaqtni yuklashda xatolik yuz berdi:", xhr.responseText);
                    }
                });
            }


            loadTimeFromDatabase(); // Sahifa yuklanganda vaqtni tiklash


            $('#submitBtn').click(async function () {
                let confirms = confirm("Rostdan ham testni yakunlamoqchimisiz?");
                if (confirms) {
                    try {
                        const result = await clearTimeInDatabase();
                        if (result.status == true) {
                            $('#quizForm').submit(); // Testni yakunlash
                        } else {
                            alert(result.message || 'Bazani tozalashda xatolik');
                            startTimer(); // Vaqtni davom ettirish
                        }
                    } catch (error) {
                        alert(error.message || 'Bazani tozalashda xatolik');
                        startTimer();
                    }
                } else {
                    startTimer(); // Vaqtni davom ettirish
                }
            });


// AJAX funksiyasi Promise bilan
            function clearTimeInDatabase() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: "{{ route('student.quiz.clearTime') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            quizId: quizId,
                            userId: userId
                        },
                        success: function (response) {
                            resolve({
                                status: true,
                                message: response.message || "Vaqt muvaffaqiyatli tozalandi"
                            });
                        },
                        error: function (xhr) {
                            reject({
                                status: false,
                                message: xhr.responseJSON?.message || "Vaqtni tozalashda xatolik yuz berdi"
                            });
                        }
                    });
                });
            }


        });
    </script>
@endsection
