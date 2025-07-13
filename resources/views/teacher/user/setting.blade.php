@extends('teacher.layouts.main') {{-- Bu yerda sizning asosiy layoutingizni chaqirasiz --}}

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ __('Profil Sozlamalari') }}
                    </div>

                    <div class="card-body">
                        <form id="profile-settings-form">
                            @csrf {{-- CSRF himoyasi uchun --}}

                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('Ism') }}</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ Auth::user()->name }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Elektron pochta') }}</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ Auth::user()->email }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="old_password"
                                    class="form-label">{{ __('Joriy Parol (o\'zgartirish uchun)') }}</label>
                                <input type="password" class="form-control" id="old_password" name="old_password">
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">{{ __('Yangi Parol') }}</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>

                            <div class="mb-3">
                                <label for="new_password_confirmation"
                                    class="form-label">{{ __('Yangi Parolni Tasdiqlash') }}</label>
                                <input type="password" class="form-control" id="new_password_confirmation"
                                    name="new_password_confirmation">
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('Saqlash') }}</button>
                            <div id="status-message" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Skriptlar uchun alohida seksiyasi --}}
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script>
        $(document).ready(function() {

            const statusMessage = $('#status-message');

            // Umumiy AJAX xato ishlovchisi
            function handleAjaxError(xhr, status, error, contextMessage =
                "Server bilan aloqada xatolik yuz berdi") {
                console.error(contextMessage + ':', status, error);
                console.error('XHR javobi:', xhr.responseText);
                console.error('Status kodi:', xhr.status);

                let errorMessage = `${contextMessage}. Iltimos, qayta urinib ko'ring.`;

                try {
                    const responseJson = JSON.parse(xhr.responseText);
                    if (responseJson.message) {
                        errorMessage += ` Xato: ${responseJson.message}`;
                    }
                    if (responseJson.errors) {
                        errorMessage += '\nDetal: ';
                        for (const field in responseJson.errors) {
                            errorMessage += `\n${field}: ${responseJson.errors[field].join(', ')}`;
                        }
                    }
                } catch (e) {
                    // JSON parsingda xato bo'lsa, oddiy xatoni ko'rsatish
                }
                statusMessage.removeClass('alert-success').addClass('alert-danger').text(errorMessage).show();
            }

            $('#profile-settings-form').on('submit', function(e) {
                e.preventDefault(); // Sahifani yangilanishini to'xtatish

                statusMessage.hide().removeClass('alert-success alert-danger').text(
                    ''); // Avvalgi xabarlarni tozalash

                const formData = $(this).serialize(); // Formadagi barcha ma'lumotlarni yig'ish

                $.ajax({
                    url: "{{ route('teacher.user.update') }}", // Yangilash uchun yo'nalish
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            statusMessage.addClass('alert-success').text(response.message)
                                .show();
                            // Parol o'zgartirilgan bo'lsa, parol maydonlarini tozalash
                            $('#old_password').val('');
                            $('#new_password').val('');
                            $('#new_password_confirmation').val('');
                        } else {
                            // Backenddan kelgan xato xabarini ko'rsatish
                            statusMessage.addClass('alert-danger').text(response.message ||
                                'Ma\'lumotlarni saqlashda xatolik yuz berdi.').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        handleAjaxError(xhr, status, error,
                            'Profil ma\'lumotlarini yangilashda xato');
                    }
                });
            });
        });
    </script>
@endsection
