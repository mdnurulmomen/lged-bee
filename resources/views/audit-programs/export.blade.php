<table class="table table-bordered" width="100%">
    <thead class="thead-light">
        <tr style="text-align: center;">
            <th>Area</th>
            <th>Category</th>
            <th>Control Objective</th>
            <th>Test Procedure</th>
            <th>Notes</th>
            <th>Done By</th>
            <th>W/P Ref.</th>
        </tr>
    </thead>

    <tbody>
    @forelse($sectorAreaPrograms as $sectorAreaProgram)
        <tr style="text-align: center;">
            <td rowspan="{{ count($sectorAreaProgram['procedures']) }}">
                {{ ucfirst($sectorAreaProgram['area_index']) }}
            </td>
            <td rowspan="{{ count($sectorAreaProgram['procedures']) }}"> {{ ucfirst($sectorAreaProgram['category']) }} </td>
            <td rowspan="{{ count($sectorAreaProgram['procedures']) }}"> {{ ucfirst($sectorAreaProgram['control_objective']) }} </td>

            @foreach ($sectorAreaProgram['procedures'] as $key => $sectorAreaProgramProcedure)
                <td>{{ ucfirst($sectorAreaProgramProcedure['test_procedure']) }}</td>
                <td>{{ ucfirst($sectorAreaProgramProcedure['note']) }}</td>
                <td>{{ ucfirst($sectorAreaProgramProcedure['done_by']) }}</td>
                <td>{{ ucfirst($sectorAreaProgramProcedure['reference']) }}</td>
                </tr><tr>
            @endforeach

        </tr>
    @empty
        <tr style="text-align: center;">
            <td colspan="7" class="datatable-cell text-center"><span>Nothing Found</span></td>
        </tr>
    @endforelse
    </tbody>
</table>
