<div class="col-md-12">
    <div style="display: none" class="alert alert-success">
       Status Update Successfully
    </div>
    <div style="display: none" class="alert alert-danger">
        Not Update
    </div>
</div>
<div class="col-md-12">

    @if($annual_plan_main)
    <p>Annual Plan Approval: <input data-id="{{$annual_plan_main->id}}" id="annual_plan_approval"
                                    type="checkbox"
                                    @if($annual_plan_main->approval_status == 'approved') checked @endif></p>
    @endif

    <table class="table table-bordered" width="100%">
        <thead class="thead-light">
        <tr class="bg-hover-warning">
            <th width="5%" class="text-center">
                Sl
            </th>

            <th width="15%" class="text-center">
                Plane No
            </th>

            <th width="20%" class="text-center">
                Office Order
            </th>

            <th width="20%" class="text-center">
                Remove Update Lock
            </th>

            <th width="20%" class="text-center">
                Plan Delete
            </th>

            <th width="20%" class="text-center">
                Plan Team Delete
            </th>
        </tr>
        </thead>

        <tbody>
        @forelse($audit_plans as $plan)
            @if(!empty($office_orders))
                @php
                $office_order = isset($office_orders[$plan->id]) ? $office_orders[$plan->id] : null;
                $office_order_info =  $office_order ? explode('-',$office_order) : null;
                @endphp
            @endif
            <tr class="text-center">
                <td class="text-center">
                    {{enTobn($loop->iteration)}}
                </td>
                <td class="text-left">
                    প্ল্যান-{{enTobn($plan->id)}}
{{--                    প্ল্যান-{{enTobn($plan->plan_no)}}--}}
                    @if($office_order_info)
                        <span class="label  @if($office_order_info && $office_order_info[1] == 'approved') label-success @else label-warning @endif">{{$office_order_info[1]}}</span>
                    @else
                        <span class="label label-danger label-warning">No Office Order</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($office_order_info)
                    <input class="office_order_status" type="checkbox"
                           value="{{$office_order_info[0]}}"
                           @if($office_order_info[1] == 'approved') checked @endif>
                    @endif
                </td>
                <td class="text-center">
                    @if($plan->edit_time_start)
                        <p>Now Updating By : {{$plan->edit_user_details}} <a style="cursor: pointer" onclick="planDelete('{{$plan->id}}','update_lock')">
                                <span class="glyphicon glyphicon-trash text-danger"></span>
                            </a></p>

                    @endif
                </td>
                <td class="text-center">
                    @if(!$office_order_info)
                     <button onclick="planDelete('{{$plan->id}}','plan')" type="button" class="btn btn-danger">Delete</button>
                    @endif
                </td>
                <td class="text-center">
                    <span class="label label-warning">{{$plan->audit_teams->count()}} Team</span>
                    @if(!$office_order_info)
                        @if($plan->audit_teams->count() > 0)
                            <button onclick="planDelete('{{$plan->id}}','team')" type="button" class="btn btn-danger">Delete</button>
                        @endif
                    @endif
                </td>
            </tr>
        @empty
            <tr data-row="0" class="datatable-row" style="left: 0px;">
                <td colspan="5" class="datatable-cell text-center"><span>Nothing Found</span></td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<script>
    $("#annual_plan_approval").click(function (){
        if (confirm("Do you want to change status") == false) {
            $('#search_btn').click();
            return;
        }

        var annual_plan_main_id = $(this).attr('data-id');
        var status = $(this).prop("checked") == true ? 'approved' : 'draft';
        var directorate_id = $('#directorate_id').val();
        $.ajax({
            url: "{{ url('annual-plan-approval-status') }}",
            type: 'post',
            dataType: "json",
            data: {
                _token: CSRF_TOKEN,
                annual_plan_main_id: annual_plan_main_id,
                status: status,
                directorate_id: directorate_id,
            },
            success: function (data) {
                console.log(data.status);
                if (data.status == 'success') {
                    $('.alert-success').show();
                    $('#search_btn').click();
                }else{
                    $('.alert-danger').show()
                }

            }
        });
    });

    $(".office_order_status").click(function (){
        if (confirm("Do you want to change status") == false) {
            $('#search_btn').click();
            return;
        }
        var office_order_id = $(this).val();
        var status = $(this).prop("checked") == true ? 'approved' : 'draft';
        var directorate_id = $('#directorate_id').val();
        $.ajax({
            url: "{{ url('office-order-approval-status') }}",
            type: 'post',
            dataType: "json",
            data: {
                _token: CSRF_TOKEN,
                office_order_id: office_order_id,
                status: status,
                directorate_id: directorate_id,
            },
            success: function (data) {
                if (data.status == 'success') {
                    $('.alert-success').show();
                    $('#search_btn').click();
                }else{
                    $('#msg_error').show()
                }

            }
        });
    });

    function planDelete(id,plan_type){
        if (confirm("Do you want to delete") == false) {
            return;
        }
        var directorate_id = $('#directorate_id').val();
        $.ajax({
            url: "{{ url('audit-plan-delete') }}",
            type: 'post',
            dataType: "json",
            data: {
                _token: CSRF_TOKEN,
                plan_id: id,
                plan_type: plan_type,
                directorate_id: directorate_id,
            },
            success: function (data) {
                if (data.status == 'success') {
                    $('.alert-success').show()
                    $('#search_btn').click();
                }else{
                    $('.alert-danger').show()
                }

            }
        });
    }
</script>
