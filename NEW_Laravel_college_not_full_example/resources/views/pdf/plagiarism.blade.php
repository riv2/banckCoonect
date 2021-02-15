@include('pdf._head')
        <style>
            body { font-size: 100%; }

            table {
                border-collapse: collapse;
            }

            table tbody td {
                border: 1px solid;
                padding: 5px;
            }

            thead:before, thead:after { display: none; }
            tbody:before, tbody:after { display: none; }
        </style>

        <div style="text-align: center">
            <h3>@lang('Reference')</h3>
            <h4>@lang('About the results of the verification of written work for plagiarism')</h4>
        </div>

        <table style="margin: 30px 0; width: 100%;">
            @foreach($fields as $field_name => $field_value)
                <tr>
                    <td>{{ $field_name }}</td>
                    <td>{{ $field_value }}</td>
                </tr>
            @endforeach
        </table>

        <div style="margin-top: 30px; text-align: center;">
            @lang('created by miras.app') ({{ $text_id }})
        </div>
        <div style="margin-top: 40px; text-align: center;">
            @lang("The originality of the document is verified through the «eTXT-Antiplagiarism»")
        </div>
    </body>
</html>
