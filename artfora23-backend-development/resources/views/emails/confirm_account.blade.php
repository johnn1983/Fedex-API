@extends('layouts.mail')

@section('title') Welcome email - ARTfora @endsection
@section('preheader') You're almost there, we just need you to verify your email address. @endsection

@section('content')
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left"
                style="padding: 0; font-size: 16px; line-height: 25px; font-family: 'Atkinson Hyperlegible', sans-serif; color: #c1c1c0;"
                class="padding-copy"
            >
                Thank you for signing up for ARTfora.
            </td>
        </tr>

        <tr>
            <td align="left"
                style="padding: 1rem 0 0 0; font-size: 16px; line-height: 25px; font-family: 'Atkinson Hyperlegible', sans-serif; color: #c1c1c0;"
                class="padding-copy"
            >
                Please click the button at the bottom or paste the website address into your browser to verify your
                email address.
            </td>
        </tr>

        <tr>
            <td align="left"
                style="padding: 1rem 0 2rem 0; font-size: 16px; line-height: 25px; font-family: 'Atkinson Hyperlegible', sans-serif; color: #FFFFFF;"
                class="padding-copy"
            >
                <a href="{{config('app.frontend_url')}}/verify-email?token={{ $hash }}&redirect={{ $redirect }}" style="color: #FFFFFF;">{{config('app.frontend_url')}}/verify-email?token={{ $hash }}&redirect={{ $redirect }}</a>
            </td>
        </tr>

        <tr>
            <td align="center">
                <!-- BULLETPROOF BUTTON -->
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="center" style="padding-top: 25px;" class="padding">
                            <table border="0" cellspacing="0" cellpadding="0" class="mobile-button-container">
                                <tr>
                                    <td align="center" style="border-radius: 10px;" bgcolor="#c1c1c0">
                                        <a
                                            href="{{config('app.frontend_url')}}/verify-email?token={{ $hash }}&redirect={{ $redirect }}"
                                            target="_blank"
                                            style="border-radius: 10px; font-family: Prozak, sans-serif; font-weight: 700; font-size: 2em; letter-spacing: 4px; color: #3a3a39; text-decoration: none; padding: 5px 25px; border: 1px solid #c1c1c0; display: inline-block;"
                                            class="mobile-button">
                                            YUP, IT'S ME
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection

@section('footer')
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left"
                style="padding: 20px 0 50px 0; font-size: 16px; line-height: 25px; font-family: 'Atkinson Hyperlegible', sans-serif; color: #666666;"
                class="padding-copy">If you have any questions, feel free to reach out to our <a
                    href="mailto:hello@artfora.com">partner success team</a> – we're lighting quick at replying.
            </td>
        </tr>
    </table>
@endsection
