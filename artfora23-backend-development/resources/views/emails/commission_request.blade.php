@extends('layouts.mail')

@section('title') New commission request @endsection
@section('preheader') {{ $name }} sent commission request to {{ $user['username'] }}. @endsection

@section('content')
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left"
                style="padding: 0 0 20px 0; font-size: 16px; line-height: 25px; font-family: 'Atkinson Hyperlegible', sans-serif; color: #c1c1c0;"
                class="padding-copy"
            >
                <i>COMMISSION FROM:</i>
                <br>
                {{ $name }}
            </td>
        </tr>

        <tr>
            <td align="left"
                style="padding: 0 0 20px 0; font-size: 16px; line-height: 25px; font-family: 'Atkinson Hyperlegible', sans-serif; color: #c1c1c0;"
                class="padding-copy"
            >
                <i>SENDER'S EMAIL ADDRESS:</i>
                <br>
                <a style="color: white;" href="mailto:{{$email}}">{{$email}}</a>
            </td>
        </tr>

        <tr>
            <td align="left"
                style="padding: 0; font-size: 16px; line-height: 25px; font-family: 'Atkinson Hyperlegible', sans-serif; color: #c1c1c0;"
                class="padding-copy"
            >
                <i>MESSAGE:</i>
                <br>
                {{ $text }}
            </td>
        </tr>
    </table>
@endsection

@section('footer')
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left"
                style="padding: 20px 0 50px 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;"
                class="padding-copy">If you have any questions, feel free to reach out to our <a
                    href="mailto:hello@artfora.com">partner success team</a> – we're lighting quick at replying.
            </td>
        </tr>
    </table>
@endsection
