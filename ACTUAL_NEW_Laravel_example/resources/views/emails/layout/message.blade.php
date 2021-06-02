@extends('emails.layout.layout')

@section('header')
<tr>
    <td class="header" align="center">

    </td>
</tr>
@endsection
@section('content', '')
@section('footer')
<tr>
    <td>
        <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td class="content-cell" align="center">
                    © {{ date('Y') }}  @lang('All rights reserved.')
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection
