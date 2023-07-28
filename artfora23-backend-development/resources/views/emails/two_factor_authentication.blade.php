@extends('layouts.mail')

@section('content')
    <table class="inner-body" align="center" width="100%" cellpadding="0" cellspacing="0"
           style="box-sizing: border-box; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
        <!-- Body content -->
        <tr>
            <td align="center"
                style="padding: 0; font-size: 16px; line-height: 25px; font-family: 'Atkinson Hyperlegible', sans-serif; color: #c1c1c0;"
                class="padding-copy"
            >
                <h1 style="font-size: 20px;">Your authentication code:</h1>
            </td>
        </tr>

        <tr>
            <td align="center"
                style="padding: 0; font-size: 20px; font-weight: bold; line-height: 25px; font-family: 'Atkinson Hyperlegible', sans-serif; color: #c1c1c0;"
                class="padding-copy"
            >
                {{ $code }}
            </td>
        </tr>

    </table>
@endsection