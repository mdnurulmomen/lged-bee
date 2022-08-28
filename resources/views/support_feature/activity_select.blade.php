@foreach($activities as $activitiy)
    <option value="{{$activitiy->id}}">{{$activitiy->title_bn}}</option>
@endforeach
