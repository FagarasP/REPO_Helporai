@component('mail::message')
# Welcome {{ $user->name }},

Your account has been created.

Here are your login details:

**Email:** {{ $user->email }}  
**Password:** {{ $password }}

Please log in and change your password right away.

@component('mail::button', ['url' => url('/login')])
Login Now
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
