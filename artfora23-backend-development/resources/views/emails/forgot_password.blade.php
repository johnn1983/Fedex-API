@extends('layouts.mail')

@section('content')
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left"
                style="padding: 0 0 20px 0; font-size: 16px; line-height: 25px; font-family: 'Atkinson Hyperlegible', sans-serif; color: #c1c1c0;"
                class="padding-copy"
            >
                You have requested to reset your password.
            </td>
        </tr>

        <tr>
            <td align="left"
                style="padding: 0 0 20px 0; font-size: 16px; line-height: 25px; font-family: 'Atkinson Hyperlegible', sans-serif; color: #c1c1c0;"
                class="padding-copy"
            >
                Please click the button to reset your password.
            </td>
        </tr>

        <tr>
            <td align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="center" style="padding-top: 25px;" class="padding">
                            <table border="0" cellspacing="0" cellpadding="0" class="mobile-button-container">
                                <tr>
                                    <td align="center" style="border-radius: 10px;" bgcolor="#c1c1c0">
                                        <a
                                                href="{{config('app.frontend_url')}}/enter-new-password?token={{$hash}}"
                                                target="_blank"
                                                style="border-radius: 10px; font-family: Prozak, sans-serif; font-weight: 700; font-size: 2em; letter-spacing: 4px; color: #3a3a39; text-decoration: none; padding: 5px 25px; border: 1px solid #c1c1c0; display: inline-block;"
                                                class="mobile-button">
                                            RESET PASSWORD
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