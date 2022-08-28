<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Amms Support</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


</head>
<body>
    <div class="container">
        <div class="row">
            <h2 class="text-center">Amms 2.0 Support</h2>
        </div>
        <div class="row">
            <div class="col-md-3">
                <select autocomplete="off" class="form-control" id="directorate_id">
                    <option value="">--select directorate--</option>
                    @foreach($directorates as $directorate)
                        <option value="{{$directorate->office_id}}">{{$directorate->office_name_bn}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select autocomplete="off" class="form-control" id="fiscal_year_id">
                    <option value="">--select fiscal year--</option>
                    @foreach($fiscal_years as $fiscal_year)
                        <option value="{{$fiscal_year->id}}">{{$fiscal_year->description}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select autocomplete="off" class="form-control" id="activity_id">
                    <option value="">--select activity--</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" id="search_btn" class="btn btn-primary">Search</button>
            </div>
        </div>
        <br>
        <div class="row load_data">

        </div>
    </div>
</body>

<script>
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $("#fiscal_year_id").change(function (){
        var fiscal_year_id = $(this).val();
        $.ajax({
                url: "{{ url('get-fiscal-year-wise-activity') }}",
                type: 'post',
                dataType: "html",
                data: {
                    _token: CSRF_TOKEN,
                    fiscal_year_id: fiscal_year_id
                },
                success: function (data) {
                    $('#activity_id').html(data);
                }
            });
    });

    $("#search_btn").click(function (){
        var directorate_id = $('#directorate_id').val();
        var fiscal_year_id = $('#fiscal_year_id').val();
        var activity_id = $('#activity_id').val();
        $.ajax({
            url: "{{ url('get-audit-plan-data') }}",
            type: 'post',
            dataType: "html",
            data: {
                _token: CSRF_TOKEN,
                directorate_id: directorate_id,
                fiscal_year_id: fiscal_year_id,
                activity_id: activity_id,
            },
            success: function (data) {
                $('.load_data').html(data);
            }
        });
    });
</script>

</html>
